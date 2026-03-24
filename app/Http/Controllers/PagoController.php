<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PagoService;
use App\Models\Reserva;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    protected $pagoService;

    public function __construct(PagoService $pagoService)
    {
        $this->pagoService = $pagoService;
    }

    public function index(Request $request)
    {
        $filtros = [
            'estado' => $request->input('estado', 'todos'),
            'metodo' => $request->input('metodo', 'todos'),
        ];

        $metricas = $this->pagoService->getMetricasGenerales();
        $reservas = $this->pagoService->getListaReservas($filtros);

        return view('modules.pagos.index', compact('metricas', 'reservas', 'filtros'));
    }

    public function showGrupoDetails($reservaId)
    {
        $desglose = $this->pagoService->getDesgloseGrupal($reservaId);
        
        return response()->json([
            'success' => true,
            'data' => $desglose
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'reserva_id' => 'required|exists:reservas,id',
            'monto_depositado' => 'required|numeric|min:1',
            'metodo_pago' => 'required|string',
            'cliente_id' => 'nullable|exists:clientes,id'
        ]);

        $reserva = Reserva::findOrFail($request->reserva_id);
        
        $datos = $request->only(['reserva_id', 'monto_depositado', 'metodo_pago', 'referencia', 'cliente_id']);
        $datos['user_id'] = Auth::id() ?? 1; // Fallback for dev

        // Si no envía cliente_id (por ejemplo pago de reserva individual general), sacamos de la reserva
        if (empty($datos['cliente_id'])) {
            $datos['cliente_id'] = $reserva->cliente_id;
        }

        $this->pagoService->registrarPago($datos);

        return redirect()->route('pagos')->with('success', 'Pago registrado correctamente.');
    }
}
