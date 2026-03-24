<?php

namespace App\Http\Controllers;
use App\Models\Cliente;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Exception;
class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titulo = "clientes";
        $clientes=Cliente::all();
        return view('modules.clientes.index',compact('titulo','clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $titulo = "crear clientes";
        return view('modules.clientes.create',compact('titulo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $cliente=new Cliente();
            $cliente->nombres=$request->nombres;
            $cliente->apellidos=$request->apellidos;
            $cliente->email=$request->email;
            $cliente->telefono=$request->telefono;
            $cliente->documento=$request->documento;
            $cliente->estado=$request->estado;
            $cliente->save();
            return to_route('clientes')->with('success','Cliente agregado éxitosamente');
        }catch(Exception $e){
            return to_route('clientes')->with('error','No se pudo agregar el cliente'.$e->getMessage());
        }
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
        $titulo ="editar disciplinas";
        $clientes=Cliente::find($id);
        return view('modules.clientes.edit',compact('clientes','titulo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $usuario=Cliente::find($id);
            $usuario->nombres=$request->nombres;
            $usuario->apellidos=$request->apellidos;
            $usuario->email=$request->email;
            $usuario->telefono=$request->telefono;
            $usuario->documento=$request->documento;
            $usuario->estado=$request->estado;
            $usuario->save();
            return to_route('clientes')->with('sucess','se ha editado correctamente');
        }catch(Exception $e){
            return to_route('clientes')->with('error','No se pudo editar',$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $cliente=Cliente::find($id);
            $cliente->delete();
            return to_route('clientes')->with('sucess','El cliente se ha eliminado correctamente');
        }catch(Exception $e){
            return to_route('clientes')->with('error','No se ha podido eliminar al cliente'.$e->getMessage());
        }
    }
}
