@extends('layouts.main')

@section('titulo', $titulo)

@section('content')
<main id="main" class="main">

  <section class="section">
    <!-- HEADER -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h5 class="mb-1">Destinos Turísticos</h5>
          <p class="text-muted mb-0">Gestiona los paquetes y destinos disponibles</p>
        </div>

        <a href="{{ route('destinos.create') }}" class="btn btn-primary">
          <i class="fa-solid fa-circle-plus me-2"></i> Nuevo Destino
        </a>
      </div>

    <div class="card p-2">
      <!-- La barra de buscar -->
      <div class="mb-2 mt-2">
        <input type="text" class="form-control form-control-sm" placeholder="Buscar destinos...">
      </div>
    </div>
      <!-- GRID DE CARDS -->
    <div class="row">
      @foreach ($destinos as $destino)
      <div class="col-lg-4 col-md-6 mb-4 mt-4">

        <div class="card border-0 shadow-sm h-100" style="border-radius:15px; overflow:hidden;">

          <!-- IMAGEN -->
          <div class="position-relative">
            
            @if(Str::startsWith($destino->imagen_dest, 'http'))
              <img src="{{ $destino->imagen_dest }}" class="w-100" style="height:220px; object-fit:cover;">
            @else
              <img src="{{ asset('storage/'.$destino->imagen_dest) }}" class="w-100" style="height:220px; object-fit:cover;">
            @endif

            <!-- DIAS -->
            <span class="badge bg-light text-dark position-absolute bottom-0 start-0 m-2">
              {{ $destino->dias_dest }} días
            </span>

            <!-- BOTONES -->
            <div class="position-absolute top-0 end-0 m-2 d-flex gap-2">
              <a href="{{ route('destinos.edit', $destino->id) }}" class="btn btn-light btn-sm">
                ✏️
              </a>

              <form action="{{ route('destinos.destroy', $destino->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-light btn-sm"
                  onclick="return confirm('¿Eliminar?')">
                  🗑️
                </button>
              </form>
            </div>

          </div>

          <!-- CONTENIDO -->
          <div class="card-body">

            <h5 class="fw-bold">
              {{ $destino->nombre_dest }}
            </h5>

            <p class="text-muted small">
              {{ Str::limit($destino->descripcion_dest, 100) }}
            </p>

            <!-- CUPOS -->
            <div class="d-flex align-items-center justify-content-between mb-2">
              <small class="text-muted">
                Cupos: {{ $destino->cupos_dest }}
              </small>

              <div style="width:100px; height:6px; background:#eee; border-radius:5px;">
                <div style="width:60%; height:6px; background:#0d6efd; border-radius:5px;"></div>
              </div>
            </div>

            <!-- PAQUETE -->
            <div class="mb-3">
              @php
                $paquetes = json_decode($destino->paquete_dest, true);
              @endphp

              @if(is_array($paquetes))
                @foreach($paquetes as $item)
                  @if($item)
                    <span class="badge bg-light text-dark me-1 mb-1">
                      {{ $item }}
                    </span>
                  @endif
                @endforeach
              @endif
            </div>

            <hr>

            <!-- PRECIO -->
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <small class="text-muted">Desde</small>
                <h5 class="fw-bold mb-0">${{ $destino->precio_dest }}</h5>
              </div>

              <a href="#" class="btn btn-primary btn-sm">
                Crear Reserva
              </a>
            </div>

          </div>

        </div>

      </div>
      @endforeach

    </div>
  </section>

</main>
@endsection