<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use Illuminate\Http\Request;

class DestinoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titulo='Destinos';
        $destinos = Destino::all();
        return view('modules.destinos.index',compact('titulo','destinos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $titulo = "crear destinos";
        return view('modules.destinos.create',compact('titulo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //subir imagen desde URL o desde archivo
        $rutaImagen = null;

        if ($request->hasFile('imagen_file')) {
            $rutaImagen = $request->file('imagen_file')->store('destinos', 'public');
        } elseif ($request->imagen_url) {
            $rutaImagen = $request->imagen_url;
        }

        try {
            $destino = new Destino();

            $destino->nombre_dest = $request->nombre_dest;
            $destino->pais_dest = $request->pais_dest;
            $destino->dias_dest = $request->dias_dest;
            $destino->descripcion_dest = $request->descripcion_dest;
            $destino->cupos_dest = $request->cupos_dest;
            $paquetes = [];
            if ($request->has('paquete_dest')) {
                $paquetes = array_values(array_filter($request->paquete_dest, function ($item) {
                    return !is_null($item) && trim($item) !== '';
                }));
            }
            $destino->paquete_dest = json_encode($paquetes);
            $destino->precio_dest = $request->precio_dest;
            $destino->imagen_dest = $rutaImagen;

            $destino->save();

            return to_route('destinos')->with('success', 'Destino agregado éxitosamente');

        } catch (\Exception $e) {
            return to_route('destinos')->with('error', 'No se pudo agregar el destino ' . $e->getMessage());
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
        $titulo ="editar Destino";
        $destinos=Destino::find($id);
        return view('modules.destinos.edit',compact('destinos','titulo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $destino = Destino::find($id);
            //para la imagen, si se sube un nueva imagen, se guarda esa, si no, se mantiene la imagen nueva
            $rutaImagen = $destino->imagen_dest;

            if ($request->hasFile('imagen_file')) {
                $rutaImagen = $request->file('imagen_file')->store('destinos', 'public');
            } elseif ($request->imagen_url) {
                $rutaImagen = $request->imagen_url;
            }

            // para el paquete
            $paquetes = is_array($request->paquete_dest)
                ? array_values(array_filter($request->paquete_dest))
                : [];

            $destino->paquete_dest = json_encode($paquetes);
            $destino->nombre_dest = $request->nombre_dest;
            $destino->pais_dest = $request->pais_dest;
            $destino->dias_dest = $request->dias_dest;
            $destino->descripcion_dest = $request->descripcion_dest;
            $destino->cupos_dest = $request->cupos_dest;
            $destino->precio_dest = $request->precio_dest;
            $destino->imagen_dest = $rutaImagen;

            $destino->save();

            return to_route('destinos')->with('success', 'Destino actualizado éxitosamente');

        } catch (Exception $e) {
            return to_route('destinos')->with('error', 'No se pudo actualizar el destino ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $destino=Destino::find($id);
            $destino->delete();
            return to_route('destinos')->with('success','El destino se ha eliminado correctamente');
        }catch(Exception $e){
            return to_route('destinos')->with('error','No se ha podido eliminar el destino'.$e->getMessage());
        }
    }
}
