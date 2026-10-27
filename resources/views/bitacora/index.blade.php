<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bitácora de Movimientos — AXStore</title>

    <!-- Dark mode init -->
    <script>
        (function() {
            var t = localStorage.getItem('ax_theme') || 'system';
            if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/home.css', 'resources/css/app.css', 'resources/js/app.js'])

    <style>
        .badge-CREAR { background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .badge-EDITAR { background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-ELIMINAR { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-CAMBIO_ESTADO { background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-LOGIN { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-LOGOUT { background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1; }
        .badge-CANCELAR { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-TRANSFERIR { background-color: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4; }
        .badge-DEVOLUCION { background-color: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .badge-APROBAR { background-color: #ecfdf5; color: #059669; border: 1px solid #6ee7b7; }
        .badge-RESTAURAR { background-color: #fefce8; color: #a16207; border: 1px solid #fef08a; }
        .badge-REORDENAR, .badge-EXPORTAR { background-color: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased font-['Plus_Jakarta_Sans',sans-serif] bg-slate-50 flex flex-col">

    <!-- ─── HEADER ─── -->
    <header class="bg-white/85 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 transition-all duration-300">
        @include('componentsHome.header')
    </header>

    <!-- ─── CONTENIDO PRINCIPAL ─── -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- ENCABEZADO DE SECCIÓN -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-teal-600 uppercase tracking-widest mb-1.5">
                    <i class="fas fa-shield-halved"></i>
                    <span>Seguridad & Trazabilidad</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    Bitácora de Movimientos
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Registro cronológico y detallado de todas las operaciones realizadas en el sistema.
                </p>
            </div>

            <!-- Botón de recarga rápida -->
            <div class="flex items-center gap-3">
                <a href="{{ route('bitacora.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:border-slate-300 rounded-xl text-xs font-bold text-slate-700 shadow-sm hover:shadow transition-all">
                    <i class="fas fa-rotate text-teal-600"></i>
                    <span>Actualizar Registros</span>
                </a>
            </div>
        </div>

        <!-- TARJETAS DE RESUMEN KPI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total de eventos -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-list-check"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Eventos</span>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight mt-0.5">
                        {{ number_format($registros->total()) }}
                    </h3>
                </div>
            </div>

            <!-- Eventos de hoy -->
            @php
                $hoyCount = \App\Models\Bitacora::whereDate('created_at', today())->count();
            @endphp
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Movimientos Hoy</span>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight mt-0.5">
                        {{ number_format($hoyCount) }}
                    </h3>
                </div>
            </div>

            <!-- Módulos auditados -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-cubes"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Módulos Activos</span>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight mt-0.5">
                        {{ $modulos->count() }}
                    </h3>
                </div>
            </div>

            <!-- Logins registrados -->
            @php
                $loginsCount = \App\Models\Bitacora::where('accion', 'LOGIN')->count();
            @endphp
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-right-to-bracket"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sesiones / Logins</span>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight mt-0.5">
                        {{ number_format($loginsCount) }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- FORMULARIO DE FILTROS AVANZADOS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between gap-4 mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-filter text-teal-600 text-sm"></i>
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Filtros de Búsqueda</h2>
                </div>
                @if(request()->anyFilled(['modulo', 'accion', 'usuario_id', 'desde', 'hasta', 'q']))
                    <a href="{{ route('bitacora.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1.5 transition-colors">
                        <i class="fas fa-times-circle"></i>
                        <span>Limpiar Filtros</span>
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('bitacora.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Búsqueda por texto libre -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Búsqueda Rápida</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                            <i class="fas fa-search text-xs"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Descripción, usuario, IP..." class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                    </div>
                </div>

                <!-- Módulo -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Módulo</label>
                    <select name="modulo" class="w-full py-2 px-3 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none bg-white">
                        <option value="">Todos los módulos</option>
                        @foreach($modulos as $mod)
                            <option value="{{ $mod }}" {{ request('modulo') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Acción -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Acción</label>
                    <select name="accion" class="w-full py-2 px-3 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none bg-white">
                        <option value="">Todas las acciones</option>
                        @foreach($acciones as $acc)
                            <option value="{{ $acc }}" {{ request('accion') == $acc ? 'selected' : '' }}>{{ $acc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Usuario -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Usuario</label>
                    <select name="usuario_id" class="w-full py-2 px-3 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none bg-white">
                        <option value="">Todos los usuarios</option>
                        @foreach($usuarios as $usr)
                            <option value="{{ $usr->id }}" {{ request('usuario_id') == $usr->id ? 'selected' : '' }}>{{ $usr->nombre_real }} ({{ $usr->username }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Botón de enviar -->
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2 px-4 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm hover:shadow flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-filter"></i>
                        <span>Filtrar</span>
                    </button>
                </div>

                <!-- Rango de Fechas -->
                <div class="lg:col-span-3">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Fecha Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="w-full py-2 px-3 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Fecha Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="w-full py-2 px-3 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all outline-none">
                </div>
            </form>
        </div>

        <!-- TABLA DE MOVIMIENTOS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Fecha / Hora</th>
                            <th class="py-3.5 px-4">Usuario</th>
                            <th class="py-3.5 px-4">Módulo</th>
                            <th class="py-3.5 px-4">Acción</th>
                            <th class="py-3.5 px-4">Descripción del Movimiento</th>
                            <th class="py-3.5 px-4">IP / Origen</th>
                            <th class="py-3.5 px-4 text-center">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($registros as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Fecha -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $item->created_at->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400 font-medium">{{ $item->created_at->format('h:i:s A') }} ({{ $item->created_at->diffForHumans() }})</div>
                            </td>

                            <!-- Usuario -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($item->usuario)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-teal-500 to-blue-500 text-white font-bold text-[11px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($item->usuario->nombre_real, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 leading-tight">{{ $item->usuario->nombre_real }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium">@<span>{{ $item->usuario->username }}</span></div>
                                        </div>
                                    </div>
                                @elseif($item->username)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 font-bold text-[11px] flex items-center justify-center shrink-0">
                                            <i class="fas fa-user-secret text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-700">{{ $item->username }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium">Externo / Intento</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-slate-400 italic font-medium">
                                        <i class="fas fa-robot text-xs"></i>
                                        <span>Sistema</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Módulo -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                @php
                                    $modIcon = match($item->modulo) {
                                        'Ventas'        => 'fa-cash-register text-blue-500',
                                        'Compras'       => 'fa-cart-plus text-emerald-500',
                                        'Productos'     => 'fa-shopping-bag text-indigo-500',
                                        'Categorías'    => 'fa-folder text-purple-500',
                                        'Usuarios'      => 'fa-users text-amber-500',
                                        'Autenticación' => 'fa-key text-slate-600',
                                        'Bodega'        => 'fa-warehouse text-cyan-500',
                                        'Proveedores'   => 'fa-truck text-orange-500',
                                        'Devoluciones'  => 'fa-rotate-left text-rose-500',
                                        'Comisiones'    => 'fa-hand-holding-dollar text-emerald-600',
                                        default         => 'fa-circle-dot text-slate-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-[11px] font-semibold text-slate-700">
                                    <i class="fas {{ $modIcon }}"></i>
                                    <span>{{ $item->modulo }}</span>
                                </span>
                            </td>

                            <!-- Acción -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider badge-{{ $item->accion }}">
                                    <i class="fas {{ $item->icon }} text-[9px]"></i>
                                    <span>{{ $item->accion }}</span>
                                </span>
                            </td>

                            <!-- Descripción -->
                            <td class="py-3 px-4">
                                <p class="text-xs text-slate-800 font-medium line-clamp-2" title="{{ $item->descripcion }}">
                                    {{ $item->descripcion }}
                                </p>
                            </td>

                            <!-- IP -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-mono text-[11px] text-slate-600 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-network-wired text-slate-400 text-[10px]"></i>
                                    <span>{{ $item->ip_address ?: '127.0.0.1' }}</span>
                                </div>
                            </td>

                            <!-- Detalle -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($item->datos_anteriores || $item->datos_nuevos)
                                    <button type="button" 
                                            onclick='verDetalle(@json($item))'
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-bold transition-all cursor-pointer">
                                        <i class="fas fa-code-compare"></i>
                                        <span>Diff</span>
                                    </button>
                                @else
                                    <button type="button"
                                            onclick='verDetalleSimple(@json($item))'
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-all cursor-pointer">
                                        <i class="fas fa-eye"></i>
                                        <span>Ver</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center mx-auto text-2xl mb-3">
                                    <i class="fas fa-clipboard-question"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-600">No se encontraron movimientos registrados</h4>
                                <p class="text-xs text-slate-400 mt-1">Prueba cambiando o eliminando los filtros aplicados arriba.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($registros->hasPages())
            <div class="px-6 py-4 bg-slate-50/70 border-t border-slate-200/80">
                {{ $registros->links() }}
            </div>
            @endif
        </div>

    </main>

    <!-- ─── MODAL DETALLE / DIFF ─── -->
    <div id="modalDetalle" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center text-sm">
                        <i class="fas fa-file-lines"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800" id="modalTitulo">Detalle del Registro</h3>
                        <p class="text-[11px] text-slate-400" id="modalSubtitulo">Trazabilidad de la acción</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModal()" class="w-8 h-8 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto space-y-4">
                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-xl text-xs">
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Usuario</span>
                        <span class="font-bold text-slate-800" id="modalUsuario">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Fecha y Hora</span>
                        <span class="font-bold text-slate-800" id="modalFecha">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Módulo / Acción</span>
                        <span class="font-bold text-slate-800" id="modalModuloAccion">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold text-[10px] uppercase">Dirección IP</span>
                        <span class="font-bold text-slate-800 font-mono" id="modalIp">-</span>
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Descripción</label>
                    <div class="p-3 bg-slate-100/70 rounded-xl text-xs text-slate-800 font-medium" id="modalDescripcion">
                        -
                    </div>
                </div>

                <!-- Comparador Diff (Antes / Después) -->
                <div id="diffContainer" class="hidden">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Comparativa de Cambios</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Antes -->
                        <div class="bg-rose-50/50 border border-rose-200 rounded-xl p-3">
                            <div class="text-[11px] font-bold text-rose-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <i class="fas fa-minus-circle"></i>
                                <span>Datos Anteriores</span>
                            </div>
                            <pre class="text-[11px] font-mono text-slate-700 whitespace-pre-wrap overflow-x-auto max-h-48" id="modalAntes">{}</pre>
                        </div>

                        <!-- Después -->
                        <div class="bg-emerald-50/50 border border-emerald-200 rounded-xl p-3">
                            <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <i class="fas fa-plus-circle"></i>
                                <span>Datos Nuevos</span>
                            </div>
                            <pre class="text-[11px] font-mono text-slate-700 whitespace-pre-wrap overflow-x-auto max-h-48" id="modalDespues">{}</pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="cerrarModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition-all cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Script del Modal -->
    <script>
        function verDetalle(item) {
            document.getElementById('modalTitulo').textContent = `Registro #${item.id} — ${item.modulo}`;
            document.getElementById('modalSubtitulo').textContent = `${item.accion} por ${item.nombre_usuario || item.username || 'Sistema'}`;
            document.getElementById('modalUsuario').textContent = `${item.nombre_usuario || item.username || 'Sistema'} (${item.rol_usuario || 'N/A'})`;
            document.getElementById('modalFecha').textContent = new Date(item.created_at).toLocaleString();
            document.getElementById('modalModuloAccion').textContent = `${item.modulo} / ${item.accion}`;
            document.getElementById('modalIp').textContent = item.ip_address || '127.0.0.1';
            document.getElementById('modalDescripcion').textContent = item.descripcion || 'Sin descripción';

            const diffContainer = document.getElementById('diffContainer');
            if (item.datos_anteriores || item.datos_nuevos) {
                diffContainer.classList.remove('hidden');
                document.getElementById('modalAntes').textContent = JSON.stringify(item.datos_anteriores, null, 2) || '(Vacío)';
                document.getElementById('modalDespues').textContent = JSON.stringify(item.datos_nuevos, null, 2) || '(Vacío)';
            } else {
                diffContainer.classList.add('hidden');
            }

            document.getElementById('modalDetalle').classList.remove('hidden');
        }

        function verDetalleSimple(item) {
            verDetalle(item);
        }

        function cerrarModal() {
            document.getElementById('modalDetalle').classList.add('hidden');
        }

        // Cerrar con Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') cerrarModal();
        });
    </script>
</body>
</html>
