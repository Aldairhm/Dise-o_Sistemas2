<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50/60">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detalle de Movimiento #{{ $bitacora->id }} — AXStore</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/home.css', 'resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-800 antialiased font-['Plus_Jakarta_Sans',sans-serif] bg-slate-50 flex flex-col">

    <!-- HEADER -->
    <header class="bg-white/85 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 transition-all duration-300">
        @include('componentsHome.header')
    </header>

    <!-- MAIN -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('bitacora.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-teal-600 hover:text-teal-700 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Volver a la Bitácora</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-600">{{ $bitacora->modulo }}</span>
                    <h1 class="text-2xl font-black text-slate-900 mt-1 flex items-center gap-2">
                        <span>Registro #{{ $bitacora->id }}</span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full uppercase font-bold bg-slate-100 text-slate-700">{{ $bitacora->accion }}</span>
                    </h1>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold text-slate-700">{{ $bitacora->created_at->format('d/m/Y h:i:s A') }}</div>
                    <div class="text-[11px] text-slate-400">{{ $bitacora->created_at->diffForHumans() }}</div>
                </div>
            </div>

            <!-- Datos Generales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6 p-4 bg-slate-50 rounded-xl text-xs">
                <div>
                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Usuario</span>
                    <span class="font-bold text-slate-800">{{ $bitacora->nombre_usuario ?: ($bitacora->username ?: 'Sistema') }} ({{ $bitacora->rol_usuario ?: 'N/A' }})</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Dirección IP</span>
                    <span class="font-mono font-bold text-slate-800">{{ $bitacora->ip_address ?: '127.0.0.1' }}</span>
                </div>
                <div class="sm:col-span-2">
                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Navegador / User Agent</span>
                    <span class="font-mono text-slate-600 text-[11px] break-all">{{ $bitacora->user_agent ?: 'N/A' }}</span>
                </div>
            </div>

            <!-- Descripción -->
            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Descripción</label>
                <div class="p-4 bg-slate-100/70 rounded-xl text-sm text-slate-800">
                    {{ $bitacora->descripcion }}
                </div>
            </div>

            <!-- Cambios JSON antes/después si existen -->
            @if($bitacora->datos_anteriores || $bitacora->datos_nuevos)
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Snapshot de Datos</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-rose-50/50 border border-rose-200 rounded-xl">
                        <span class="text-xs font-bold text-rose-700 uppercase tracking-wider block mb-2">Estado Anterior</span>
                        <pre class="text-xs font-mono text-slate-700 whitespace-pre-wrap">{{ json_encode($bitacora->datos_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '(Sin datos anteriores)' }}</pre>
                    </div>

                    <div class="p-4 bg-emerald-50/50 border border-emerald-200 rounded-xl">
                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block mb-2">Estado Nuevo</span>
                        <pre class="text-xs font-mono text-slate-700 whitespace-pre-wrap">{{ json_encode($bitacora->datos_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '(Sin datos nuevos)' }}</pre>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </main>
</body>
</html>
