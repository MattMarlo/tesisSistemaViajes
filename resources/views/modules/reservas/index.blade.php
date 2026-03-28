@extends('layouts.main')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Gestión de Reservas</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('reservas_individual.create') }}" class="btn fw-bold text-white" style="background-color:#3b82f6;border:none;">
                <i class="bi bi-plus-lg"></i> Reserva Individual
            </a>
            <a href="{{ route('reservas_grupal.create') }}" class="btn fw-bold text-white" style="background-color:#10b981;border:none;">
                <i class="bi bi-people"></i> Reserva Grupal
            </a>
        </div>
    </div>

    @if (session('success') && !session('toast_sync'))
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

    @if(session('toast_sync'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
        <div id="toastSync" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4500">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check2-circle me-1"></i> {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
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
                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                <button type="button"
                                    class="btn btn-sm px-3 rounded-pill text-white fw-semibold"
                                    style="background-color:#3b82f6;border:1px solid #2563eb;"
                                    onclick="abrirModalDetalleReserva({{ $res->id }})">
                                    Ver Detalle
                                </button>

                                @if($res->tipo !== 'grupal' && $res->estado_pago !== 'pagado')
                                    @php
                                        $pendienteRes   = max(0, $res->precio_total_viaje - $res->total_depositado);
                                        $nombreCliente  = $res->nombres . ' ' . $res->apellidos;
                                    @endphp
                                    <button
                                        type="button"
                                        class="btn btn-sm px-3 rounded-pill text-success fw-bold"
                                        style="background-color: rgba(16, 185, 129, 0.12); border: 1px solid #10b981;"
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

{{-- Modal detalle reserva (ERP) --}}
<div class="modal fade" id="modalDetalleReserva" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="detalle_codigo">—</h5>
                    <div class="small text-muted" id="detalle_fecha_creacion"></div>
                    <span id="detalle_badge_estado" class="badge mt-2"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-secondary text-uppercase small mb-3">Cliente titular</h6>
                        <div id="vista_lectura_titular">
                            <p class="mb-1"><strong id="detalle_titular_nombre">—</strong></p>
                            <p class="mb-1 small text-muted" id="detalle_titular_email">—</p>
                            <p class="mb-0 small text-muted" id="detalle_titular_tel">—</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-secondary text-uppercase small mb-3">Detalle del viaje</h6>
                        <div id="vista_lectura_viaje">
                            <p class="mb-1"><span class="badge bg-dark" id="detalle_destino_badge">—</span></p>
                            <p class="mb-1 small"><strong>Reserva:</strong> <span id="detalle_fecha_reserva_txt">—</span></p>
                            <p class="mb-1 small"><strong>Viaje:</strong> <span id="detalle_fecha_viaje_txt">—</span></p>
                            <p class="mb-1 small"><strong>Precio total:</strong> <span class="text-success fw-bold" id="detalle_precio_txt">—</span></p>
                            <p class="mb-0 small text-muted" id="detalle_itinerario">—</p>
                            <p class="mb-0 small mt-2" id="detalle_grupo_linea" style="display:none;"><span class="badge" style="background:#4c1d95;">Grupo</span> <span id="detalle_grupo_nombre"></span></p>
                        </div>
                        <div id="vista_edicion_viaje" class="d-none">
                            <input type="hidden" id="edit_reserva_id">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Destino</label>
                                <select id="edit_destino_id" class="form-select form-select-sm"></select>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label small fw-semibold">Fecha reserva</label>
                                    <input type="date" id="edit_fecha_reserva" class="form-control form-control-sm">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label small fw-semibold">Fecha viaje</label>
                                    <input type="date" id="edit_fecha_viaje" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Precio total (€)</label>
                                <input type="number" step="0.01" id="edit_precio_total" class="form-control form-control-sm">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Estado reserva</label>
                                <select id="edit_estado" class="form-select form-select-sm">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-sm text-white" style="background:#3b82f6;" onclick="guardarEdicionReserva()">Guardar cambios</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelarEdicionReserva()">Cancelar edición</button>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-secondary text-uppercase small">Registrar pago</h6>
                    <span class="small text-muted">Saldo estimado: <strong id="detalle_saldo_pendiente" class="text-danger">—</strong></span>
                </div>
                <form action="{{ route('pagos.store') }}" method="POST" class="border rounded p-3 bg-light">
                    @csrf
                    <input type="hidden" name="redirect_after" value="reservas">
                    <input type="hidden" name="reserva_id" id="registro_reserva_id">
                    <input type="hidden" name="cliente_id" id="registro_cliente_id">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small mb-0">Monto (€)</label>
                            <input type="number" step="0.01" name="monto_depositado" id="registro_monto" class="form-control form-control-sm" required min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-0">Método</label>
                            <select name="metodo_pago" class="form-select form-select-sm" required>
                                <option value="transferencia">Transferencia</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="efectivo">Efectivo</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-0">Referencia</label>
                            <input type="text" name="referencia" class="form-control form-control-sm" placeholder="Opcional">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm text-white w-100 fw-bold" style="background:#10b981;">+ Registrar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn text-white" style="background:#3b82f6;" id="btn_editar_logistica" onclick="activarEdicionReserva()">Editar</button>
                <button type="button" class="btn btn-outline-danger" id="btn_eliminar_reserva" onclick="confirmarEliminarReserva()">Eliminar</button>
                <a href="#" class="btn text-white fw-bold d-none" id="btn_gestionar_pago" style="background:#10b981;">Gestionar pago</a>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Cobro de Reservas (atajo) --}}
<div class="modal fade" id="modalCobrarReserva" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('pagos.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="redirect_after" value="reservas">
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
                <button type="submit" class="btn fw-bold text-white" style="background:#10b981;">Procesar pago</button>
            </div>
        </form>
    </div>
</div>

<script>
    const detalleUrlBase = @json(url('/reservas'));
    let datosDetalleActual = null;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function badgeEstadoReserva(estado) {
        const e = (estado || '').toLowerCase();
        if (e === 'confirmada') return '<span class="badge bg-success">Confirmada</span>';
        if (e === 'cancelada') return '<span class="badge" style="background:#ef4444;">Cancelada</span>';
        return '<span class="badge bg-warning text-dark">Pendiente</span>';
    }

    function abrirModalDetalleReserva(id) {
        fetch(detalleUrlBase + '/' + id + '/detalle', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(json => {
                if (!json.success) throw new Error('Error al cargar');
                datosDetalleActual = json.data;
                poblarModalDetalle(json.data);
                const m = new bootstrap.Modal(document.getElementById('modalDetalleReserva'));
                m.show();
            })
            .catch(() => alert('No se pudo cargar el detalle de la reserva.'));
    }

    function poblarModalDetalle(d) {
        document.getElementById('detalle_codigo').textContent = d.codigo_reserva;
        document.getElementById('detalle_fecha_creacion').textContent = 'Creada: ' + (d.fecha_creacion || '—');
        document.getElementById('detalle_badge_estado').innerHTML = badgeEstadoReserva(d.estado);

        document.getElementById('detalle_titular_nombre').textContent = d.titular.nombre_completo || '—';
        document.getElementById('detalle_titular_email').textContent = d.titular.email || '';
        document.getElementById('detalle_titular_tel').textContent = d.titular.telefono || '';

        document.getElementById('detalle_destino_badge').textContent = d.destino.pais || '—';
        function fmtFecha(v) {
            if (!v) return '—';
            const s = String(v).substring(0, 10);
            const p = s.split('-');
            if (p.length === 3) return p[2] + '/' + p[1] + '/' + p[0];
            return v;
        }
        document.getElementById('detalle_fecha_reserva_txt').textContent = fmtFecha(d.fecha_reserva);
        document.getElementById('detalle_fecha_viaje_txt').textContent = fmtFecha(d.fecha_viaje);
        document.getElementById('detalle_precio_txt').textContent = '€' + Number(d.precio_total_viaje).toFixed(2);
        document.getElementById('detalle_itinerario').textContent = d.itinerario_resumen || '';

        const gl = document.getElementById('detalle_grupo_linea');
        if (d.tipo === 'grupal' && d.grupo_nombre) {
            gl.style.display = 'block';
            document.getElementById('detalle_grupo_nombre').textContent = d.grupo_nombre;
        } else {
            gl.style.display = 'none';
        }

        document.getElementById('edit_reserva_id').value = d.id;
        document.getElementById('registro_reserva_id').value = d.id;
        document.getElementById('registro_cliente_id').value = d.cliente_id || '';

        const pend = Math.max(0, Number(d.precio_total_viaje) - Number(d.total_depositado || 0));
        document.getElementById('detalle_saldo_pendiente').textContent = '€' + pend.toFixed(2);
        document.getElementById('registro_monto').value = pend > 0 ? pend.toFixed(2) : '';

        const sel = document.getElementById('edit_destino_id');
        sel.innerHTML = '';
        (d.destinos_opciones || []).forEach(o => {
            const opt = document.createElement('option');
            opt.value = o.id;
            opt.textContent = o.pais;
            if (d.destino && String(o.id) === String(d.destino.id)) opt.selected = true;
            sel.appendChild(opt);
        });

        document.getElementById('edit_fecha_reserva').value = (d.fecha_reserva || '').substring(0, 10);
        document.getElementById('edit_fecha_viaje').value = (d.fecha_viaje || '').substring(0, 10);
        document.getElementById('edit_precio_total').value = d.precio_total_viaje;
        document.getElementById('edit_estado').value = d.estado || 'pendiente';

        document.getElementById('vista_edicion_viaje').classList.add('d-none');
        document.getElementById('vista_lectura_viaje').classList.remove('d-none');
        document.getElementById('btn_editar_logistica').classList.remove('d-none');

        const btnPago = document.getElementById('btn_gestionar_pago');
        const ep = (d.estado_pago || '').toLowerCase();
        if (ep === 'pendiente' || ep === 'parcial') {
            btnPago.classList.remove('d-none');
            btnPago.href = @json(url('/pagos')) + '?reserva_id=' + d.id + '&abrir_cobro=1';
        } else {
            btnPago.classList.add('d-none');
        }

        const btnDel = document.getElementById('btn_eliminar_reserva');
        if (d.pagos_activos > 0) {
            btnDel.disabled = true;
            btnDel.title = 'Existen pagos vinculados; anule primero en Pagos.';
        } else {
            btnDel.disabled = false;
            btnDel.title = '';
        }
    }

    function activarEdicionReserva() {
        document.getElementById('vista_edicion_viaje').classList.remove('d-none');
        document.getElementById('vista_lectura_viaje').classList.add('d-none');
    }

    function cancelarEdicionReserva() {
        if (datosDetalleActual) poblarModalDetalle(datosDetalleActual);
    }

    function guardarEdicionReserva() {
        const id = document.getElementById('edit_reserva_id').value;
        const body = {
            fecha_reserva: document.getElementById('edit_fecha_reserva').value,
            fecha_viaje: document.getElementById('edit_fecha_viaje').value,
            precio_total_viaje: document.getElementById('edit_precio_total').value,
            destino_id: document.getElementById('edit_destino_id').value,
            estado: document.getElementById('edit_estado').value,
        };
        fetch(detalleUrlBase + '/' + id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify(body),
        })
            .then(r => r.json().then(j => ({ ok: r.ok, j })))
            .then(({ ok, j }) => {
                if (!ok) {
                    alert(j.message || (j.errors ? JSON.stringify(j.errors) : 'No se pudo guardar'));
                    return;
                }
                window.location.reload();
            })
            .catch(() => alert('Error de red al guardar.'));
    }

    function confirmarEliminarReserva() {
        if (!datosDetalleActual) return;
        if (datosDetalleActual.pagos_activos > 0) {
            alert('No se puede eliminar: existen pagos activos. Anule los pagos primero.');
            return;
        }
        if (!confirm('¿Eliminar esta reserva del itinerario?')) return;
        if (!confirm('Esta acción es definitiva. ¿Confirma la eliminación?')) return;

        const id = datosDetalleActual.id;
        fetch(detalleUrlBase + '/' + id, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
        })
            .then(r => r.json().then(j => ({ ok: r.ok, j })))
            .then(({ ok, j }) => {
                if (!ok) {
                    alert(j.message || 'No se pudo eliminar');
                    return;
                }
                window.location.reload();
            })
            .catch(() => alert('Error de red.'));
    }

    function abrirModalCobrarReserva(reservaId, clienteNombre, pendiente) {
        document.getElementById('cobrar_reserva_id').value = reservaId;
        document.getElementById('cobrar_cliente_nombre').value = clienteNombre;
        document.getElementById('cobrar_monto').value = pendiente;
        document.getElementById('cobrar_cliente_id').value = '';

        var modal = new bootstrap.Modal(document.getElementById('modalCobrarReserva'));
        modal.show();
    }

    @if(session('toast_sync') && session('success'))
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('toastSync');
        if (el && typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const t = new bootstrap.Toast(el);
            t.show();
        }
    });
    @endif
</script>

@endsection
