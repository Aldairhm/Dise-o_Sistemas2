<x-app title="Centro de Ayuda | AXStore">
    @php
        $whatsappDestino = env('WHATSAPP_SOPORTE', '50362057666');
        $mensajeWa = urlencode('Hola, tengo una duda sobre el sistema AXStore que no encontré en el módulo de Ayuda.');
        $linkWhatsapp = str_starts_with($whatsappDestino, 'http')
            ? $whatsappDestino
            : "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsappDestino) . "?text={$mensajeWa}";
    @endphp
    <style>@media (min-width: 768px) { .ayuda-modulos { grid-template-columns: minmax(235px, 285px) minmax(0, 1fr); } }</style>
    <div class="mx-auto max-w-6xl space-y-6">
        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver al inicio
            </a>
        </div>
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">AXStore · Centro de Ayuda</p>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800">¿Qué necesitas hacer?</h1>
                <p class="text-xs sm:text-sm text-slate-500">Busca una tarea o elige una guía paso a paso.</p>
            </div>
            <div>
                <a href="{{ route('ayuda.tickets.index') }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 shadow-sm transition-all">
                    <i class="fas fa-ticket text-emerald-600"></i>
                    <span>Control de Dudas y Tickets</span>
                </a>
            </div>
        </header>
        <form action="{{ route('ayuda.index') }}" method="GET" role="search" class="space-y-2"
              x-data="{ busquedaInvalida: false }" :novalidate="true"
              @submit="if (!$refs.busqueda.value.trim()) {
                  $event.preventDefault();
                  busquedaInvalida = true;
                  $refs.busqueda.focus();
              }">
            <div class="flex flex-wrap gap-2">
                <label for="ayuda-busqueda" class="sr-only">Buscar una tarea</label>
                <input id="ayuda-busqueda" name="q" type="search" maxlength="120" value="{{ $query }}"
                       placeholder="Ejemplo: venta, stock o devolución" required x-ref="busqueda"
                       :aria-invalid="busquedaInvalida"
                       :aria-describedby="busquedaInvalida ? 'ayuda-busqueda-error' : null"
                       @input="if ($event.target.value.trim()) busquedaInvalida = false"
                       class="min-w-0 flex-1 rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 aria-invalid:border-rose-500 aria-invalid:focus:border-rose-500 aria-invalid:focus:ring-rose-500/20">
                <button type="submit" class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-bold text-white">Buscar</button>
            </div>
            <p id="ayuda-busqueda-error" x-show="busquedaInvalida" role="alert" style="display: none;"
               class="flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700">
                <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                Escribe una palabra o una tarea para buscar.
            </p>
        </form>

        <div class="grid items-start gap-5 ayuda-modulos">
            <nav aria-label="Módulos de ayuda">
                <details id="ayudaModulosNav" class="group rounded-xl border bg-white shadow-sm overflow-hidden transition-all duration-200" open>
                    <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none bg-slate-50 hover:bg-slate-100 transition-colors list-none">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold uppercase tracking-wide text-slate-800">Módulos</span>
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full bg-slate-200 text-slate-700">
                                {{ count($guides) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <span class="group-open:hidden text-slate-400">Desplegar</span>
                            <span class="hidden group-open:inline text-slate-400">Contraer</span>
                            <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200 group-open:rotate-180"></i>
                        </div>
                    </summary>

                    <div class="p-2 border-t border-slate-100 max-h-[68vh] overflow-y-auto space-y-1">
                        @foreach ($guides as $key => $item)
                            <a href="{{ route('ayuda.index', ['tema' => $key]) }}"
                               @if ($topic === $key) aria-current="page" @endif
                               class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-50 transition-colors {{ $topic === $key ? 'bg-blue-50 text-blue-900 font-semibold border-l-4 border-blue-600' : 'text-slate-700' }}">
                                <span aria-hidden="true" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm"
                                      style="background:{{ $item['icon_bg'] }};color:{{ $item['icon_fg'] }}">
                                    <i class="fas {{ $item['icon'] }}"></i>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-bold truncate leading-tight">{{ $item['module'] }}</span>
                                    <span class="block text-[11px] text-slate-500 truncate leading-tight">{{ $item['description'] }}</span>
                                </span>
                                @if ($topic === $key)
                                    <i class="fas fa-check text-blue-600 text-xs shrink-0"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </details>
            </nav>
            <div>
        @if ($guide)
            <article class="rounded-xl border p-6" style="background:#f8fafc;color:#172334">
                <a href="{{ route('ayuda.index') }}" class="text-sm font-semibold underline" style="color:#1745a3">Volver a las tareas</a>
                <p class="mt-5 text-sm font-bold">{{ $guide['module'] }}</p>
                <h2 class="mt-1 text-2xl font-bold">{{ $topic === 'venta' ? 'Ventas en tienda y por envío' : $guide['title'] }}</h2>
                @php
                    $cases = $guide['cases'];
                    if (in_array($topic, ['inventario', 'venta'], true)) {
                        $cases = [$guide['title'] => $guide] + $cases;
                    }
                @endphp
                @if ($topic === 'devolucion')
                    <p class="mt-3">Antes de empezar, verifica que la venta esté entregada y dentro del plazo de garantía.</p>
                @endif
                @if ($cases)
                    <div class="mt-5 space-y-3">
                        @foreach ($cases as $caseTitle => $case)
                            <details class="rounded-lg border p-4">
                                <summary class="cursor-pointer font-bold">{{ $caseTitle }}</summary>
                <ol class="mt-5 list-decimal space-y-3 pl-6 leading-relaxed">
                    @foreach ($case['steps'] as $step)
                        <li class="pl-1">{{ $step }}</li>
                    @endforeach
                </ol>
                <div class="mt-6 rounded-lg border-l-4 p-4" style="background:#e6f5ef;border-color:#197354">
                    <h3 class="font-bold">Comprueba el resultado</h3>
                    <p class="mt-1">{{ $case['check'] }}</p>
                </div>
                            </details>
                        @endforeach
                    </div>
                @else
                    @php($case = $guide)
                <ol class="mt-5 list-decimal space-y-3 pl-6 leading-relaxed">
                    @foreach ($case['steps'] as $step)
                        <li class="pl-1">{{ $step }}</li>
                    @endforeach
                </ol>
                <div class="mt-6 rounded-lg border-l-4 p-4" style="background:#e6f5ef;border-color:#197354">
                    <h3 class="font-bold">Comprueba el resultado</h3>
                    <p class="mt-1">{{ $case['check'] }}</p>
                </div>
                @endif
                <a href="{{ route($guide['route']) }}" class="mt-5 inline-block rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Abrir módulo</a>
            </article>
        @elseif ($query === '')
            <section class="rounded-xl border p-6" style="background:#f8fafc;color:#172334">
                <h2 class="text-2xl font-bold">Selecciona un módulo</h2>
                <p class="mt-2">Elige un módulo de la lista para consultar su guía paso a paso.</p>
            </section>
        @else
            <section class="space-y-3" aria-label="Tareas">
                @if ($query !== '')
                    <h2 class="text-lg font-bold">Resultados para «{{ $query }}»</h2>
                @endif
                @forelse ($query === '' ? $guides : $results as $key => $item)
                    <a href="{{ route('ayuda.index', ['tema' => $key]) }}"
                       class="block rounded-xl border p-4 hover:underline" style="background:#f8fafc;color:#172334">
                        <span class="block text-xs font-bold uppercase tracking-wide" style="color:#1745a3">{{ $item['module'] }}</span>
                        <span class="mt-1 block text-lg font-bold">{{ $item['title'] }} <span aria-hidden="true">→</span></span>
                    </a>
                @empty
                    <p>No encontramos una guía. Prueba con el nombre de una tarea.</p>
                @endforelse
                @if ($query !== '')
                    <a href="{{ route('ayuda.index') }}" class="inline-block font-semibold underline">Ver todas las tareas</a>
                @endif
            </section>
        @endif
            </div>
        </div>

        <!-- ─── SECCIÓN DE SOPORTE POR WHATSAPP ─── -->
        <section class="rounded-xl border p-5 flex flex-col sm:flex-row items-center justify-between gap-4" style="background:#f8fafc;color:#172334">
            <div class="flex items-center gap-3.5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-emerald-600 bg-emerald-50 border border-emerald-200/60 text-lg">
                    <i class="fab fa-whatsapp"></i>
                </span>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">¿No encuentras lo que buscas o tienes otra duda?</h3>
                    <p class="text-xs text-slate-500">Comunícate con soporte para resolver inquietudes puntuales sobre el sistema.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
                <a href="{{ route('ayuda.tickets.index', ['mis_dudas' => 1]) }}"
                   class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3.5 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-all shadow-sm">
                    <i class="fas fa-user-circle text-blue-600"></i>
                    <span>Mis Dudas</span>
                </a>
                <button type="button"
                        onclick="abrirModalWhatsapp()"
                        class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:text-emerald-700 hover:border-emerald-400 transition-all shadow-sm cursor-pointer">
                    <i class="fab fa-whatsapp text-emerald-600 text-sm"></i>
                    <span>Consultar por WhatsApp</span>
                </button>
            </div>
        </section>
    </div>

    <!-- ─── MODAL DE CONSULTA DE WHATSAPP (RESPONSIVO CON SCROLL) ─── -->
    <div id="modalWhatsappAyuda" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5">
        <div class="bg-white rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden transform transition-all my-auto">
            
            <!-- Encabezado Fijo del Modal -->
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-base shrink-0">
                        <i class="fab fa-whatsapp"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 leading-tight">Enviar consulta a soporte</h3>
                        <p class="text-[11px] text-slate-500 leading-tight">Completa los detalles para atenderte con mayor rapidez.</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalWhatsapp()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition-colors cursor-pointer" title="Cerrar">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            <!-- Cuerpo del Modal (Con Scroll Interno) -->
            <div class="p-5 overflow-y-auto flex-1 space-y-3.5">
                
                <!-- Badge de usuario -->
                <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200/60 text-xs">
                    <span class="text-slate-500 font-medium">Consultando como:</span>
                    <span class="font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="fas fa-user-circle text-slate-400"></i>
                        {{ auth()->user()->nombre_real ?? 'Usuario' }}
                        <span class="px-1.5 py-0.5 rounded text-[10px] uppercase font-bold bg-blue-100 text-blue-700">
                            {{ auth()->user()->rol ?? 'vendedor' }}
                        </span>
                    </span>
                </div>

                <!-- Fila de 2 Columnas: Módulo y Tipo de Consulta -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Selector de Módulo -->
                    <div>
                        <label for="waModuloSelect" class="block text-xs font-bold text-slate-700 mb-1">
                            Módulo de la consulta <span class="text-rose-500">*</span>
                        </label>
                        <select id="waModuloSelect" onchange="actualizarPreviewWhatsapp()" class="w-full text-xs rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach ($guides as $key => $item)
                                <option value="{{ $item['module'] }}" {{ ($topic === $key) ? 'selected' : '' }}>
                                    {{ $item['module'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo de consulta -->
                    <div>
                        <label for="waTipoSelect" class="block text-xs font-bold text-slate-700 mb-1">
                            Tipo de consulta <span class="text-rose-500">*</span>
                        </label>
                        <select id="waTipoSelect" onchange="actualizarPreviewWhatsapp()" class="w-full text-xs rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="Duda de funcionamiento">Duda de funcionamiento</option>
                            <option value="Problema o Error">Inconveniente o error</option>
                            <option value="Consulta sobre pedido o venta">Consulta de venta / stock</option>
                            <option value="Solicitud de ajuste">Solicitud de ajuste especial</option>
                            <option value="Otra consulta">Otra consulta general</option>
                        </select>
                    </div>
                </div>

                <!-- Duda específica -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="waDudaTexto" class="block text-xs font-bold text-slate-700">
                            Describe tu duda o problema <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[10px] text-slate-400 font-mono" id="waCharCount">0 / 300</span>
                    </div>
                    <textarea id="waDudaTexto"
                              rows="3"
                              maxlength="300"
                              oninput="actualizarPreviewWhatsapp()"
                              placeholder="Describe brevemente qué estás intentando hacer o qué inconveniente tienes..."
                              class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all resize-none"></textarea>
                    <p id="errorWaDuda" class="hidden text-[11px] font-bold text-rose-600 flex items-center gap-1 mt-1 pl-1">
                        <i class="fas fa-circle-exclamation text-[10px]"></i>
                        <span>Debes ingresar el detalle de tu duda o consulta (mínimo 5 caracteres).</span>
                    </p>
                </div>

                <!-- Adjuntar Foto / Evidencia (Opcional) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Foto o captura de evidencia <span class="text-slate-400 font-normal">(opcional)</span>
                        </label>
                        <span id="waFotoStatus" class="text-[10px] text-slate-400">Máx. 5MB</span>
                    </div>

                    <!-- Input file oculto -->
                    <input type="file" id="waFotoInput" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="procesarFotoEvidencia(event)">

                    <!-- Área de selección si no hay foto -->
                    <div id="waUploadArea"
                         onclick="document.getElementById('waFotoInput').click()"
                         class="border-2 border-dashed border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/20 rounded-xl p-2.5 flex items-center justify-center gap-2 cursor-pointer transition-all text-slate-600">
                        <i class="fas fa-camera text-slate-400 text-xs"></i>
                        <span class="text-xs font-medium text-slate-600">Subir captura o foto de evidencia</span>
                    </div>

                    <!-- Previsualización de la foto seleccionada -->
                    <div id="waPreviewFotoArea" class="hidden flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <img id="waFotoThumb" src="" alt="Evidencia" class="w-9 h-9 rounded-lg object-cover border border-slate-200 shrink-0">
                            <div class="min-w-0">
                                <p id="waFotoNombre" class="text-xs font-bold text-slate-700 truncate max-w-[200px] sm:max-w-[280px]">imagen.jpg</p>
                                <p id="waFotoSubiendoText" class="text-[10px] text-emerald-600 font-medium flex items-center gap-1">
                                    <i class="fas fa-spinner fa-spin"></i> Subiendo enlace...
                                </p>
                            </div>
                        </div>
                        <button type="button" onclick="quitarFotoEvidencia()" class="text-slate-400 hover:text-rose-500 p-1.5 transition-colors cursor-pointer" title="Quitar foto">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Vista previa del mensaje formateado (Acordeón desplegable compacto) -->
                <details class="group rounded-xl border border-slate-200 bg-slate-50 overflow-hidden">
                    <summary class="flex items-center justify-between px-3 py-2 cursor-pointer select-none text-[11px] font-bold text-slate-600 hover:bg-slate-100 transition-colors list-none">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-receipt text-slate-400"></i>
                            <span>Ver formato del ticket a enviar</span>
                        </span>
                        <div class="flex items-center gap-1 text-[10px] text-slate-400">
                            <span class="group-open:hidden">Mostrar</span>
                            <span class="hidden group-open:inline">Ocultar</span>
                            <i class="fas fa-chevron-down transition-transform duration-200 group-open:rotate-180"></i>
                        </div>
                    </summary>
                    <div class="p-3 border-t border-slate-200 text-[11px] font-mono text-slate-700 whitespace-pre-wrap leading-relaxed max-h-32 overflow-y-auto break-words bg-white" id="waPreviewBox">
                        ...
                    </div>
                </details>
            </div>

            <!-- Footer Fijo del Modal -->
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="cerrarModalWhatsapp()" class="w-full sm:w-auto px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-200 transition-colors cursor-pointer text-center">
                    Cancelar
                </button>
                <button type="button"
                        id="btnEnviarWhatsappModal"
                        onclick="enviarAWhatsapp()"
                        class="relative w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-xs font-bold text-white shadow-md hover:shadow-lg transition-all cursor-pointer overflow-hidden disabled:opacity-60 disabled:cursor-not-allowed"
                        style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);">
                    <span id="btnEnviarWhatsappText" class="inline-flex items-center gap-2">
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span>Abrir WhatsApp con este mensaje</span>
                    </span>
                    <span id="btnEnviarWhatsappSpinner" class="absolute inset-0 flex items-center justify-center gap-2" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <span>Registrando ticket...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <script>
        const WA_PHONE = "{{ preg_replace('/[^0-9]/', '', env('WHATSAPP_SOPORTE', '50362057666')) }}";
        const USER_NAME = "{{ addslashes(auth()->user()->nombre_real ?? 'Usuario') }}";
        const USER_ROLE = "{{ ucfirst(auth()->user()->rol ?? 'vendedor') }}";
        const UPLOAD_ROUTE = "{{ route('ayuda.subir-evidencia') }}";
        const STORE_TICKET_ROUTE = "{{ route('ayuda.tickets.store') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";

        let urlEvidenciaSubida = '';
        let subiendoFoto = false;
        let currentTicketId = '';

        function abrirModalWhatsapp() {
            currentTicketId = 'TK-' + Math.floor(10000 + Math.random() * 90000);
            document.getElementById('modalWhatsappAyuda').classList.remove('hidden');
            actualizarPreviewWhatsapp();
            setTimeout(() => {
                document.getElementById('waDudaTexto').focus();
            }, 100);
        }

        function cerrarModalWhatsapp() {
            document.getElementById('modalWhatsappAyuda').classList.add('hidden');
            const txt = document.getElementById('waDudaTexto');
            const errP = document.getElementById('errorWaDuda');
            if (txt && errP) {
                txt.classList.remove('border-rose-500', 'bg-rose-50/40', 'ring-1', 'ring-rose-400');
                txt.classList.add('border-slate-300');
                errP.classList.add('hidden');
            }
        }

        document.getElementById('waDudaTexto')?.addEventListener('input', function() {
            if (this.value.trim().length >= 5) {
                this.classList.remove('border-rose-500', 'bg-rose-50/40', 'ring-1', 'ring-rose-400');
                this.classList.add('border-slate-300');
                document.getElementById('errorWaDuda')?.classList.add('hidden');
            }
        });

        async function procesarFotoEvidencia(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validar peso máximo 5MB
            if (file.size > 5 * 1024 * 1024) {
                alert('La imagen no debe superar los 5MB.');
                e.target.value = '';
                return;
            }

            // Mostrar preview local
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('waFotoThumb').src = event.target.result;
            };
            reader.readAsDataURL(file);

            document.getElementById('waFotoNombre').textContent = file.name;
            document.getElementById('waUploadArea').classList.add('hidden');
            document.getElementById('waPreviewFotoArea').classList.remove('hidden');
            document.getElementById('waFotoSubiendoText').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo enlace...';
            document.getElementById('waFotoSubiendoText').className = 'text-[10px] text-emerald-600 font-medium flex items-center gap-1';

            subiendoFoto = true;

            const formData = new FormData();
            formData.append('evidencia', file);
            formData.append('_token', CSRF_TOKEN);

            try {
                const response = await fetch(UPLOAD_ROUTE, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success && data.url) {
                    urlEvidenciaSubida = data.url;
                    document.getElementById('waFotoSubiendoText').innerHTML = '<i class="fas fa-check-circle"></i> Enlace listo para el ticket';
                    document.getElementById('waFotoSubiendoText').className = 'text-[10px] text-emerald-700 font-bold flex items-center gap-1';
                    actualizarPreviewWhatsapp();
                } else {
                    throw new Error(data.message || 'Error al subir');
                }
            } catch (err) {
                console.error(err);
                document.getElementById('waFotoSubiendoText').innerHTML = '<i class="fas fa-exclamation-circle text-rose-500"></i> No se pudo subir el enlace';
                document.getElementById('waFotoSubiendoText').className = 'text-[10px] text-rose-600 font-medium flex items-center gap-1';
            } finally {
                subiendoFoto = false;
            }
        }

        function quitarFotoEvidencia() {
            urlEvidenciaSubida = '';
            document.getElementById('waFotoInput').value = '';
            document.getElementById('waUploadArea').classList.remove('hidden');
            document.getElementById('waPreviewFotoArea').classList.add('hidden');
            document.getElementById('waFotoThumb').src = '';
            actualizarPreviewWhatsapp();
        }

        function construirMensajeWhatsapp() {
            const modulo = document.getElementById('waModuloSelect').value;
            const tipo = document.getElementById('waTipoSelect').value;
            const duda = document.getElementById('waDudaTexto').value.trim();

            const ahora = new Date();
            const ticketId = currentTicketId || ('TK-' + Math.floor(10000 + Math.random() * 90000));
            currentTicketId = ticketId;
            const fechaHora = ahora.toLocaleDateString('es-SV', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ', ' + 
                              ahora.toLocaleTimeString('es-SV', { hour: '2-digit', minute: '2-digit', hour12: true });

            const separador = '────────────────────────────────────────────────────';

            let msg = `*AXSTORE · TICKET DE SOPORTE*\n`;
            msg += `${separador}\n`;
            msg += `*Nº Ticket:* #${ticketId}\n`;
            msg += `*Fecha:* ${fechaHora}\n`;
            msg += `*Usuario:* ${USER_NAME} (${USER_ROLE})\n`;
            msg += `*Módulo:* ${modulo}\n`;
            msg += `*Motivo:* ${tipo}\n`;
            msg += `${separador}\n`;
            msg += `*DESCRIPCIÓN DE LA DUDA:*\n`;
            if (duda) {
                msg += `${duda}\n`;
            } else {
                msg += `Hola, solicito asistencia técnica para resolver una duda sobre este módulo.\n`;
            }

            if (urlEvidenciaSubida) {
                msg += `${separador}\n`;
                msg += `*EVIDENCIA ADJUNTA:*\n${urlEvidenciaSubida}\n`;
            }

            msg += `${separador}\n`;
            msg += `_Centro de Ayuda AXStore_`;

            return msg;
        }

        function actualizarPreviewWhatsapp() {
            const textarea = document.getElementById('waDudaTexto');
            document.getElementById('waCharCount').textContent = `${textarea.value.length} / 300`;
            const mensaje = construirMensajeWhatsapp();
            document.getElementById('waPreviewBox').textContent = mensaje;
        }

        async function enviarAWhatsapp() {
            if (subiendoFoto) {
                Swal.fire({
                    icon: 'info',
                    title: 'Subiendo Evidencia',
                    text: 'Espera un segundo mientras se termina de adjuntar la imagen...',
                    timer: 2500,
                    showConfirmButton: false
                });
                return;
            }

            const modulo = document.getElementById('waModuloSelect').value;
            const tipo = document.getElementById('waTipoSelect').value;
            const dudaInput = document.getElementById('waDudaTexto');
            const duda = dudaInput.value.trim();
            const errP = document.getElementById('errorWaDuda');

            if (!duda || duda.length < 5) {
                dudaInput.classList.remove('border-slate-300');
                dudaInput.classList.add('border-rose-500', 'bg-rose-50/40', 'ring-1', 'ring-rose-400');
                if (errP) errP.classList.remove('hidden');
                dudaInput.focus();
                return;
            }

            const mensaje = construirMensajeWhatsapp();

            const btnEnviar = document.getElementById('btnEnviarWhatsappModal');
            const btnText = document.getElementById('btnEnviarWhatsappText');
            const btnSpinner = document.getElementById('btnEnviarWhatsappSpinner');

            if (btnEnviar && btnText && btnSpinner) {
                btnEnviar.disabled = true;
                btnText.classList.add('invisible');
                btnSpinner.style.display = 'flex';
            }

            try {
                // Registrar ticket en base de datos y bitácora
                await fetch(STORE_TICKET_ROUTE, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        codigo_ticket: currentTicketId,
                        modulo: modulo,
                        tipo_consulta: tipo,
                        duda: duda || 'Consulta general de soporte',
                        evidencia_url: urlEvidenciaSubida || null
                    })
                });
            } catch (err) {
                console.error('No se pudo registrar en la bitacora de tickets:', err);
            } finally {
                if (btnEnviar && btnText && btnSpinner) {
                    btnEnviar.disabled = false;
                    btnText.classList.remove('invisible');
                    btnSpinner.style.display = 'none';
                }
            }

            // Uso de api.whatsapp.com para evitar redirecciones que dañen caracteres en navegadores Windows
            const url = `https://api.whatsapp.com/send?phone=${WA_PHONE}&text=${encodeURIComponent(mensaje)}`;
            window.open(url, '_blank', 'noopener,noreferrer');
            cerrarModalWhatsapp();

            // Limpiar formulario y regenerar ticketId para una futura consulta
            document.getElementById('waDudaTexto').value = '';
            quitarFotoEvidencia();
            currentTicketId = '';
        }

        // Cerrar modal al presionar Escape o clic fuera
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') cerrarModalWhatsapp();
        });

        document.getElementById('modalWhatsappAyuda')?.addEventListener('click', function (e) {
            if (e.target === this) cerrarModalWhatsapp();
        });

        document.addEventListener('DOMContentLoaded', function () {
            const navDetails = document.getElementById('ayudaModulosNav');
            if (!navDetails) return;

            const savedState = localStorage.getItem('ayuda_modulos_open');
            if (savedState === 'false') {
                navDetails.removeAttribute('open');
            } else {
                navDetails.setAttribute('open', '');
            }

            navDetails.addEventListener('toggle', function () {
                localStorage.setItem('ayuda_modulos_open', navDetails.open ? 'true' : 'false');
            });
        });
    </script>
</x-app>






