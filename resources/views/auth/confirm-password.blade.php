<x-app title="Confirmar Contraseña | AXStore">

    <div class="max-w-md mx-auto mt-10">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-8">

            {{-- Ícono --}}
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 rounded-2xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-shield-halved text-2xl text-amber-600"></i>
                </div>
            </div>

            <h2 class="text-xl font-black text-center text-slate-900 mb-2">Confirmar Contraseña</h2>
            <p class="text-xs text-slate-500 text-center mb-6">
                Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="mb-5">
                    <label for="password" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                        Contraseña
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autofocus
                           autocomplete="current-password"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                           placeholder="Ingresa tu contraseña">

                    @error('password')
                        <p class="text-xs text-red-500 mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-widest shadow-lg shadow-blue-600/20 transition-all duration-200">
                    <i class="fas fa-lock-open mr-2"></i>
                    Confirmar y Continuar
                </button>
            </form>

        </div>
    </div>

</x-app>

