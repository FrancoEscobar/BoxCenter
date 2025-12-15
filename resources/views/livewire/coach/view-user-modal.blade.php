<div>
    @if($mostrarModal && $usuario)
        {{-- Overlay del modal --}}
        <div class="modal-overlay" wire:click="cerrarModal"></div>

        {{-- Modal --}}
        <div class="user-modal">
            {{-- Header del modal --}}
            <div class="user-modal-header">
                <div class="d-flex align-items-center gap-3">
                    <img 
                        src="{{ $usuario->foto_perfil ?? asset('images/default-avatar.png') }}" 
                        alt="Foto de {{ $usuario->name }}" 
                        class="user-modal-avatar"
                    >
                    <div class="flex-grow-1">
                        <h5 class="mb-1 fw-bold">{{ $usuario->name }} {{ $usuario->apellido }}</h5>
                        <span class="badge {{ $usuario->role->nombre === 'coach' ? 'bg-primary' : 'bg-success' }}">
                            {{ ucfirst($usuario->role->nombre) }}
                        </span>
                    </div>
                </div>
                <button type="button" wire:click="cerrarModal" class="btn-close"></button>
            </div>

            {{-- Contenido del modal --}}
            <div class="user-modal-body">
                {{-- Información básica --}}
                <div class="info-section">
                    <h6 class="section-title"><i class="bi bi-person-vcard me-2"></i>Información de contacto</h6>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $usuario->email }}</span>
                        </div>
                        @if($usuario->telefono)
                            <div class="info-item">
                                <span class="info-label">Teléfono</span>
                                <span class="info-value">{{ $usuario->telefono }}</span>
                            </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label">Miembro desde</span>
                            <span class="info-value">{{ $usuario->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                @if($usuario->role->nombre === 'atleta')
                    {{-- Estado de membresía --}}
                    <div class="info-section">
                        <h6 class="section-title"><i class="bi bi-credit-card me-2"></i>Membresía</h6>
                        <div class="membership-status">
                            <span class="badge bg-{{ $colorMembresia }} mb-2" style="font-size: 0.9rem;">
                                {{ $estadoMembresia }}
                            </span>
                            @if($membresia)
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label">Plan</span>
                                        <span class="info-value">{{ $membresia->plan->nombre ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Fecha de inicio</span>
                                        <span class="info-value">{{ Carbon\Carbon::parse($membresia->fecha_inicio)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Fecha de vencimiento</span>
                                        <span class="info-value">{{ Carbon\Carbon::parse($membresia->fecha_vencimiento)->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted small mb-0">El atleta no tiene una membresía activa</p>
                            @endif
                        </div>
                    </div>

                    {{-- Estadísticas --}}
                    <div class="info-section">
                        <h6 class="section-title"><i class="bi bi-graph-up me-2"></i>Estadísticas</h6>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-value">{{ $estadisticas['total_asistencias'] ?? 0 }}</div>
                                <div class="stat-label">Clases totales</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">{{ $estadisticas['asistencias_mes'] ?? 0 }}</div>
                                <div class="stat-label">Este mes</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">{{ $estadisticas['clases_reservadas'] ?? 0 }}</div>
                                <div class="stat-label">Reservadas</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value text-truncate" style="font-size: 0.9rem;">{{ $estadisticas['ultima_asistencia'] ?? 'Nunca' }}</div>
                                <div class="stat-label">Última asistencia</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer del modal --}}
            <div class="user-modal-footer">
                <button type="button" wire:click="cerrarModal" class="btn btn-secondary">Cerrar</button>
            </div>
        </div>
    @endif

    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            backdrop-filter: blur(2px);
        }

        .user-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 1051;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .user-modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e1e5eb;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .user-modal-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .user-modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .user-modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e1e5eb;
            display: flex;
            justify-content: flex-end;
            background: #f8f9fa;
        }

        .info-section {
            margin-bottom: 1.5rem;
        }

        .info-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 0.25rem;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 0.95rem;
            color: #212529;
            font-weight: 500;
        }

        .membership-status {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            color: white;
        }

        .stat-card:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card:nth-child(4) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.75rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 576px) {
            .user-modal {
                width: 95%;
                max-height: 95vh;
            }

            .user-modal-avatar {
                width: 60px;
                height: 60px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</div>
