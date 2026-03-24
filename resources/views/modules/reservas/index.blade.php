@extends('layouts.main')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Gestión de Reservas</h2>
    <div class="d-flex gap-2">
            <a href="{{ route('reservas_individual.create') }}" class="btn btn-primary fw-bold">
                <i class="bi bi-plus-lg"></i> Reserva Individual
            </a>
            <a href="{{ route('reservas_grupal.create') }}" class="btn btn-success fw-bold">
                <i class="bi bi-people"></i> Reserva Grupal
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }} 
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Cliente Titular</th>
                        <th>Destino</th>
                        <th>Tipo Viaje</th>
                        <th>Fecha Viaje</th>
                        <th>Precio Total</th>
                        <th>Estado Reserva </th>
                        <th>Pago </th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservas as $res)
                    <tr>
                        <td class="fw-bold">{{ $res->id }}</td>
                        <td class="fw-bold">{{ $res->codigo_reserva }}</td>
                        
                        <td>{{ $res->nombres }} {{ $res->apellidos }}</td>
                        <td><span class="badge bg-secondary">{{ $res->pais }}</span></td>
                        <td><span class="text-capitalize">{{ $res->tipo }}</span></td>
                        
                        <td>{{ \Carbon\Carbon::parse($res->fecha_viaje)->format('d/m/Y') }}</td>
                        <td class="text-success fw-bold">€{{ number_format($res->precio_total_viaje, 2) }}</td>
                        <td><span class="text-capitalize">{{ $res->estado }}</span></td>
                        
                        <td>
                            @if($res->estado_pago == 'pagado') 
                                <span class="badge bg-success">Completado</span>
                            @elseif($res->estado_pago == 'parcial') 
                                <span class="badge bg-warning text-dark">Parcial:  {{ $res->total_depositado }}</span>
                            @else 
                                <span class="badge bg-danger">Pendiente</span> 
                            @endif
                        </td>
                        <td class="text-end"> {{-- Alineado a la derecha como en tu imagen --}}
                            <div class="d-flex justify-content-end gap-2">
                                
                                {{-- CASO 1: Si es GRUPAL, mostramos botón DESGLOSE (Negro/Gris oscuro) --}}
                                @if($res->tipo === 'grupal')
                                    <a href="#" 
                                    class="btn btn-sm px-3 rounded-pill text-black" 
                                    style="background-color: #2D2D2D; border: 1px solid #444;">
                                        Desglose
                                    </a>
                                @else
                                    {{-- CASO 2: Si es INDIVIDUAL, mostramos botón VER (Gris transparente/Borde) --}}
                                    <a href="#" 
                                    class="btn btn-sm px-3 rounded-pill text-black-50  text-black" 
                                    style="background-color: #9498f6; border: 1px solid #0b2dda;">
                                        Ver
                                    </a>
                                @endif

                                {{-- CASO 3: Botón COBRAR (Solo si es individual y falta pago) --}}
                                {{-- Nota: Según tu imagen, los grupos no muestran "Cobrar" aquí sino en el desglose --}}
                                @if($res->tipo !== 'grupal' && $res->estado_pago !== 'pagado')
                                    <a href="#" 
                                    class="btn btn-sm px-3 rounded-pill text-success fw-bold" 
                                    style="background-color: rgba(25, 135, 84, 0.1); border: 1px solid #198754;">
                                        Cobrar
                                    </a>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            No hay reservas registradas en el sistema.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3">
        {{ $reservas->links() }}
    </div>
</div>
@endsection