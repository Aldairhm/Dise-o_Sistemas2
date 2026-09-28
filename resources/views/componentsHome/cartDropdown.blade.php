{{-- Bolsa de Compras estilo Apple (Popover / Dropdown) --}}
<div class="cart-dropdown relative inline-flex items-center" id="ax-cart-container">
    {{-- Botón Trigger del Carrito --}}
    <button type="button" 
            id="ax-cart-btn" 
            onclick="window.AXCart.toggleDropdown(event)" 
            class="relative p-2 text-gray-600 hover:text-blue-600 transition-transform hover:scale-105 block focus:outline-none cursor-pointer" 
            title="Tu Bolsa de Compras"
            aria-expanded="false"
            aria-label="Abrir bolsa de compras">
        <i class="fas fa-shopping-cart text-xl"></i>
        {{-- Badge con contador dinámico de artículos --}}
        <span id="ax-cart-badge" 
              class="absolute -top-0.5 -right-0.5 bg-blue-600 text-white text-[10px] font-black h-4 min-w-[16px] px-1 rounded-full flex items-center justify-center shadow-sm transition-all duration-300 transform scale-0 opacity-0 pointer-events-none">
            0
        </span>
    </button>

    {{-- Menú Desplegable estilo Apple Bag --}}
    <div id="ax-cart-menu" 
         onclick="event.stopPropagation()"
         class="absolute top-[calc(100%+12px)] -right-12 sm:right-0 w-[min(380px,calc(100vw-24px))] bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/90 overflow-hidden z-50 transition-all duration-300 transform origin-top-right opacity-0 invisible -translate-y-2 pointer-events-none text-left">
        
        {{-- Flecha decorativa superior alineada al botón del carrito --}}
        <div class="absolute -top-1.5 right-14 sm:right-3.5 w-3 h-3 bg-white border-t border-l border-slate-200 transform rotate-45 pointer-events-none"></div>

        {{-- Encabezado de la Bolsa --}}
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-2">
                <i class="fas fa-bag-shopping text-blue-600 text-sm"></i>
                <h3 class="text-sm font-black text-slate-800">Tu Bolsa</h3>
                <span id="ax-cart-header-count" class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-full">
                    0 artículos
                </span>
            </div>
            <button type="button" 
                    onclick="window.AXCart.closeDropdown()" 
                    class="text-slate-400 hover:text-slate-600 p-1 rounded-lg text-xs transition-colors cursor-pointer" 
                    title="Cerrar bolsa">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Contenido cuando hay productos en la bolsa --}}
        <div id="ax-cart-filled" class="hidden">
            {{-- Lista con scroll de artículos --}}
            <div id="ax-cart-items-list" class="max-h-[260px] overflow-y-auto divide-y divide-slate-100 p-2 space-y-1">
                {{-- Renderizado reactivamente con window.AXCart --}}
            </div>

            {{-- Pie de la bolsa: Subtotal y Botones Apple --}}
            <div class="p-4 bg-slate-50/90 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-500 uppercase tracking-wider text-[11px]">Subtotal:</span>
                    <span id="ax-cart-subtotal" class="text-base font-black text-slate-900">$0.00</span>
                </div>

                <div class="space-y-2 pt-1">
                    {{-- Botón Principal: Pagar / Revisar bolsa --}}
                    <button type="button" 
                       onclick="window.AXCart.openEntregaModal()" 
                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-lg shadow-blue-500/25 transition-all duration-200 hover:scale-[1.01] active:scale-[0.99] text-center cursor-pointer">
                        <span>Revisar Bolsa y Pagar</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>

                    {{-- Botón Secundario: Seguir comprando --}}
                    <button type="button" 
                            onclick="window.AXCart.closeDropdown(); document.querySelector('#productos')?.scrollIntoView({behavior: 'smooth'});" 
                            class="w-full py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs transition-colors text-center cursor-pointer">
                        Seguir comprando
                    </button>
                </div>
            </div>
        </div>

        {{-- Estado vacío estilo Apple --}}
        <div id="ax-cart-empty" class="p-8 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mb-3 shadow-inner">
                <i class="fas fa-basket-shopping"></i>
            </div>
            <p class="text-sm font-black text-slate-800">Tu bolsa está vacía</p>
            <p class="text-xs text-slate-400 mt-1 max-w-[220px] mx-auto">
                Los productos que agregues desde el catálogo aparecerán aquí.
            </p>
            <div class="mt-4">
                <button type="button" 
                        onclick="window.AXCart.closeDropdown(); document.querySelector('#productos')?.scrollIntoView({behavior: 'smooth'});"
                        class="px-4 py-2 bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 text-xs font-bold rounded-xl transition-all cursor-pointer">
                    Seguir comprando
                </button>
            </div>
        </div>

    </div>

    {{-- Modal "Registrar Entrega de Producto" --}}
    @include('componentsHome.modalEntregaVenta')
</div>
