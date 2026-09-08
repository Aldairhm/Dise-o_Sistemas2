<div id="userModal" class="fixed inset-0 z-[120] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop oscuro con blur -->
    <div class="fixed inset-0 bg-slate-950/65 backdrop-blur-xs transition-opacity" onclick="closeUserModal()"></div>

    <div class="flex min-h-screen items-center justify-center p-3 sm:p-5 text-center">
        <!-- Contenedor Principal -->
        <div class="relative w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-gray-100 flex flex-col my-8">

            <!-- ─── CABECERA  ─── -->
            <div class="bg-blue-600 px-6 py-4.5 flex items-center justify-between text-white shadow-md">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center text-white text-sm shadow-xs">
                        <i class="fas fa-circle-check" id="modalHeaderIcon"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-black tracking-tight" id="modalTitle">
                        Registrar Nuevo Usuario
                    </h3>
                </div>
                <!-- Boton -->
                <button type="button" 
                        onclick="closeUserModal()" 
                        class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition-colors cursor-pointer"
                        title="Cerrar ventana">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Formulario Principal -->
            <form id="userForm" onsubmit="handleUserSubmit(event)">
                @csrf
                <input type="hidden" id="userId" name="user_id" value="">
                <input type="hidden" id="_method" name="_method" value="POST">

                <!-- ─── CUERPO ─── -->
                <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-12 gap-6 sm:gap-8 max-h-[75vh] overflow-y-auto custom-scrollbar">

                    <!-- ─── NFORMACION DEL USUARIO ─── -->
                    <div class="md:col-span-5 flex flex-col">
                        <h4 class="text-[11px] font-black uppercase tracking-wider text-gray-500 mb-3.5">
                            INFORMACIÓN DEL USUARIO
                        </h4>

                        <!-- Tarjeta de previsualizacion -->
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-5 flex flex-col items-center justify-center text-center relative overflow-hidden flex-1 shadow-inner">
                            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue-500 via-cyan-400 to-indigo-500"></div>

                            <!-- Avatar dinamico -->
                            <div id="previewAvatar" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-500 text-white flex items-center justify-center text-4xl font-black shadow-lg shadow-blue-500/25 mb-4 transition-transform hover:scale-105">
                                <span id="previewInitial">U</span>
                            </div>

                            <!-- Nombre en tiempo real -->
                            <h5 id="previewName" class="text-base font-extrabold text-gray-900 leading-tight">
                                Nuevo Usuario
                            </h5>
                            <p id="previewUsername" class="text-xs text-gray-500 mt-0.5 break-all">
                                usuario@axstore.com
                            </p>

                            <!-- Insignias de Rol, Estado y Teléfono -->
                            <div class="flex items-center gap-2 mt-3.5 flex-wrap justify-center">
                                <span id="previewRoleBadge" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                    <i class="fas fa-tag text-[9px]"></i> <span id="previewRoleText">Vendedor</span>
                                </span>
                                <span id="previewStatusBadge" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span id="previewStatusText">Habilitado</span>
                                </span>
                                <span id="previewPhoneBadge" class="hidden inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    <i class="fas fa-phone text-[9px]"></i> <span id="previewPhoneText"></span>
                                </span>
                            </div>

                            <!-- Resumen informativo de seguridad -->
                            <div class="w-full mt-5 pt-4 border-t border-gray-200/60 text-left text-xs space-y-2 text-gray-600">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400 text-[11px] font-medium">Plataforma:</span>
                                    <span class="font-bold text-gray-800">AXStore Cloud</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400 text-[11px] font-medium">Autenticación:</span>
                                    <span class="font-bold text-emerald-600 flex items-center gap-1">
                                        <i class="fas fa-shield-halved text-[10px]"></i> Cifrado Bcrypt
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400 text-[11px] font-medium">Acceso:</span>
                                    <span class="font-bold text-gray-800">Panel Web / App</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ─── DATOS DEL USUARIO ─── -->
                    <div class="md:col-span-7 flex flex-col space-y-4">
                        <h4 class="text-[11px] font-black uppercase tracking-wider text-gray-500 mb-0.5">
                            DATOS DEL USUARIO
                        </h4>

                        <!-- Banner de error -->
                        <div id="modalAlertError" class="hidden rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-700 flex items-start gap-2.5">
                            <i class="fas fa-circle-exclamation text-rose-500 mt-0.5 flex-shrink-0"></i>
                            <div id="modalAlertErrorText">Por favor verifica los errores en el formulario.</div>
                        </div>

                        <!-- Nombre Completo -->
                        <div class="form-field-group">
                            <label for="input_nombre_real" class="block text-xs font-bold text-gray-800 mb-1">
                                Nombre Completo <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="input_nombre_real" 
                                       name="nombre_real" 
                                       required
                                       placeholder="Ej. Juan Pérez"
                                       class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all"
                                       oninput="updatePreviewCard()">
                            </div>
                            <!-- Texto de campo obligatorio -->
                            <p class="text-[11px] text-gray-400 mt-1">Nombre y apellidos reales del usuario (Campo obligatorio)</p>
                        </div>

                        <!-- Teléfono -->
                        <div class="form-field-group">
                            <label for="input_telefono" class="block text-xs font-bold text-gray-800 mb-1">
                                Teléfono <span class="font-normal text-gray-400 ml-1">(opcional)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center gap-1 text-gray-400 text-xs font-bold select-none">
                                    <img src="https://flagcdn.com/w20/sv.png" alt="SV" class="h-3.5 rounded-sm opacity-90">
                                    <span>+503</span>
                                </div>
                                <input type="text" 
                                       id="input_telefono" 
                                       name="telefono"
                                       maxlength="9"
                                       placeholder="XXXX-XXXX"
                                       class="w-full rounded-xl border border-gray-300 pr-10 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all"
                                       style="padding-left: 5.2rem;"
                                       oninput="formatTelefonoInput(this); updatePreviewCard()">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2" id="modal-telefono-icon"></div>
                            </div>
                            <p id="modal-telefono-msg" class="text-[11px] text-gray-400 mt-1">Formato: XXXX-XXXX (ej: 7890-1234)</p>
                        </div>

                        <!-- 2. Correo Electronico  -->
                        <div class="form-field-group">
                            <label for="input_username" class="block text-xs font-bold text-gray-800 mb-1">
                                Correo Electrónico o Usuario <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="input_username" 
                                       name="username" 
                                       required
                                       placeholder="Ej. juan@axstore.com o juanperez"
                                       class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all"
                                       oninput="updatePreviewCard()">
                            </div>
                            <!-- Texto de campo obligatorio -->
                            <p class="text-[11px] text-gray-400 mt-1">Identificador único para iniciar sesión en la plataforma (Campo obligatorio)</p>
                        </div>

                        <!-- Rol y Estado -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Rol -->
                            <div class="form-field-group">
                                <label for="input_rol" class="block text-xs font-bold text-gray-800 mb-1">
                                    Rol de Acceso <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <select id="input_rol" 
                                        name="rol" 
                                        required
                                        class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all bg-white"
                                        onchange="updatePreviewCard()">
                                    <option value="vendedor" selected>Vendedor</option>
                                    <option value="admin">Administrador</option>
                                </select>
                                <p class="text-[11px] text-gray-400 mt-1">Nivel de privilegios (Campo obligatorio)</p>
                            </div>

                            <!-- Estado -->
                            <div class="form-field-group">
                                <label for="input_estado" class="block text-xs font-bold text-gray-800 mb-1">
                                    Estado de la Cuenta <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <select id="input_estado" 
                                        name="estado" 
                                        required
                                        class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm text-gray-900 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all bg-white"
                                        onchange="updatePreviewCard()">
                                    <option value="1" selected>Habilitado</option>
                                    <option value="0">Deshabilitado (Suspendido)</option>
                                </select>
                                <p class="text-[11px] text-gray-400 mt-1">Estado de operatividad (Campo obligatorio)</p>
                            </div>
                        </div>

                        <!-- Contraseña y Confirmacion -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                            <!-- Contraseña -->
                            <div class="form-field-group">
                                <label for="input_password" class="block text-xs font-bold text-gray-800 mb-1">
                                    Contraseña <span id="pwdRequiredIndicator" class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="input_password" 
                                           name="password" 
                                           required
                                           placeholder="••••••••"
                                           class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 pr-10 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all">
                                    <button type="button" 
                                            onclick="toggleModalPassword('input_password', 'eye_pwd')" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 cursor-pointer text-xs p-1">
                                        <i class="fas fa-eye" id="eye_pwd"></i>
                                    </button>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1" id="pwdHelpText">Mínimo 8 caracteres (Campo obligatorio)</p>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="form-field-group">
                                <label for="input_password_confirmation" class="block text-xs font-bold text-gray-800 mb-1">
                                    Confirmar Contraseña <span id="pwdConfRequiredIndicator" class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="input_password_confirmation" 
                                           name="password_confirmation" 
                                           required
                                           placeholder="••••••••"
                                           class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 pr-10 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 focus:outline-none transition-all">
                                    <button type="button" 
                                            onclick="toggleModalPassword('input_password_confirmation', 'eye_conf')" 
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 cursor-pointer text-xs p-1">
                                        <i class="fas fa-eye" id="eye_conf"></i>
                                    </button>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Repite la contraseña exacta (Campo obligatorio)</p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ─── PIE DEL MODAL ─── -->
                <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-2xl">
                    <!-- Botón CANCELAR -->
                    <button type="button" 
                            onclick="closeUserModal()" 
                            class="px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                        CANCELAR
                    </button>

                    <!-- Boton REGISTRAR / GUARDAR -->
                    <button type="submit" 
                            id="btnSubmitModal"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-black text-sm text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 shadow-slate-900/20">
                        <i class="fas fa-check text-xs"></i>
                        <span id="btnSubmitText">REGISTRAR USUARIO</span>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
