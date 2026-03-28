<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\Reserva;
use App\Services\PagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    public function __construct(protected PagoService $pagoService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*
        $reservas=DB::table('reservas as r')
            ->select(
                'r.id',
                'r.codigo_reserva',
                'r.tipo',
                'c.nombres',
                'c.apellidos',
                'd.pais',
                'r.fecha_viaje',
                'r.precio_total_viaje',
                'r.estado',
                'r.estado_pago'
            )
            ->join('clientes as c','r.cliente_id','=','c.id')
            ->join('destinos as d','r.destino_id','=','d.id')
            ->orderBy('r.id', 'desc')
            ->paginate(10);*/
        
        $titulo='Reservas';
        $reservas = DB::table('reservas as r')
        ->select(
            'r.id',
            'r.codigo_reserva',
            'r.tipo',
            'c.nombres',
            'c.apellidos',
            'd.pais',
            'r.fecha_viaje',
            'r.precio_total_viaje',
            'r.estado',
            'r.estado_pago',
            DB::raw('COALESCE(SUM(p.monto_depositado),0) as total_depositado')
        )
        ->join('clientes as c','r.cliente_id','=','c.id')
        ->join('destinos as d','r.destino_id','=','d.id')
        ->leftJoin('pagos as p','r.id','=','p.reserva_id') // 👈 clave
        ->groupBy(
            'r.id',
            'r.codigo_reserva',
            'r.tipo',
            'c.nombres',
            'c.apellidos',
            'd.pais',
            'r.fecha_viaje',
            'r.precio_total_viaje',
            'r.estado',
            'r.estado_pago'
        )
        ->orderBy('r.id', 'desc')
        ->paginate(10);
        return view('modules.reservas.index', compact('reservas','titulo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $reserva = Reserva::findOrFail($id);

        if (DB::table('pagos')->where('reserva_id', $reserva->id)->exists()) {
            $msg = 'No se puede eliminar la reserva: existen pagos registrados. Anule los pagos primero desde el módulo de Pagos.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->route('reservas')->with('error', $msg);
        }

        DB::transaction(function () use ($reserva) {
            DB::table('reservas_grupos')->where('reserva_id', $reserva->id)->delete();
            $reserva->delete();
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Reserva eliminada correctamente.']);
        }

        return redirect()->route('reservas')->with('success', 'Reserva eliminada correctamente.');
    }

    public function detalleJson(string $id)
    {
        $reserva = Reserva::with(['cliente', 'destino', 'user', 'reservaGrupo.grupo', 'pago'])->findOrFail($id);

        $totalPagado = (float) $reserva->pago->sum('monto_depositado');
        $pagosActivos = $reserva->pago->count();

        $titularNombre = $reserva->cliente
            ? trim($reserva->cliente->nombres.' '.$reserva->cliente->apellidos)
            : '';

        $grupoNombre = null;
        if ($reserva->tipo === 'grupal' && $reserva->reservaGrupo && $reserva->reservaGrupo->grupo) {
            $grupoNombre = $reserva->reservaGrupo->grupo->nombre_grupo;
        }

        $destinos = Destino::query()->orderBy('pais')->get(['id', 'pais']);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                 => $reserva->id,
                'codigo_reserva'     => $reserva->codigo_reserva,
                'tipo'               => $reserva->tipo,
                'fecha_creacion'     => $reserva->created_at?->format('d/m/Y H:i'),
                'fecha_reserva'      => $reserva->fecha_reserva,
                'fecha_viaje'        => $reserva->fecha_viaje,
                'precio_total_viaje' => (float) $reserva->precio_total_viaje,
                'estado'             => $reserva->estado,
                'estado_pago'        => $reserva->estado_pago,
                'total_depositado'   => $totalPagado,
                'pagos_activos'      => $pagosActivos,
                'cliente_id'         => $reserva->cliente_id,
                'titular'            => [
                    'nombres'   => $reserva->cliente->nombres ?? '',
                    'apellidos' => $reserva->cliente->apellidos ?? '',
                    'email'     => $reserva->cliente->email ?? '',
                    'telefono'  => $reserva->cliente->telefono ?? '',
                    'nombre_completo' => $titularNombre,
                ],
                'destino'            => [
                    'id'   => $reserva->destino->id ?? null,
                    'pais' => $reserva->destino->pais ?? '',
                ],
                'grupo_nombre'       => $grupoNombre,
                'itinerario_resumen' => ($reserva->destino ? 'Destino: '.$reserva->destino->pais.'. Salida: '.\Carbon\Carbon::parse($reserva->fecha_viaje)->format('d/m/Y') : ''),
                'destinos_opciones'  => $destinos,
            ],
        ]);
    }

    public function update(Request $request, string $id)
    {
        $reserva = Reserva::findOrFail($id);

        $request->validate([
            'fecha_viaje'        => 'required|date',
            'fecha_reserva'      => 'required|date',
            'precio_total_viaje' => 'required|numeric|min:0',
            'destino_id'         => 'required|exists:destinos,id',
            'estado'             => 'required|in:confirmada,pendiente,cancelada',
        ]);

        $reserva->fill($request->only([
            'fecha_viaje',
            'fecha_reserva',
            'precio_total_viaje',
            'destino_id',
            'estado',
        ]));
        $reserva->save();

        $this->pagoService->sincronizarEstadoPagoReserva($reserva->id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Reserva actualizada.']);
        }

        return redirect()->route('reservas')->with('success', 'Reserva actualizada correctamente.');
    }
}
