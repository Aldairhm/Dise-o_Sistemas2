<x-app title="Detalle del Ticket #{{ $ticket->codigo_ticket }} | AXStore">
    <div class="mx-auto max-w-5xl space-y-6 pb-12">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ Auth::user()->isSuperAdmin() ? route('superadmin.tickets') : route('ayuda.tickets.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 mb-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Listado de Dudas</span>
                </a>
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xl sm:text-2xl font-black text-slate-800">
                        #{{ $ticket->codigo_ticket }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-black 
                        {{ $ticket->estado === 'Escalada' ? 'bg-amber-100 text-amber-800' : ($ticket->estado === 'Resuelta' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $ticket->estado }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold 
                        {{ $ticket->prioridad === 'Urgente' ? 'bg-rose-100 text-rose-800' : ($ticket->prioridad === 'Alta' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                        {{ $ticket->prioridad }}
                    </span>
                </div>
                <h1 class="text-base sm:text-lg font-bold text-slate-700 mt-1">
                    {{ $ticket->titulo ?? 'Consulta sobre ' . $ticket->modulo }}
                </h1>
            </div>

            <!-- Acciones según Rol -->
            <div class="flex flex-wrap items-center gap-2">
                @if(Auth::user()->isAdmin() && $ticket->estado !== 'Escalada' && $ticket->estado !== 'Resuelta' && $ticket->estado !== 'Cerrada')
                    <button type="button" onclick="document.getElementById('modalEscalar').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white transition-colors shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                        <span>Escalar a Super Admin</span>
                    </button>
                @endif

                @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin() || (Auth::user()->isVendedor() && in_array($ticket->estado, ['Resuelta', 'Cerrada'])))
                    <button type="button" onclick="document.getElementById('modalCambiarEstado').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-white transition-colors shadow-sm flex items-center gap-1.5">
                        <i class="fas fa-sliders"></i>
                        <span>Cambiar Estado</span>
                    </button>
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

            <div class="p-6 space-y-4 max-h-[600px] overflow-y-auto">
                @foreach($mensajes as $msg)
                    @php
                        $esMio = $msg->id_usuario === Auth::id();
                        $esInterno = $msg->tipo_mensaje === 'interno_admin';
                    @endphp
                    <div class="flex flex-col {{ $esMio ? 'items-end' : 'items-start' }}">
                        <div class="max-w-xl rounded-2xl p-4 text-xs shadow-sm {{ $esInterno ? 'bg-amber-50 border border-amber-200 text-amber-950' : ($esMio ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-800') }}">
                            <div class="flex items-center justify-between gap-3 mb-1 text-[10px] {{ $esMio && !$esInterno ? 'text-blue-100' : 'text-slate-400' }}">
                                <span class="font-bold {{ $esInterno ? 'text-amber-800' : '' }}">
                                    {{ $msg->nombre_usuario }} ({{ ucfirst($msg->rol_usuario) }})
                                    @if($esInterno)
                                        <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-200 text-amber-900 font-black">
                                            NOTA INTERNA
                                        </span>
                                    @endif
                                </span>
                                <span class="font-mono">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                            </div>
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

            <!-- Formulario de Respuesta -->
            @if($ticket->estado !== 'Cerrada' || Auth::user()->isSuperAdmin())
                <form method="POST" action="{{ route('ayuda.tickets.responder', $ticket->id) }}" class="p-5 bg-slate-50 border-t border-slate-200 space-y-3">
                    @csrf
                    <div>
                        <textarea name="mensaje" required rows="3" placeholder="Escribe tu respuesta o información adicional..." class="w-full p-3.5 rounded-2xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-blue-500 bg-white"></textarea>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
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
            @else
                <div class="p-4 bg-slate-100 text-center text-xs text-slate-500 font-medium">
                    Este ticket se encuentra cerrado. Puedes reabrirlo cambiando su estado si el problema persiste.
                </div>
            @endif
        </div>

        <!-- Trazabilidad de Historial de Estados -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-3">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider">Historial de Transiciones de Estado</h3>
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($historial as $h)
                    <div class="py-2.5 flex items-center justify-between gap-4">
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

            <form method="POST" action="{{ route('ayuda.tickets.escalar', $ticket->id) }}" class="p-6 space-y-4 text-xs">
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

            <form method="POST" action="{{ route('ayuda.tickets.estado', $ticket->id) }}" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nuevo Estado <span class="text-rose-500">*</span></label>
                    <select name="estado" required class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs">
                        @foreach(\App\Models\SoporteTicket::ESTADOS as $st)
                            <option value="{{ $st }}" {{ $ticket->estado === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Resolución o Motivo del Cambio</label>
                    <textarea name="resolucion" rows="3" placeholder="Detalla la solución brindada o razón del cambio de estado..." class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-500 text-xs">{{ $ticket->resolucion }}</textarea>
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
</x-app>

