<x-app title="Gestión de Cuentas y Auditorías | AXStore Super Admin">
    <script>
        function gestionSuperAdminUsuarios(config) {
            return {
                filtroSearch: config.search || '',
                filtroRol: config.rol || 'todos',
                filtroEstado: config.estado || 'todos',
                cargandoUsuarios: false,
                solicitudCarga: 0,
                errorCarga: '',

                async ejecutarFiltro() {
                    await this.cargarUsuarios();
                },

                async limpiarFiltros() {
                    this.filtroSearch = '';
                    this.filtroRol = 'todos';
                    this.filtroEstado = 'todos';
                    await this.cargarUsuarios();
                },

                construirUrl(pagina = 1) {
                    const params = new URLSearchParams();
                    if (this.filtroSearch && this.filtroSearch.trim() !== '') {
                        params.set('search', this.filtroSearch.trim());
                    }
                    if (this.filtroRol && this.filtroRol !== 'todos') {
                        params.set('rol', this.filtroRol);
                    }
                    if (this.filtroEstado && this.filtroEstado !== 'todos') {
                        params.set('estado', this.filtroEstado);
                    }
                    if (pagina > 1) {
                        params.set('page', pagina);
                    }

                    const queryString = params.toString();
                    return `{{ route('superadmin.usuarios') }}${queryString ? '?' + queryString : ''}`;
                },

                async cargarUsuarios(urlDestino = null) {
                    const solicitud = ++this.solicitudCarga;
                    this.cargandoUsuarios = true;
                    this.errorCarga = '';
                    const url = urlDestino || this.construirUrl(1);

                    try {
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error('No se pudo completar la solicitud.');
                        const data = await res.json();
                        if (solicitud !== this.solicitudCarga) return;
                        if (!data.success || typeof data.html !== 'string') {
                            throw new Error('La respuesta no contiene los datos esperados.');
                        }
                        if (data.success) {
                            window.history.pushState({}, '', url);
                            const contenedor = document.getElementById('contenedorTablaSuperAdminUsuarios');
                            if (contenedor) {
                                contenedor.innerHTML = data.html;
                                this.atarPaginacionUsuarios();
                            }
                        }
                    } catch (err) {
                        if (solicitud !== this.solicitudCarga) return;
                        this.errorCarga = 'No se pudieron cargar los usuarios. Intenta de nuevo.';
                        console.error('Error al cargar usuarios super admin:', err);
                    } finally {
                        if (solicitud === this.solicitudCarga) this.cargandoUsuarios = false;
                    }
                },

                atarPaginacionUsuarios() {
                    const contenedor = document.getElementById('contenedorTablaSuperAdminUsuarios');
                    if (!contenedor) return;
                    const links = contenedor.querySelectorAll('a[href*="page="]');
                    links.forEach(link => {
                        link.addEventListener('click', async (e) => {
                            e.preventDefault();
                            const href = link.getAttribute('href');
                            if (!href) return;
                            await this.cargarUsuarios(href);
                        });
                    });
                },

                init() {
                    this.atarPaginacionUsuarios();
                }
            };
        }
    </script>

    <div class="mx-auto max-w-7xl space-y-6 pb-12" x-data="gestionSuperAdminUsuarios({
        search: @js(request('search', '')),
        rol: @js(request('rol', 'todos')),
        estado: @js(request('estado', 'todos'))
    })">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('superadmin.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 mb-1">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al Panel de Gobernanza</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fas fa-users-gear"></i>
                    </span>
                    <span>Gestión de Usuarios y Auditoría Segura</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Supervisión de cuentas, roles, permisos y activación de sesiones de suplantación controlada (Secure Impersonation).
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('modalNuevoSuperAdmin').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm cursor-pointer">
                    <i class="fas fa-user-shield mr-1.5"></i>
                    Crear Super Admin
                </button>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <form @submit.prevent="ejecutarFiltro()" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[240px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text"
                           x-model="filtroSearch"
                           @input.debounce.400ms="ejecutarFiltro()"
                           placeholder="Buscar por nombre o correo electrónico..."
                           class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div>
                <select x-model="filtroRol" @change="ejecutarFiltro()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 bg-white cursor-pointer">
                    <option value="todos">Todos los roles</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="vendedor">Vendedor</option>
                </select>
            </div>

            <div>
                <select x-model="filtroEstado" @change="ejecutarFiltro()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:outline-none focus:border-indigo-500 bg-white cursor-pointer">
                    <option value="todos">Todos los estados</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-3 text-xs transition-all shadow-md shadow-blue-600/15 flex items-center justify-center gap-1.5 cursor-pointer">
                    <i :class="cargandoUsuarios ? 'fas fa-spinner fa-spin' : 'fas fa-filter'"></i>
                    <span x-text="cargandoUsuarios ? 'Filtrando...' : 'Filtrar'"></span>
                </button>
                <button type="button" 
                        @click="limpiarFiltros()"
                        x-show="filtroSearch || filtroRol !== 'todos' || filtroEstado !== 'todos'"
                        style="display: none;"
                        class="rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold py-2 px-3 text-xs transition-colors flex items-center justify-center cursor-pointer" 
                        title="Restablecer filtros">
                    <i class="fas fa-rotate-left"></i>
                </button>
            </div>
        </form>

        <!-- Skeleton Loading Inteligente para Tabla de Usuarios Super Admin -->
        <p x-show="errorCarga" x-cloak x-text="errorCarga" role="alert" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700"></p>
        <div x-show="cargandoUsuarios" style="display: none;" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-2">
            <x-skeleton-table :rows="5" :cols="6" :avatarCol="0" />
        </div>

        <!-- Tabla de Usuarios -->
        <div id="contenedorTablaSuperAdminUsuarios" x-show="!cargandoUsuarios" :aria-busy="cargandoUsuarios" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            @include('superadmin.tabla-usuarios', ['users' => $users])
        </div>

    </div>

    <!-- Modal para Iniciar Auditoría Segura (Secure Impersonation) -->
    <div id="modalAuditoria" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-base">
                        <i class="fas fa-user-shield"></i>
                    </span>
                    <div>
                        <h3 class="text-base font-black">Iniciar Auditoría Segura</h3>
                        <p class="text-xs text-slate-400">Suplantación administrativa trazable</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalAuditoria').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="formAuditoria" data-loading-form data-loading-label="Iniciando..." method="POST" action="" class="p-6 space-y-4 text-xs">
                @csrf
                <div class="p-3 bg-purple-50 rounded-xl border border-purple-200 text-purple-900">
                    <span class="font-bold block">Cuenta a auditar:</span>
                    <span id="auditarNombreUsuario" class="text-sm font-black text-purple-950"></span>
                    <span id="auditarEmailUsuario" class="block text-slate-500 text-[11px] font-mono"></span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Justificación de la Auditoría <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="justificacion" required rows="3" placeholder="Describe el motivo formal (ej: Investigación de discrepancia en reporte de comisiones ticket #TK-84920)..." class="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:border-purple-500 text-xs"></textarea>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Quedará registrado permanentemente en la bitácora inmutable.</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Duración Máxima</label>
                        <select name="duracion" class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-purple-500 text-xs">
                            <option value="15" selected>15 Minutos</option>
                            <option value="30">30 Minutos</option>
                            <option value="60">1 Hora (Extendido)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Modo de Operación</label>
                        <select name="solo_lectura" class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-purple-500 text-xs">
                            <option value="1" selected>Solo Lectura (Seguro)</option>
                            <option value="0">Permisivo</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Confirmación con Contraseña de Super Admin <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="mfa_password" required placeholder="Ingresa tu contraseña actual para autorizar" class="w-full p-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-purple-500 text-xs">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalAuditoria').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="relative inline-flex items-center justify-center px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition-colors shadow-sm cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                        <span class="btn-text">Iniciar Sesión de Auditoría</span>
                        <span class="btn-spinner absolute inset-0 flex items-center justify-center gap-1.5" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Iniciando...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Crear Nuevo Super Admin (Estilo idéntico a Gestión de Usuarios) -->
    <div id="modalNuevoSuperAdmin" class="fixed inset-0 z-[120] hidden overflow-y-auto" aria-labelledby="modal-title-create" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/65 backdrop-blur-xs transition-opacity" onclick="cerrarModalNuevoSuperAdmin()"></div>

        <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
            <div class="relative w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8">

                <!-- Cabecera -->
                <div class="bg-indigo-600 px-6 py-4.5 flex items-center justify-between text-white shadow-md">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center text-white text-sm shadow-xs">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-black tracking-tight">
                            Registrar Nuevo Super Administrador
                        </h3>
                    </div>
                    <button type="button" 
                            onclick="cerrarModalNuevoSuperAdmin()" 
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors cursor-pointer"
                            title="Cerrar ventana">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Formulario -->
                <form method="POST" data-loading-form data-loading-label="Registrando..." action="{{ route('superadmin.crear') }}" autocomplete="off">
                    @csrf

                    <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-12 gap-6 sm:gap-8 max-h-[75vh] overflow-y-auto custom-scrollbar">

                        <!-- Tarjeta de previsualización (Izquierda) -->
                        <div class="md:col-span-5 flex flex-col">
                            <h4 class="text-[11px] font-black uppercase tracking-wider text-gray-500 mb-3.5">
                                INFORMACIÓN DEL SUPER ADMIN
                            </h4>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-5 flex flex-col items-center justify-center text-center relative overflow-hidden flex-1 shadow-inner">
                                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-700"></div>

                                <!-- Avatar -->
                                <div id="createPreviewAvatar" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center text-4xl font-black shadow-lg shadow-indigo-500/25 mb-4 transition-transform hover:scale-105">
                                    <span id="createPreviewInitial">S</span>
                                </div>

                                <!-- Nombre en tiempo real -->
                                <h5 id="createPreviewName" class="text-base font-extrabold text-gray-900 leading-tight">
                                    Nuevo Super Admin
                                </h5>
                                <p id="createPreviewUsername" class="text-xs text-gray-500 mt-0.5 break-all">
                                    superadmin@axstore.com
                                </p>

                                <!-- Insignias -->
                                <div class="flex items-center gap-2 mt-3.5 flex-wrap justify-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                        <i class="fas fa-shield-halved text-[9px]"></i> <span>SUPER ADMIN</span>
                                    </span>
                                    <span id="createPreviewStatusBadge" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span id="createPreviewStatusText">Habilitado</span>
                                    </span>
                                    <span id="createPreviewPhoneBadge" class="hidden inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="fas fa-phone text-[9px]"></i> <span id="createPreviewPhoneText"></span>
                                    </span>
                                </div>

                                <!-- Resumen de seguridad -->
                                <div class="w-full mt-5 pt-4 border-t border-gray-200/60 text-left text-xs space-y-2 text-gray-600">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-[11px] font-medium">Plataforma:</span>
                                        <span class="font-bold text-gray-800">AXStore Governance</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-[11px] font-medium">Nivel de Acceso:</span>
                                        <span class="font-bold text-indigo-600 flex items-center gap-1">
                                            <i class="fas fa-crown text-[10px]"></i> Total / Gobernanza
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-[11px] font-medium">Autenticación:</span>
                                        <span class="font-bold text-emerald-600 flex items-center gap-1">
                                            <i class="fas fa-shield-halved text-[10px]"></i> Cifrado Bcrypt
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos del formulario (Derecha) -->
                        <div class="md:col-span-7 flex flex-col space-y-4">
                            <h4 class="text-[11px] font-black uppercase tracking-wider text-gray-500 mb-0.5">
                                DATOS DEL SUPER ADMINISTRADOR
                            </h4>

                            <!-- Nombre Completo -->
                            <div>
                                <label for="create_nombre_real" class="block text-xs font-bold text-gray-800 mb-1">
                                    Nombre Completo <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="text" 
                                       id="create_nombre_real" 
                                       name="nombre_real"
                                       autocomplete="off"
                                       required
                                       placeholder="Ej. Ing. Carlos Mendoza"
                                       class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all"
                                       oninput="updateCreateSuperAdminPreview()">
                                <p class="text-[11px] text-gray-400 mt-1">Nombre y apellidos reales del super administrador (Campo obligatorio)</p>
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label for="create_telefono" class="block text-xs font-bold text-gray-800 mb-1">
                                    Teléfono
                                </label>
                                <div class="relative">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center gap-1 text-gray-400 text-xs font-bold select-none">
                                        <img src="https://flagcdn.com/w20/sv.png" alt="SV" class="h-3.5 rounded-sm opacity-90">
                                        <span>+503</span>
                                    </div>
                                    <input type="text" 
                                           id="create_telefono" 
                                           name="telefono"
                                           autocomplete="off"
                                           maxlength="9"
                                           placeholder="XXXX-XXXX"
                                           class="w-full rounded-xl border border-gray-300 pr-10 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all"
                                           style="padding-left: 5.2rem;"
                                           oninput="formatTelefonoInput(this); updateCreateSuperAdminPreview()">
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Formato: XXXX-XXXX (ej: 7890-1234)</p>
                            </div>

                            <!-- Correo Institucional -->
                            <div>
                                <label for="create_username" class="block text-xs font-bold text-gray-800 mb-1">
                                    Correo Institucional o Usuario <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="text" 
                                       id="create_username" 
                                       name="username"
                                       autocomplete="one-time-code"
                                       autocomplete="off"
                                       required
                                       placeholder="Ej. cmendoza@axstore.com"
                                       class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all"
                                       oninput="updateCreateSuperAdminPreview()">
                                <p class="text-[11px] text-gray-400 mt-1">Identificador único de acceso administrativo (Campo obligatorio)</p>
                            </div>

                            <!-- Estado de la Cuenta -->
                            <div>
                                <label for="create_estado" class="block text-xs font-bold text-gray-800 mb-1">
                                    Estado de la Cuenta <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <select id="create_estado" 
                                        name="estado" 
                                        required
                                        class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all bg-white"
                                        onchange="updateCreateSuperAdminPreview()">
                                    <option value="1" selected>Habilitado</option>
                                    <option value="0">Deshabilitado (Suspendido)</option>
                                </select>
                                <p class="text-[11px] text-gray-400 mt-1">Estado de operatividad (Campo obligatorio)</p>
                            </div>

                            <!-- Contraseña y Confirmación -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                                <div>
                                    <label for="create_password" class="block text-xs font-bold text-gray-800 mb-1">
                                        Contraseña <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               id="create_password" 
                                               name="password"
                                               autocomplete="new-password"
                                               required
                                               placeholder="••••••••"
                                               class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 pr-10 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all">
                                        <button type="button" 
                                                onclick="toggleInputPassword('create_password', 'create_eye_pwd')" 
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 cursor-pointer text-xs p-1">
                                            <i class="fas fa-eye" id="create_eye_pwd"></i>
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Mínimo 8 caracteres (Campo obligatorio)</p>
                                </div>

                                <div>
                                    <label for="create_password_confirmation" class="block text-xs font-bold text-gray-800 mb-1">
                                        Confirmar Contraseña <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               id="create_password_confirmation" 
                                               name="password_confirmation"
                                               autocomplete="new-password"
                                               required
                                               placeholder="••••••••"
                                               class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 pr-10 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all">
                                        <button type="button" 
                                                onclick="toggleInputPassword('create_password_confirmation', 'create_eye_conf')" 
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 cursor-pointer text-xs p-1">
                                            <i class="fas fa-eye" id="create_eye_conf"></i>
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Repite la contraseña exacta (Campo obligatorio)</p>
                                </div>
                            </div>

                            <!-- PASO OBLIGATORIO DE SEGURIDAD (SUPER ADMIN EN SESIÓN) -->
                            <div class="p-3.5 bg-amber-50/80 border border-amber-200 rounded-2xl space-y-1 mt-2 shadow-xs">
                                <div class="flex items-center gap-1.5 text-amber-900 font-extrabold text-xs">
                                    <i class="fas fa-lock text-amber-600"></i>
                                    <span>Paso de Seguridad Obligatorio</span>
                                </div>
                                <label for="create_auth_password" class="block text-xs font-bold text-gray-800 pt-1">
                                    Contraseña de tu cuenta Super Admin actual en sesión <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="create_auth_password" 
                                           name="auth_password" 
                                           required 
                                           placeholder="Ingresa tu contraseña para autorizar la creación" 
                                           class="w-full rounded-xl border border-amber-300 bg-white px-3.5 py-2.5 pr-10 text-sm text-gray-900 placeholder:text-gray-400 focus:border-amber-600 focus:ring-2 focus:ring-amber-600/20 focus:outline-none transition-all">
                                    <button type="button" 
                                            onclick="toggleInputPassword('create_auth_password', 'create_eye_auth')" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 cursor-pointer text-xs p-1">
                                        <i class="fas fa-eye" id="create_eye_auth"></i>
                                    </button>
                                </div>
                                <p class="text-[11px] text-amber-800/80">Se requiere la verificación de tu contraseña para crear un usuario con privilegios totales.</p>
                            </div>

                        </div>
                    </div>

                    <!-- Pie del Modal -->
                    <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" 
                                onclick="cerrarModalNuevoSuperAdmin()" 
                                class="px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                            CANCELAR
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-black text-sm text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-indigo-600 hover:bg-indigo-700 shadow-indigo-600/25">
                            <i class="fas fa-check text-xs"></i>
                            <span>REGISTRAR SUPER ADMIN</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Modal para Editar Super Admin (Estilo idéntico a Gestión de Usuarios) -->
    <div id="modalEditarSuperAdmin" class="fixed inset-0 z-[120] hidden overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/65 backdrop-blur-xs transition-opacity" onclick="cerrarModalEditarSuperAdmin()"></div>

        <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
            <div class="relative w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8">

                <!-- Cabecera -->
                <div class="bg-indigo-600 px-6 py-4.5 flex items-center justify-between text-white shadow-md">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center text-white text-sm shadow-xs">
                            <i class="fas fa-user-pen"></i>
                        </div>
                        <h3 class="text-base sm:text-lg font-black tracking-tight">
                            Editar Super Administrador
                        </h3>
                    </div>
                    <button type="button" 
                            onclick="cerrarModalEditarSuperAdmin()" 
                            class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors cursor-pointer"
                            title="Cerrar ventana">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Formulario -->
                <form id="formEditarSuperAdmin" data-loading-form data-loading-label="Guardando..." method="POST" action="" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-12 gap-6 sm:gap-8 max-h-[75vh] overflow-y-auto custom-scrollbar">

                        <!-- Tarjeta de previsualización (Izquierda) -->
                        <div class="md:col-span-5 flex flex-col">
                            <h4 class="text-[11px] font-black uppercase tracking-wider text-gray-500 mb-3.5">
                                INFORMACIÓN DEL SUPER ADMIN
                            </h4>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-5 flex flex-col items-center justify-center text-center relative overflow-hidden flex-1 shadow-inner">
                                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-700"></div>

                                <!-- Avatar -->
                                <div id="editPreviewAvatar" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center text-4xl font-black shadow-lg shadow-indigo-500/25 mb-4 transition-transform hover:scale-105">
                                    <span id="editPreviewInitial">S</span>
                                </div>

                                <!-- Nombre en tiempo real -->
                                <h5 id="editPreviewName" class="text-base font-extrabold text-gray-900 leading-tight">
                                    Super Admin
                                </h5>
                                <p id="editPreviewUsername" class="text-xs text-gray-500 mt-0.5 break-all">
                                    superadmin@axstore.com
                                </p>

                                <!-- Insignias -->
                                <div class="flex items-center gap-2 mt-3.5 flex-wrap justify-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                        <i class="fas fa-shield-halved text-[9px]"></i> <span>SUPER ADMIN</span>
                                    </span>
                                    <span id="editPreviewStatusBadge" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span id="editPreviewStatusText">Habilitado</span>
                                    </span>
                                    <span id="editPreviewPhoneBadge" class="hidden inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="fas fa-phone text-[9px]"></i> <span id="editPreviewPhoneText"></span>
                                    </span>
                                </div>

                                <!-- Resumen de seguridad -->
                                <div class="w-full mt-5 pt-4 border-t border-gray-200/60 text-left text-xs space-y-2 text-gray-600">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-[11px] font-medium">Plataforma:</span>
                                        <span class="font-bold text-gray-800">AXStore Governance</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-[11px] font-medium">Nivel de Acceso:</span>
                                        <span class="font-bold text-indigo-600 flex items-center gap-1">
                                            <i class="fas fa-crown text-[10px]"></i> Total / Gobernanza
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-[11px] font-medium">Autenticación:</span>
                                        <span class="font-bold text-emerald-600 flex items-center gap-1">
                                            <i class="fas fa-shield-halved text-[10px]"></i> Cifrado Bcrypt
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos del formulario (Derecha) -->
                        <div class="md:col-span-7 flex flex-col space-y-4">
                            <h4 class="text-[11px] font-black uppercase tracking-wider text-gray-500 mb-0.5">
                                DATOS DEL SUPER ADMINISTRADOR
                            </h4>

                            <!-- Nombre Completo -->
                            <div>
                                <label for="editSuperAdminNombre" class="block text-xs font-bold text-gray-800 mb-1">
                                    Nombre Completo <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="text" 
                                       id="editSuperAdminNombre" 
                                       name="nombre_real"
                                       autocomplete="off"
                                       required
                                       placeholder="Ej. Ing. Carlos Mendoza"
                                       class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all"
                                       oninput="updateEditSuperAdminPreview()">
                                <p class="text-[11px] text-gray-400 mt-1">Nombre y apellidos reales del super administrador (Campo obligatorio)</p>
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label for="editSuperAdminTelefono" class="block text-xs font-bold text-gray-800 mb-1">
                                    Teléfono
                                </label>
                                <div class="relative">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center gap-1 text-gray-400 text-xs font-bold select-none">
                                        <img src="https://flagcdn.com/w20/sv.png" alt="SV" class="h-3.5 rounded-sm opacity-90">
                                        <span>+503</span>
                                    </div>
                                    <input type="text" 
                                           id="editSuperAdminTelefono" 
                                           name="telefono"
                                           autocomplete="off"
                                           maxlength="9"
                                           placeholder="XXXX-XXXX"
                                           class="w-full rounded-xl border border-gray-300 pr-10 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all"
                                           style="padding-left: 5.2rem;"
                                           oninput="formatTelefonoInput(this); updateEditSuperAdminPreview()">
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Formato: XXXX-XXXX (ej: 7890-1234)</p>
                            </div>

                            <!-- Correo Institucional -->
                            <div>
                                <label for="editSuperAdminUsername" class="block text-xs font-bold text-gray-800 mb-1">
                                    Correo Institucional o Usuario <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input type="text" 
                                       id="editSuperAdminUsername" 
                                       name="username"
                                       autocomplete="one-time-code"
                                       autocomplete="off"
                                       required
                                       placeholder="Ej. cmendoza@axstore.com"
                                       class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all"
                                       oninput="updateEditSuperAdminPreview()">
                                <p class="text-[11px] text-gray-400 mt-1">Identificador único de acceso administrativo (Campo obligatorio)</p>
                            </div>

                            <!-- Estado de la Cuenta -->
                            <div>
                                <label for="editSuperAdminEstado" class="block text-xs font-bold text-gray-800 mb-1">
                                    Estado de la Cuenta <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <select id="editSuperAdminEstado" 
                                        name="estado" 
                                        required
                                        class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all bg-white"
                                        onchange="updateEditSuperAdminPreview()">
                                    <option value="1">Habilitado</option>
                                    <option value="0">Deshabilitado (Suspendido)</option>
                                </select>
                                <p class="text-[11px] text-gray-400 mt-1">Estado de operatividad (Campo obligatorio)</p>
                            </div>

                            <!-- Contraseña Opcional y Confirmación -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                                <div>
                                    <label for="edit_password" class="block text-xs font-bold text-gray-800 mb-1">
                                        Nueva Contraseña <span class="text-gray-400 font-normal">(Opcional)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               id="edit_password" 
                                               name="password"
                                               autocomplete="new-password"
                                               placeholder="Dejar en blanco para conservar"
                                               class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 pr-10 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all">
                                        <button type="button" 
                                                onclick="toggleInputPassword('edit_password', 'edit_eye_pwd')" 
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 cursor-pointer text-xs p-1">
                                            <i class="fas fa-eye" id="edit_eye_pwd"></i>
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Mínimo 8 caracteres si se desea cambiar</p>
                                </div>

                                <div>
                                    <label for="edit_password_confirmation" class="block text-xs font-bold text-gray-800 mb-1">
                                        Confirmar Contraseña
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               id="edit_password_confirmation" 
                                               name="password_confirmation"
                                               autocomplete="new-password"
                                               placeholder="Repite la nueva contraseña"
                                               class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 pr-10 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 focus:outline-none transition-all">
                                        <button type="button" 
                                                onclick="toggleInputPassword('edit_password_confirmation', 'edit_eye_conf')" 
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 cursor-pointer text-xs p-1">
                                            <i class="fas fa-eye" id="edit_eye_conf"></i>
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Requerido solo si se define nueva contraseña</p>
                                </div>
                            </div>

                            <!-- PASO OBLIGATORIO DE SEGURIDAD (SUPER ADMIN EN SESIÓN) -->
                            <div class="p-3.5 bg-amber-50/80 border border-amber-200 rounded-2xl space-y-1 mt-2 shadow-xs">
                                <div class="flex items-center gap-1.5 text-amber-900 font-extrabold text-xs">
                                    <i class="fas fa-lock text-amber-600"></i>
                                    <span>Paso de Seguridad Obligatorio</span>
                                </div>
                                <label for="edit_auth_password" class="block text-xs font-bold text-gray-800 pt-1">
                                    Contraseña de tu cuenta Super Admin actual en sesión <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="edit_auth_password" 
                                           name="auth_password" 
                                           required 
                                           placeholder="Ingresa tu contraseña para autorizar cambios" 
                                           class="w-full rounded-xl border border-amber-300 bg-white px-3.5 py-2.5 pr-10 text-sm text-gray-900 placeholder:text-gray-400 focus:border-amber-600 focus:ring-2 focus:ring-amber-600/20 focus:outline-none transition-all">
                                    <button type="button" 
                                            onclick="toggleInputPassword('edit_auth_password', 'edit_eye_auth')" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 cursor-pointer text-xs p-1">
                                        <i class="fas fa-eye" id="edit_eye_auth"></i>
                                    </button>
                                </div>
                                <p class="text-[11px] text-amber-800/80">Se requiere la verificación criptográfica del Super Administrador en sesión activa.</p>
                            </div>

                        </div>
                    </div>

                    <!-- Pie del Modal -->
                    <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" 
                                onclick="cerrarModalEditarSuperAdmin()" 
                                class="px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                            CANCELAR
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-black text-sm text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-indigo-600 hover:bg-indigo-700 shadow-indigo-600/25">
                            <i class="fas fa-check text-xs"></i>
                            <span>GUARDAR CAMBIOS</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function toggleInputPassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye-eye');
            }
        }

        function formatTelefonoInput(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            if (value.length > 4) {
                input.value = value.slice(0, 4) + '-' + value.slice(4);
            } else {
                input.value = value;
            }
        }

        function updateCreateSuperAdminPreview() {
            const nombre = document.getElementById('create_nombre_real')?.value.trim() || 'Nuevo Super Admin';
            const username = document.getElementById('create_username')?.value.trim() || 'superadmin@axstore.com';
            const telefono = document.getElementById('create_telefono')?.value.trim() || '';
            const estado = document.getElementById('create_estado')?.value;

            document.getElementById('createPreviewName').textContent = nombre;
            document.getElementById('createPreviewUsername').textContent = username;
            document.getElementById('createPreviewInitial').textContent = nombre.charAt(0).toUpperCase() || 'S';

            const phoneBadge = document.getElementById('createPreviewPhoneBadge');
            const phoneText = document.getElementById('createPreviewPhoneText');
            if (telefono) {
                phoneText.textContent = '+503 ' + telefono;
                phoneBadge.classList.remove('hidden');
            } else {
                phoneBadge.classList.add('hidden');
            }

            const statusBadge = document.getElementById('createPreviewStatusBadge');
            const statusText = document.getElementById('createPreviewStatusText');
            if (estado === '1') {
                statusBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200';
                statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span><span>Habilitado</span>';
            } else {
                statusBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200';
                statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span><span>Deshabilitado</span>';
            }
        }

        function updateEditSuperAdminPreview() {
            const nombre = document.getElementById('editSuperAdminNombre')?.value.trim() || 'Super Admin';
            const username = document.getElementById('editSuperAdminUsername')?.value.trim() || 'superadmin@axstore.com';
            const telefono = document.getElementById('editSuperAdminTelefono')?.value.trim() || '';
            const estado = document.getElementById('editSuperAdminEstado')?.value;

            document.getElementById('editPreviewName').textContent = nombre;
            document.getElementById('editPreviewUsername').textContent = username;
            document.getElementById('editPreviewInitial').textContent = nombre.charAt(0).toUpperCase() || 'S';

            const phoneBadge = document.getElementById('editPreviewPhoneBadge');
            const phoneText = document.getElementById('editPreviewPhoneText');
            if (telefono) {
                phoneText.textContent = '+503 ' + telefono;
                phoneBadge.classList.remove('hidden');
            } else {
                phoneBadge.classList.add('hidden');
            }

            const statusBadge = document.getElementById('editPreviewStatusBadge');
            if (estado == '1') {
                statusBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200';
                statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span><span>Habilitado</span>';
            } else {
                statusBadge.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200';
                statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span><span>Deshabilitado</span>';
            }
        }

        function cerrarModalNuevoSuperAdmin() {
            document.getElementById('modalNuevoSuperAdmin').classList.add('hidden');
        }

        function cerrarModalEditarSuperAdmin() {
            document.getElementById('modalEditarSuperAdmin').classList.add('hidden');
        }

        function abrirModalAuditoria(userId, nombre, email) {
            document.getElementById('auditarNombreUsuario').textContent = nombre;
            document.getElementById('auditarEmailUsuario').textContent = email;
            document.getElementById('formAuditoria').action = `/superadmin/usuarios/${userId}/auditar`;
            document.getElementById('modalAuditoria').classList.remove('hidden');
        }

        function abrirModalEditarSuperAdmin(userId, nombre, email, telefono, estado) {
            document.getElementById('editSuperAdminNombre').value = nombre;
            document.getElementById('editSuperAdminUsername').value = email;
            document.getElementById('editSuperAdminTelefono').value = telefono || '';
            document.getElementById('editSuperAdminEstado').value = estado;
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirmation').value = '';
            document.getElementById('edit_auth_password').value = '';
            document.getElementById('formEditarSuperAdmin').action = `/superadmin/usuarios/${userId}/superadmin`;
            updateEditSuperAdminPreview();
            document.getElementById('modalEditarSuperAdmin').classList.remove('hidden');
        }
    </script>
</x-app>
