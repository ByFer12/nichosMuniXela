@extends('./Layouts.landing')

@section('title', 'Gestión de Solicitudes Pendientes')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Solicitudes</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Solicitudes Pendientes</li>
    </ol>



    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-inbox me-1"></i> Solicitudes Pendientes y En Proceso
        </div>
        <div class="card-body">
            {{-- Filtros (Opcional) --}}
            <form method="GET" action="{{ route('admin.solicitudes.index') }}" class="row gx-2 gy-2 align-items-center mb-4">
                 <div class="col-auto">
                    <select name="tipo_solicitud" class="form-select form-select-sm">
                        <option value="">-- Tipo Solicitud --</option>
                        <option value="boleta" {{ request('tipo_solicitud') == 'boleta' ? 'selected' : '' }}>Boleta</option>
                        <option value="exhumacion" {{ request('tipo_solicitud') == 'exhumacion' ? 'selected' : '' }}>Exhumación</option>
                         <option value="otro" {{ request('tipo_solicitud') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
                 <div class="col-auto">
                    <input type="number" name="search_contrato" class="form-control form-control-sm" placeholder="ID Contrato..." value="{{ request('search_contrato') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary btn-sm">Filtrar</button>
                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID Sol.</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Solicitante</th>
                            <th>Contrato / Nicho</th>
                            <th>Obs. Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                        <tr>
                            <td>{{ $solicitud->id }}</td>
                            <td>{{ $solicitud->fecha_solicitud->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($solicitud->tipo_solicitud == 'boleta') <i class="fas fa-file-invoice-dollar text-success"></i> Boleta
                                @elseif($solicitud->tipo_solicitud == 'exhumacion') <i class="fas fa-skull-crossbones text-warning"></i> Exhumación
                                @else <i class="fas fa-question-circle"></i> Otro @endif
                            </td>
                            <td>{{ $solicitud->solicitante->nombre ?? 'N/A' }} <br><small class="text-muted">({{ $solicitud->solicitante->email ?? '?' }})</small></td>
                            <td>
                                @if($solicitud->contrato)
                                    C: {{ $solicitud->contrato->id }}
                                    @if($solicitud->contrato->nicho)
                                        <br><small>N: {{ $solicitud->contrato->nicho->codigo ?? '?' }}</small>
                                    @endif
                                @else N/A @endif
                            </td>
                            <td style="max-width: 200px; white-space: normal;">{{ $solicitud->observaciones_usuario ?? '-' }}</td>
                            <td>
                                {{-- Botones/Formularios de Acción --}}
                                @if($solicitud->estado == 'pendiente')
                                    @if($solicitud->tipo_solicitud == 'boleta')
                                        {{-- Procesar Boleta --}}
                                        <button type="button" class="btn btn-success btn-sm mb-1 w-100" data-bs-toggle="modal" data-bs-target="#processBoletaModal{{ $solicitud->id }}">
                                            <i class="fas fa-check me-1"></i> Generar Boleta
                                        </button>
                                    @elseif($solicitud->tipo_solicitud == 'exhumacion')
                                         {{-- Aprobar Exhumación --}}
                                        <button type="button" class="btn btn-success btn-sm mb-1 w-100" data-bs-toggle="modal" data-bs-target="#approveExhumacionModal{{ $solicitud->id }}">
                                            <i class="fas fa-check me-1"></i> Aprobar Exh.
                                        </button>
                                    @endif

                                    {{-- Rechazar (Común a ambos) --}}
                                    <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $solicitud->id }}">
                                        <i class="fas fa-times me-1"></i> Rechazar
                                    </button>
                                @else
                                     <span class="badge bg-info">{{ ucfirst($solicitud->estado) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay solicitudes pendientes o en proceso.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($solicitudes->hasPages()) <div class="mt-3">{{ $solicitudes->links() }}</div> @endif
        </div>
    </div>

    {{-- Modales para las acciones --}}
    @foreach($solicitudes as $solicitud)
        @if($solicitud->estado == 'pendiente')
            {{-- Modal Procesar Boleta --}}
            @if($solicitud->tipo_solicitud == 'boleta')
            <div class="modal fade" id="processBoletaModal{{ $solicitud->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.solicitudes.processBoleta', $solicitud) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header"> <h5 class="modal-title">Generar Boleta (Solicitud #{{ $solicitud->id }})</h5> <button type="button" class="btn-close" data-bs-dismiss="modal"></button> </div>
                            <div class="modal-body">
                                <p>Se generará una nueva boleta de pago (Q600.00) para el contrato #{{ $solicitud->contrato_id }}.</p>
                                <div class="mb-3">
                                    <label for="obs_admin_boleta_{{ $solicitud->id }}" class="form-label">Observaciones (Opcional):</label>
                                    <textarea class="form-control" id="obs_admin_boleta_{{ $solicitud->id }}" name="observaciones_admin" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer"> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button> <button type="submit" class="btn btn-success">Confirmar y Generar</button> </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

             {{-- Modal Aprobar Exhumación --}}
            @if($solicitud->tipo_solicitud == 'exhumacion')
            <div class="modal fade" id="approveExhumacionModal{{ $solicitud->id }}" tabindex="-1" aria-hidden="true">
                 <div class="modal-dialog">
                    <form action="{{ route('admin.solicitudes.approveExhumacion', $solicitud) }}" method="POST">
                         @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header"> <h5 class="modal-title">Aprobar Exhumación (Solicitud #{{ $solicitud->id }})</h5> <button type="button" class="btn-close" data-bs-dismiss="modal"></button> </div>
                            <div class="modal-body">
                                <p>Se marcará la solicitud de exhumación para el contrato #{{ $solicitud->contrato_id }} como aprobada.</p>
                                 <p class="small text-muted">Nota: La programación y registro final de la exhumación se realiza en la sección "Gestión de Exhumaciones".</p>
                                 <div class="mb-3">
                                    <label for="obs_admin_exh_approve_{{ $solicitud->id }}" class="form-label">Observaciones (Opcional):</label>
                                    <textarea class="form-control" id="obs_admin_exh_approve_{{ $solicitud->id }}" name="observaciones_admin" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer"> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button> <button type="submit" class="btn btn-success">Confirmar Aprobación</button> </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Modal Rechazar (Común) --}}
            <div class="modal fade" id="rejectModal{{ $solicitud->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                     <form action="{{ route('admin.solicitudes.reject', $solicitud) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header"> <h5 class="modal-title">Rechazar Solicitud #{{ $solicitud->id }}</h5> <button type="button" class="btn-close" data-bs-dismiss="modal"></button> </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="obs_admin_reject_{{ $solicitud->id }}" class="form-label">Motivo del Rechazo <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="obs_admin_reject_{{ $solicitud->id }}" name="observaciones_admin" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer"> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button> <button type="submit" class="btn btn-danger">Confirmar Rechazo</button> </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach

</div> {{-- Fin container --}}
@endsection