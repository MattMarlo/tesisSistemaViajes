@extends('layouts.main')

@section('titulo', 'Nuevo Destino')

@section('content')
<main id="main" class="main">

    <!-- HEADER -->
    <div class="pagetitle mb-4">
        <h1 class="fw-bold">Nuevo Destino</h1>
        <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('destinos') }}">Destinos</a></li>
            <li class="breadcrumb-item active">NuevoDestino</li>
        </ol>
        </nav>
    </div>

    <!-- CONTENIDO -->
    <section class="section">
        <form action="{{ route('destinos.store') }}" id="form_disciplina" method="post" enctype="multipart/form-data">
            @csrf

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">
                        Información del destino
                    </h5>

                    <div class="row">
                        <!-- NOMBRES -->
                        <div class="col-12 col-sm-6">
                        <label for="nombre_dest" class="form-label">Nombre del Destino</label>
                        <input required
                            class="form-control"
                            placeholder="Ej: Cusco - Machu Picchu"
                            type="text" 
                            name="nombre_dest"
                            id="nombre_dest">
                        </div>

                        <!-- PAIS -->
                        <div class="col-12 col-sm-6">
                        <label for="pais_dest" class="form-label">País</label>
                        <input required
                            class="form-control"
                            placeholder="Ingrese el país del destino"
                            type="text" 
                            name="pais_dest"
                            id="pais_dest">
                        </div>

                        <!-- DESCRIPCION -->
                        <div class="col-12">
                        <label for="descripcion_dest" class="form-label">Descripción</label>
                        <textarea required
                            class="form-control"
                            placeholder="Describe el destino, actividades incluidas, lugares a visitar..."
                            name="descripcion_dest"
                            id="descripcion_dest"
                            rows="3"></textarea>
                        </div>

                        <div class="row mt-3">
                            <!-- Duracion dias -->
                            <div class="col-md-4">
                            <label for="dias_dest" class="form-label">Duración (días)</label>
                            <input required
                                class="form-control"
                                placeholder="5"
                                type="number"
                                name="dias_dest"
                                id="dias_dest">
                            </div>

                            <!-- CUPOS -->
                            <div class="col-md-4">
                            <label for="cupos_dest" class="form-label">Cupos Totales</label>
                            <input required
                                class="form-control"
                                placeholder="20"
                                type="number"
                                name="cupos_dest"
                                id="cupos_dest">
                            </div>

                            <!-- PRECIO -->
                            <div class="col-md-4">
                            <label for="precio_dest" class="form-label">Precio (USD)</label>
                            <input required
                                class="form-control"
                                placeholder="1850"
                                type="text"
                                name="precio_dest"
                                id="precio_dest">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                    
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">

                    <h5 class="fw-semibold mb-3">Imagen del Destino</h5>

                    <!-- para URL -->
                    <div class="mb-3">
                    <label class="form-label">URL de la Imagen</label>
                    <input type="text" 
                        name="imagen_url" 
                        class="form-control"
                        placeholder="https://ejemplo.com/imagen.jpg">
                    </div>

                    <!-- para ARCHIVO -->
                    <div class="mb-3">
                    <label class="form-label">O subir imagen desde tu PC</label>
                    <input type="file" 
                        name="imagen_file" 
                        class="form-control"
                        accept="image/*">
                    </div>

                    <small class="text-muted">
                    Puedes usar una URL o subir una imagen desde tu computadora
                    </small>

                </div>
            </div>
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">¿Qué incluye el paquete?</h5>

                    <!-- para las opciones -->
                    <div class="mb-2">
                        <input
                            class="form-control"
                            placeholder="Ej: Transporte"
                            type="text"
                            name="paquete_dest[]">
                    </div>

                    <div class="mb-2">
                        <input
                            class="form-control"
                            placeholder="Ej: Hospedaje"
                            type="text"
                            name="paquete_dest[]">
                    </div>

                    <div class="mb-2">
                        <input
                            class="form-control"
                            placeholder="Ej: Guía"
                            type="text"
                            name="paquete_dest[]">
                    </div>

                    <div class="mb-2">
                        <input
                            class="form-control"
                            placeholder="Agregar otro item"
                            type="text"
                            name="paquete_dest[]">
                    </div>

                    <small class="text-muted">
                        Puedes llenar varios campos (los vacíos no se guardan)
                    </small>
                </div>
            </div>
            <!-- BOTONES -->
            <div class="d-flex flex-column flex-sm-row gap-2 mt-4 justify-content-end">
                <a href="{{ route('destinos') }}" class="btn btn-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    Guardar destino
                </button>
            </div>
        </form>
    </section>
</main>
@endsection
