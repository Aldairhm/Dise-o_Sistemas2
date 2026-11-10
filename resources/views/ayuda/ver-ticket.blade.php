<x-app title="Detalle del Ticket #{{ $ticket->codigo_ticket }} | AXStore">
    <div class="mx-auto max-w-5xl min-w-0 space-y-6 pb-12">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ Auth::user()->isSuperAdmin() ? route('superadmin.tickets') : route('ayuda.tickets.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 mb-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Listado de Dudas</span>
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="font-mono text-xl sm:text-2xl font-black text-slate-800">
                        #{{ $ticket->codigo_ticket }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-black 
                        {{ $ticket->estado === 'Escalada' ? 'bg-amber-100 text-amber-800' : ($ticket->estado === 'Resuelta' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $ticket->estado }}
                    </span>
                </div>
                <h1 class="text-base sm:text-lg font-bold text-slate-700 mt-1">
                    {{ $ticket->titulo ?? 'Consulta sobre ' . $ticket->modulo }}
                </h1>
            </div>

            <!-- Acciones según Rol -->
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $esCreador = ($ticket->id_usuario === Auth::id());
                    $puedeGestionar = (Auth::user()->isSuperAdmin() || (Auth::user()->isAdmin() && !$esCreador))
                        && (!in_array($ticket->estado, ['Resuelta', 'Cancelada']) || Auth::user()->isSuperAdmin());
                    $puedeEscalar = app(\App\Services\NotificationService::class)->canEscalateTicket(Auth::user(), $ticket);
                    $puedeCancelarPropio = $esCreador && in_array($ticket->estado, ['Pendiente', 'En proceso']);
                @endphp

                @if($puedeEscalar)
                    <button type="button" onclick="document.getElementById('modalEscalar').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white transition-colors shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                        <span>Escalar a Super Admin</span>
                    </button>
                @endif

                @if($puedeGestionar)
                    <button type="button" onclick="document.getElementById('modalCambiarEstado').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-white transition-colors shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-sliders"></i>
                        <span>Cambiar Estado</span>
                    </button>
                @elseif($puedeCancelarPropio)
                    <button type="button" onclick="document.getElementById('modalCambiarEstado').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition-colors shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-ban"></i>
                        <span>Cancelar Mi Duda</span>
                    </button>
                @else
                    <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                        <i class="fas fa-lock mr-1 text-slate-400"></i> Estado Definitivo
                    </span>
                @endif
            </div>
        </div>

        <!-- Información General y Trazabilidad -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Solicitante</span>
                <span class="font-bold text-slate-800 text-sm">{{ $ticket->nombre_usuario }}</span>
                <span class="text-slate-400 block text-[11px] font-mono">{{ $ticket->usuario->username ?? '—' }} ({{ $ticket->rol_usuario }})</span>
            </div>
            <div>
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Módulo / Tipo</span>
                <span class="font-bold text-slate-800">{{ $ticket->modulo }}</span>
                <span class="text-slate-500 block text-[11px]">{{ $ticket->tipo_consulta }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Nivel Jerárquico</span>
                <span class="font-bold {{ $ticket->nivel_escalamiento === 'super_admin' ? 'text-indigo-600' : 'text-blue-600' }}">
                    {{ $ticket->nivel_escalamiento === 'super_admin' ? 'Super Administrador' : 'Administrador' }}
                </span>
                <span class="text-slate-400 block text-[11px]">Asignado: {{ $ticket->asignado->nombre_real ?? 'Sin asignar' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Fechas Clave</span>
                <span class="text-slate-700 block">Apertura: {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                @if($ticket->fecha_resolucion)
                    <span class="text-emerald-700 font-bold block">Resuelto: {{ $ticket->fecha_resolucion->format('d/m/Y H:i') }}</span>
                @endif
            </div>
        </div>

        @if($ticket->resolucion)
            <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-xs text-emerald-900">
                <span class="font-black text-emerald-950 flex items-center gap-1.5 mb-1">
                    <i class="fas fa-check-double text-emerald-600"></i> Resolución Final:
                </span>
                <p class="leading-relaxed">{{ $ticket->resolucion }}</p>
                @if($ticket->resolvedor)
                    <span class="text-[10px] text-emerald-700 block mt-1">
                        Registrada por: {{ $ticket->resolvedor->nombre_real }} ({{ $ticket->resolvedor->rol }})
                    </span>
                @endif
            </div>
        @endif

        <!-- Hilo de Conversación -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-comments text-slate-500"></i>
                    <h2 class="text-sm font-black text-slate-800">Historial de Conversación y Seguimiento</h2>
                </div>
                <span class="text-xs text-slate-400 font-mono">{{ $mensajes->count() }} mensajes</span>
            </div>

            <div class="p-4 sm:p-6 space-y-4 max-h-[600px] overflow-y-auto">
                @foreach($mensajes as $index => $msg)
                    @php
                        $esMio = $msg->id_usuario === Auth::id();
                        $esInterno = $msg->tipo_mensaje === 'interno_admin';
                        $esPrimerMensaje = ($index === 0);
                    @endphp
                    <div class="flex flex-col {{ $esMio ? 'items-end' : 'items-start' }}">
                        <div class="max-w-full sm:max-w-xl rounded-2xl p-4 text-xs shadow-sm break-words {{ $esInterno ? 'bg-amber-50 border border-amber-200 text-amber-950' : ($esMio ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-800') }}">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-3 mb-1 text-[10px] {{ $esMio && !$esInterno ? 'text-blue-100' : 'text-slate-400' }}">
                                <span class="font-bold {{ $esInterno ? 'text-amber-800' : '' }}">
                                    {{ $msg->nombre_usuario }} ({{ ucfirst($msg->rol_usuario) }})
                                    @if($esInterno)
                                        <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-200 text-amber-900 font-black">
                                            NOTA INTERNA
                                        </span>
                                    @endif
                                    @if($esPrimerMensaje)
                                        <span class="ml-1 px-1.5 py-0.5 rounded {{ $esMio ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }} font-bold">
                                            Ticket Apertura
                                        </span>
                                    @endif
                                </span>
                                <span class="font-mono">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                            </div>

                            @if($esPrimerMensaje)
                                <!-- Tarjeta de Contexto Rápido del Ticket -->
                                <div class="my-2 p-2.5 rounded-xl {{ $esMio ? 'bg-white/10 border border-white/20' : 'bg-white border border-slate-200' }} grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                                    <div>
                                        <span class="{{ $esMio ? 'text-blue-200' : 'text-slate-400' }} block text-[10px] uppercase font-bold">Módulo:</span>
                                        <span class="font-bold {{ $esMio ? 'text-white' : 'text-slate-800' }}">{{ $ticket->modulo }}</span>
                                    </div>
                                    <div>
                                        <span class="{{ $esMio ? 'text-blue-200' : 'text-slate-400' }} block text-[10px] uppercase font-bold">Motivo / Tipo:</span>
                                        <span class="font-bold {{ $esMio ? 'text-white' : 'text-slate-800' }}">{{ $ticket->tipo_consulta }}</span>
                                    </div>
                                </div>
                            @endif

                            <p class="leading-relaxed whitespace-pre-line">{{ $msg->mensaje }}</p>
                            
                            @if($msg->evidencia_url)
                                <div class="mt-3 pt-2 border-t {{ $esMio && !$esInterno ? 'border-blue-500' : 'border-slate-200' }}">
                                    <a href="{{ $msg->evidencia_url }}" target="_blank" class="inline-flex items-center gap-1.5 font-bold underline text-[11px] {{ $esMio && !$esInterno ? 'text-white' : 'text-blue-600' }}">
                                        <i class="fas fa-paperclip"></i> Ver archivo adjunto / evidencia
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Formulario de Respuesta o Aviso de Ticket Cerrado -->
            @if(!in_array($ticket->estado, ['Resuelta', 'Cancelada']))
                <form data-loading-form data-loading-label="Procesando..." method="POST" action="{{ route('ayuda.tickets.responder', $ticket->id) }}" class="p-5 bg-slate-50 border-t border-slate-200 space-y-3">
                    @csrf
                    <div>
                        <textarea name="mensaje" required rows="3" placeholder="Escribe tu respuesta o información adicional..." class="w-full p-3.5 rounded-2xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-blue-500 bg-white"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-4">
                            @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                                <label class="inline-flex items-center gap-2 text-xs text-amber-900 font-bold cursor-pointer">
                                    <input type="checkbox" name="tipo_mensaje" value="interno_admin" class="rounded text-amber-600 focus:ring-0">
                                    <span>Nota interna privada (No visible para el vendedor)</span>
                                </label>
                            @endif
                        </div>

                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors shadow-sm flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>Enviar Respuesta</span>
                        </button>
                    </div>
                </form>
            @elseif($ticket->estado === 'Resuelta')
                <div class="p-5 bg-emerald-50/70 border-t border-emerald-100 flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2.5 text-emerald-900">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                            <i class="fas fa-lock"></i>
                        </span>
                        <div>
                            <span class="font-bold text-sm block">Ticket Resuelto</span>
                            <span class="text-emerald-700 text-[11px]">Esta duda ha sido resuelta satisfactoriamente y su estado se encuentra cerrado de forma definitiva.</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-4 bg-slate-100 text-center text-xs text-slate-500 font-medium border-t border-slate-200">
                    <i class="fas fa-ban mr-1.5 text-slate-400"></i>
                    Este ticket se encuentra cancelado. Puedes reactivarlo cambiando su estado a 'En proceso' si es necesario.
                </div>
            @endif
        </div>

        <!-- Trazabilidad de Historial de Estados -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-3">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider">Historial de Transiciones de Estado</h3>
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($historial as $h)
                    <div class="py-2.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
                        <div class="space-y-0.5">
                            <span class="font-bold text-slate-800">
                                {{ $h->estado_anterior ? "'{$h->estado_anterior}' → " : '' }}'{{ $h->estado_nuevo }}'
                            </span>
                            @if($h->motivo)
                                <p class="text-[11px] text-slate-500">{{ $h->motivo }}</p>
                            @endif
                        </div>
                        <div class="text-right text-[10px] text-slate-400 shrink-0">
                            <span>{{ $h->usuario->nombre_real ?? 'Sistema' }}</span>
                            <span class="block font-mono">{{ $h->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Modal para Escalamiento (Admin -> Super Admin) -->
    <div id="modalEscalar" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-base">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black">Escalar Caso al Super Admin</h3>
                        <p class="text-xs text-slate-400">Pasa el control a nivel superior de gobernanza</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalEscalar').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            <form data-loading-form data-loading-label="Procesando..." method="POST" action="{{ route('ayuda.tickets.escalar', $ticket->id) }}" class="p-6 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Justificación del Escalamiento <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="justificacion" required rows="4" placeholder="Explica detalladamente por qué este caso requiere la intervención o autorización del Super Administrador..." class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-amber-500 text-xs"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalEscalar').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold transition-colors">
                        Confirmar Escalamiento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Cambio de Estado -->
    <div id="modalCambiarEstado" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-slate-800 text-blue-400 flex items-center justify-center text-base">
                        <i class="fas fa-sliders"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black">Actualizar Estado del Ticket</h3>
                        <p class="text-xs text-slate-400">Flujo jerárquico de control</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalCambiarEstado').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            <form data-loading-form data-loading-label="Procesando..." id="formCambiarEstadoTicket" method="POST" action="{{ route('ayuda.tickets.estado', $ticket->id) }}" novalidate class="p-6 space-y-4 text-xs">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nuevo Estado <span class="text-rose-500">*</span></label>
                    <select name="estado" id="selectNuevoEstado" class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs">
                        @if($esCreador && !Auth::user()->isSuperAdmin())
                            <option value="Cancelada">Cancelada (Cancelar mi propia duda)</option>
                        @else
                            @foreach(\App\Models\SoporteTicket::ESTADOS as $st)
                                @if($st === 'Escalada' && $ticket->nivel_escalamiento === 'super_admin')
                                    @continue
                                @endif
                                <option value="{{ $st }}" {{ $ticket->estado === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Resolución o Motivo del Cambio <span class="text-rose-500">*</span></label>
                    <textarea name="resolucion" id="textareaResolucionModal" rows="3" placeholder="Detalla obligatoriamente la solución brindada o razón del cambio de estado..." class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs transition-all resize-none">{{ $ticket->resolucion }}</textarea>
                    <p id="errorResolucionModal" class="hidden text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-1 pl-1">
                        <i class="fas fa-circle-exclamation text-[10px]"></i>
                        <span>Debes ingresar la resolución o motivo del cambio (mínimo 5 caracteres).</span>
                    </p>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalCambiarEstado').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold transition-colors">
                        Guardar Cambio
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formCambiarEstadoTicket');
            const textarea = document.getElementById('textareaResolucionModal');
            const errorMsg = document.getElementById('errorResolucionModal');

            if (form && textarea && errorMsg) {
                textarea.addEventListener('input', function () {
                    if (this.value.trim().length >= 5) {
                        this.classList.remove('border-rose-500', 'bg-rose-50/40', 'ring-1', 'ring-rose-400');
                        this.classList.add('border-slate-200');
                        errorMsg.classList.add('hidden');
                    }
                });

                form.addEventListener('submit', function (e) {
                    const val = textarea.value.trim();
                    if (val.length < 5) {
                        e.preventDefault();
                        textarea.classList.remove('border-slate-200');
                        textarea.classList.add('border-rose-500', 'bg-rose-50/40', 'ring-1', 'ring-rose-400');
                        errorMsg.classList.remove('hidden');
                        textarea.focus();
                    }
                });
            }
        });
    </script>
</x-app>
