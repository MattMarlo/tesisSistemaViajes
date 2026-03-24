<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ReservaController extends Controller
{
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
     
    
}
