<x-app title="Respaldos de Base de Datos | AXStore">

    <div class="max-w-[1440px] mx-auto space-y-6"
         x-data="backupManager()">

        {{-- 1. ENCABEZADO --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-blue-600 transition-colors mb-3">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-600/20">
                        <i class="fas fa-database text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-black uppercase tracking-[0.18em] text-emerald-600">Seguridad</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700">
                                MySQL
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            Respaldos de Base de Datos
                        </h1>
                    </div>
                </div>
            </div>

            {{-- BOTONES DE ACCIÓN RÁPIDA: RESTAURAR + GENERAR --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- Botón Restaurar Base de Datos --}}
                <button type="button"
                        @click="openRestoreModal('upload')"
                        class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-widest shadow-lg shadow-amber-500/20 transition-all duration-200 cursor-pointer">
                    <i class="fas fa-rotate-left text-sm"></i>
                    <span>Restaurar Base de Datos</span>
                </button>

                {{-- Botón Generar Respaldo --}}
                <div x-data="{ loading: false }">
                    <form action="{{ route('backups.create') }}" method="POST"
                          @submit="loading = true">
                        @csrf
                        <button type="submit"
                                :disabled="loading"
                                class="inline-flex items-center gap-2.5 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-widest shadow-lg shadow-emerald-600/20 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed">
                            <template x-if="!loading">
                                <span class="flex items-center gap-2.5">
                                    <i class="fas fa-plus-circle text-sm"></i>
                                    Generar Nuevo Respaldo
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-2.5">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Generando respaldo...
                                </span>
                            </template>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- 2. KPI CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
            {{-- Total de respaldos --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="h-1 w-full bg-blue-600 rounded-full -mt-5 mb-4 -mx-0 rounded-t-2xl" style="margin-left:-1.25rem; margin-right:-1.25rem; margin-top:-1.25rem; width:calc(100% + 2.5rem); border-radius: 1rem 1rem 0 0;"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Respaldos</p>
                        <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ count($files) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-archive text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Último respaldo --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="h-1 w-full bg-emerald-600 rounded-full -mt-5 mb-4 -mx-0 rounded-t-2xl" style="margin-left:-1.25rem; margin-right:-1.25rem; margin-top:-1.25rem; width:calc(100% + 2.5rem); border-radius: 1rem 1rem 0 0;"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Último Respaldo</p>
                        <p class="text-sm font-bold text-slate-900 mt-1">
                            {{ count($files) > 0 ? $files[0]['date_human'] : 'Sin respaldos' }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-clock text-emerald-600"></i>
                    </div>
                </div>
            </div>

            {{-- Espacio utilizado --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="h-1 w-full bg-amber-500 rounded-full -mt-5 mb-4 -mx-0 rounded-t-2xl" style="margin-left:-1.25rem; margin-right:-1.25rem; margin-top:-1.25rem; width:calc(100% + 2.5rem); border-radius: 1rem 1rem 0 0;"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Espacio Total</p>
                        <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                            @php
                                $totalBytes = collect($files)->sum('size_raw');
                                $units = ['B', 'KB', 'MB', 'GB'];
                                $pow = $totalBytes > 0 ? floor(log($totalBytes) / log(1024)) : 0;
                                $pow = min($pow, count($units) - 1);
                            @endphp
                            {{ round($totalBytes / pow(1024, $pow), 2) }} {{ $units[$pow] }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i class="fas fa-hdd text-amber-500"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. TABLA DE RESPALDOS --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

            {{-- Header de la tabla --}}
            <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                        <i class="fas fa-list text-slate-500 text-xs"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Historial de Respaldos</h2>
                </div>
                <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">
                    {{ count($files) }} {{ count($files) === 1 ? 'archivo' : 'archivos' }}
                </span>
            </div>

            @if(count($files) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80">
                                <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">#</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Archivo</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tamaño</th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-5 py-3 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($files as $index => $file)
                                <tr class="hover:bg-slate-50/60 transition-colors text-xs text-slate-700">
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 text-[10px] font-bold text-slate-500">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-file-zipper text-emerald-600 text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 text-xs">{{ $file['name'] }}</p>
                                                <p class="text-[10px] text-slate-400 mt-0.5">Base de datos MySQL (.zip)</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold">
                                            <i class="fas fa-weight-hanging text-[9px]"></i>
                                            {{ $file['size'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-slate-700">{{ $file['date'] }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $file['date_human'] }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            {{-- Descargar --}}
                                            <a href="{{ route('backups.download', $file['name']) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold shadow-sm transition-all duration-200"
                                               title="Descargar archivo .zip">
                                                <i class="fas fa-download text-[10px]"></i>
                                                <span>Descargar</span>
                                            </a>

                                            {{-- Restaurar este respaldo --}}
                                            <button type="button"
                                                    @click="openRestoreModal('existing', '{{ $file['name'] }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-bold shadow-sm transition-all duration-200 cursor-pointer"
                                                    title="Restaurar este respaldo en la base de datos">
                                                <i class="fas fa-rotate-left text-[10px]"></i>
                                                <span>Restaurar</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Estado vacío --}}
                <div class="px-5 py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-database text-2xl text-slate-300"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-500 mb-1">No hay respaldos aún</h3>
                    <p class="text-xs text-slate-400">
                        Genera tu primer respaldo de base de datos haciendo clic en el botón
                        <span class="font-bold text-emerald-600">"Generar Nuevo Respaldo"</span>.
                    </p>
                </div>
            @endif
        </div>

        {{-- 4. INFORMACIÓN DE SEGURIDAD Y RETENCIÓN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-info-circle text-blue-600 text-xs"></i>
                    </div>
                    <div class="text-xs text-slate-500 space-y-1">
                        <p class="font-bold text-slate-700">Política de retención</p>
                        <p>• Se conservan respaldos diarios de los <strong class="text-slate-700">últimos 7 días</strong>.</p>
                        <p>• Se conserva <strong class="text-slate-700">1 copia semanal</strong> durante 1 mes.</p>
                        <p>• Máximo aproximado: <strong class="text-slate-700">~11 archivos</strong> en disco.</p>
                        <p>• Sincronización automática simultánea con Google Drive.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-shield-halved text-amber-600 text-xs"></i>
                    </div>
                    <div class="text-xs text-slate-500 space-y-1">
                        <p class="font-bold text-slate-700">Restauración de Base de Datos</p>
                        <p>• Puedes restaurar cualquier respaldo del historial con un solo clic.</p>
                        <p>• También puedes subir scripts directos <strong class="text-slate-700">.sql</strong> o respaldos <strong class="text-slate-700">.zip</strong>.</p>
                        <p>• Se exige la confirmación con tu contraseña de administrador para prevenir accidentes.</p>
                        <p>• Cada operación queda registrada de forma auditable en la bitácora del sistema.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             MODAL DE CONFIRMACIÓN Y RESTAURACIÓN DE BASE DE DATOS
        ═══════════════════════════════════════════════════════════════════ --}}
        <div x-show="restoreModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">

            {{-- Backdrop --}}
            <div x-show="restoreModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                 @click="if(!restoreLoading) restoreModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="restoreModalOpen"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200/80 p-6 sm:p-7">

                    {{-- Formulario de Restauración --}}
                    <form action="{{ route('backups.restore') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          @submit="restoreLoading = true">
                        @csrf

                        {{-- Encabezado Modal --}}
                        <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-triangle-exclamation text-xl"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-600">Acción Crítica</span>
                                    <h3 class="text-lg font-black text-slate-900" id="modal-title">
                                        Restaurar Base de Datos MySQL
                                    </h3>
                                </div>
                            </div>
                            <button type="button"
                                    :disabled="restoreLoading"
                                    @click="restoreModalOpen = false"
                                    class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition-colors">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>

                        {{-- Banner de Advertencia --}}
                        <div class="mt-4 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 leading-relaxed">
                            <p class="font-bold mb-1 flex items-center gap-1.5">
                                <i class="fas fa-triangle-exclamation text-amber-600"></i>
                                ¡Advertencia importante!
                            </p>
                            Esta operación sobrescribirá los datos actuales de la base de datos con la información del respaldo seleccionado. Cualquier cambio no respaldado se perderá de forma permanente.
                        </div>

                        {{-- Selección del Origen del Respaldo --}}
                        <div class="mt-5 space-y-4">
                            {{-- Pestañas de modo --}}
                            <div class="flex items-center p-1 rounded-xl bg-slate-100 text-xs font-bold">
                                <button type="button"
                                        @click="restoreMode = 'existing'"
                                        :class="restoreMode === 'existing' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                        class="flex-1 py-1.5 px-3 rounded-lg transition-all text-center">
                                    <i class="fas fa-server mr-1"></i> Respaldo del Historial
                                </button>
                                <button type="button"
                                        @click="restoreMode = 'upload'"
                                        :class="restoreMode === 'upload' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                        class="flex-1 py-1.5 px-3 rounded-lg transition-all text-center">
                                    <i class="fas fa-upload mr-1"></i> Subir Archivo (.sql / .zip)
                                </button>
                            </div>

                            {{-- MODO A: RESIDUAL EXISTENTE --}}
                            <template x-if="restoreMode === 'existing'">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Archivo a restaurar:
                                    </label>

                                    @if(count($files) > 0)
                                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-file-zipper text-sm"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-black text-slate-900 truncate" x-text="selectedFileName || '{{ $files[0]['name'] }}'"></p>
                                                    <p class="text-[10px] text-slate-400 mt-0.5">Almacenado localmente en el servidor</p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Selector si quiere cambiar de archivo del historial --}}
                                        <div class="pt-1">
                                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                                                Cambiar por otro respaldo del historial:
                                            </label>
                                            <select x-model="selectedFileName"
                                                    class="w-full text-xs rounded-xl border border-slate-300 bg-white px-3 py-2 font-medium text-slate-700 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                                                @foreach($files as $f)
                                                    <option value="{{ $f['name'] }}">{{ $f['name'] }} ({{ $f['date'] }}) - {{ $f['size'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <input type="hidden" name="file_name" :value="selectedFileName || '{{ $files[0]['name'] }}'">
                                    @else
                                        <div class="p-4 rounded-xl bg-slate-50 text-center text-xs text-slate-500">
                                            No hay respaldos locales disponibles. Usa la opción de <strong>Subir Archivo</strong>.
                                        </div>
                                    @endif
                                </div>
                            </template>

                            {{-- MODO B: SUBIR ARCHIVO NUEVO --}}
                            <template x-if="restoreMode === 'upload'">
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Selecciona tu script .sql o respaldo .zip:
                                    </label>
                                    <input type="file"
                                           name="backup_file"
                                           accept=".sql,.zip"
                                           :required="restoreMode === 'upload'"
                                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 border border-slate-300 rounded-xl cursor-pointer bg-white">
                                    <p class="text-[10px] text-slate-400">
                                        Tamaño máximo admitido: 50MB. Admite .sql puros o archivos .zip exportados por AXStore.
                                    </p>
                                </div>
                            </template>

                            {{-- Confirmación con Contraseña de Administrador --}}
                            <div class="pt-2 border-t border-slate-100 space-y-1.5">
                                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    Contraseña de Administrador <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="password"
                                           name="password"
                                           required
                                           autocomplete="current-password"
                                           placeholder="Ingresa tu contraseña para autorizar"
                                           class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                                </div>
                                <p class="text-[10px] text-slate-400">
                                    Por seguridad corporativa, debes confirmar tu identidad para aplicar la restauración.
                                </p>
                            </div>
                        </div>

                        {{-- Botones del Modal --}}
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="button"
                                    :disabled="restoreLoading"
                                    @click="restoreModalOpen = false"
                                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
                                Cancelar
                            </button>

                            <button type="submit"
                                    :disabled="restoreLoading"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-amber-500/20 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                                <template x-if="!restoreLoading">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-rotate-left"></i>
                                        <span>Confirmar y Restaurar</span>
                                    </span>
                                </template>
                                <template x-if="restoreLoading">
                                    <span class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        <span>Restaurando base de datos...</span>
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

    {{-- Script Alpine del Gestor de Respaldos --}}
    <script>
        function backupManager() {
            return {
                restoreModalOpen: false,
                restoreMode: 'upload', // 'existing' | 'upload'
                selectedFileName: @json($files[0]['name'] ?? ''),
                restoreLoading: false,

                openRestoreModal(mode, fileName = '') {
                    this.restoreMode = mode;
                    if (fileName) {
                        this.selectedFileName = fileName;
                    }
                    this.restoreModalOpen = true;
                }
            };
        }
    </script>

</x-app>
