@extends('layouts.main')

@section('titulo', 'Editar Destino')

@section('content')
<main id="main" class="main">

    <!-- HEADER -->
    <div class="pagetitle mb-4">
        <h1 class="fw-bold">Editar Destino</h1>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('destinos') }}">Destinos</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <form action="{{ route('destinos.update', $destinos->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!--  Informacion del destino -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">

                    <h5 class="fw-semibold mb-3">Información del destino</h5>

                    <div class="row">

                        <div class="col-12 col-sm-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre_dest" class="form-control"
                                value="{{ $destinos->nombre_dest }}" required>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label class="form-label">País</label>
                            <input type="text" name="pais_dest" class="form-control"
                                value="{{ $destinos->pais_dest }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion_dest" class="form-control" rows="3" required>{{ $destinos->descripcion_dest }}</textarea>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label class="form-label">Días</label>
                                <input type="number" name="dias_dest" class="form-control"
                                    value="{{ $destinos->dias_dest }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Cupos</label>
                                <input type="number" name="cupos_dest" class="form-control"
                                    value="{{ $destinos->cupos_dest }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Precio</label>
                                <input type="text" name="precio_dest" class="form-control"
                                    value="{{ $destinos->precio_dest }}" required>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- IMAGEN -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">

                    <h5 class="fw-semibold mb-3">Imagen actual</h5>

                    @if(Str::startsWith($destinos->imagen_dest, 'http'))
                        <img src="{{ $destinos->imagen_dest }}" width="200" class="mb-3 rounded">
                    @else
                        <img src="{{ asset('storage/'.$destinos->imagen_dest) }}" width="200" class="mb-3 rounded">
                    @endif

                    <div class="mb-3">
                        <label>URL nueva (opcional)</label>
                        <input type="text" name="imagen_url" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>O subir nueva imagen</label>
                        <input type="file" name="imagen_file" class="form-control">
                    </div>

                </div>
            </div>

            <!-- PAQUETE -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">

                    <h5 class="fw-semibold mb-3">Paquete</h5>

                    @php
                        $paquetes = json_decode($destinos->paquete_dest, true);
                    @endphp

                    @if(is_array($paquetes))
                        @foreach($paquetes as $item)
                            @if($item)
                            <div class="mb-2">
                                <input 
                                    type="text" 
                                    name="paquete_dest[]" 
                                    class="form-control"
                                    value="{{ $item }}">
                            </div>
                            @endif
                        @endforeach
                    @endif

                    <!-- para otro item -->
                    <div class="mb-2">
                        <input 
                            type="text" 
                            name="paquete_dest[]" 
                            class="form-control"
                            placeholder="Agregar nuevo opcion al paquete">
                    </div>

                    <small class="text-muted">
                        Puedes editar o agregar nuevos elementos
                    </small>

                </div>
            </div>

            <!-- BOTONES -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('destinos') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">Actualizar</button>
            </div>

        </form>
    </section>

</main>
@endsection
