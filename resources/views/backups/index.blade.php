<x-app title="Respaldos de Base de Datos | AXStore">

    <div class="max-w-[1440px] mx-auto space-y-6">

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
                                                <p class="text-[10px] text-slate-400 mt-0.5">Base de datos MySQL</p>
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
                                        <a href="{{ route('backups.download', $file['name']) }}"
                                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold shadow-sm transition-all duration-200"
                                           title="Descargar respaldo">
                                            <i class="fas fa-download text-[10px]"></i>
                                            Descargar
                                        </a>
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

        {{-- 4. INFORMACIÓN --}}
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
                    <p>• Se envía una notificación por correo al generar o si falla un respaldo.</p>
                </div>
            </div>
        </div>

    </div>

</x-app>

