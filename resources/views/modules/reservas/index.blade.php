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
                        <th>Estado Reserva</th>
                        <th>Pago</th>
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
                                <span class="badge bg-warning text-dark">Parcial: {{ $res->total_depositado }}</span>
                            @else 
                                <span class="badge bg-danger">Pendiente</span> 
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">

                                {{-- Botón VER o DESGLOSE según tipo --}}
                                @if($res->tipo === 'grupal')
                                    <a href="#" 
                                       class="btn btn-sm px-3 rounded-pill text-white" 
                                       style="background-color: #2D2D2D; border: 1px solid #444;">
                                        Desglose
                                    </a>
                                @else
                                    <a href="#" 
                                       class="btn btn-sm px-3 rounded-pill" 
                                       style="background-color: #9498f6; border: 1px solid #0b2dda; color:#fff;">
                                        Ver
                                    </a>
                                @endif

                                {{-- Botón COBRAR: solo para individuales con saldo pendiente --}}
                                @if($res->tipo !== 'grupal' && $res->estado_pago !== 'pagado')
                                    @php
                                        $pendienteRes   = max(0, $res->precio_total_viaje - $res->total_depositado);
                                        $nombreCliente  = $res->nombres . ' ' . $res->apellidos;
                                    @endphp
                                    <button 
                                        type="button"
                                        class="btn btn-sm px-3 rounded-pill text-success fw-bold" 
                                        style="background-color: rgba(25, 135, 84, 0.1); border: 1px solid #198754;"
                                        onclick="abrirModalCobrarReserva({{ $res->id }}, '{{ addslashes($nombreCliente) }}', {{ $pendienteRes }})">
                                        Cobrar
                                    </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
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

{{-- Modal de Cobro de Reservas --}}
<div class="modal fade" id="modalCobrarReserva" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('pagos.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-secondary fw-semibold">ID Reserva</label>
                    <input type="number" name="reserva_id" id="cobrar_reserva_id" class="form-control" readonly>
                </div>

                <input type="hidden" name="cliente_id" id="cobrar_cliente_id">

                <div class="mb-3">
                    <label class="form-label text-secondary fw-semibold">Cliente</label>
                    <input type="text" id="cobrar_cliente_nombre" class="form-control" disabled>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary fw-semibold">Monto (€)</label>
                        <input type="number" step="0.01" name="monto_depositado" id="cobrar_monto" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary fw-semibold">Método</label>
                        <select name="metodo_pago" class="form-select" required>
                            <option value="transferencia">Transferencia</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="efectivo">Efectivo</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-secondary fw-semibold">Referencia (Opcional)</label>
                    <input type="text" name="referencia" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success fw-bold">Procesar pago</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalCobrarReserva(reservaId, clienteNombre, pendiente) {
        document.getElementById('cobrar_reserva_id').value = reservaId;
        document.getElementById('cobrar_cliente_nombre').value = clienteNombre;
        document.getElementById('cobrar_monto').value = pendiente;
        document.getElementById('cobrar_cliente_id').value = '';

        var modal = new bootstrap.Modal(document.getElementById('modalCobrarReserva'));
        modal.show();
    }
</script>

@endsection