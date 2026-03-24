<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReservaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReservaIndividualController extends Controller
{
    protected $reservaService;
    // Inyectamos el servicio en el constructor
    public function __construct(ReservaService $reservaService)
    {
        $this->reservaService = $reservaService;
    }
    public function create()
    {
        $clientes = DB::table('clientes')->get();
        $destinos = DB::table('destinos')->get();
        $reservas = DB::table('reservas')->get();
        return view('modules.reservas.individual.create', compact('clientes', 'destinos','reservas'));
    }
    
    public function store(Request $request)
    {
        $datos = $request->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'destino_id'         => 'required|exists:destinos,id',
            //'estado'             => 'nullable|in:confirmada,pendiente,cancelada',
            'fecha_reserva'      => 'required|date',
            'fecha_viaje'        => 'required|date',
            'precio_total_viaje' => 'required|numeric|min:0',
            'monto_depositado'   => 'nullable|numeric|min:0',
            'metodo_pago'        => 'nullable|string',
            'fecha_pago'         => 'nullable|date',
        ]);

        $usuario_id = Auth::id();
        if (!$usuario_id) {
            return back()->with('error', 'Debes estar autenticado para crear una reserva.')->withInput();
        }

        $datos['user_id'] = $usuario_id;
        //$datos['estado'] = $datos['estado'] ?? 'pendiente';

        try {
            $codigo = $this->reservaService->guardarIndividual($datos);
            return to_route('reservas')->with('success', 'Reserva creada. Código: ' . $codigo);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear reserva: ' . $e->getMessage())->withInput();
        }
    }
}
