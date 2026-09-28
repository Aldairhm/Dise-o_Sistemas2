/**
 * AXStore Cart Engine — Lógica de Bolsa de Compras reactiva estilo Apple.
 * Gestiona el carrito en localStorage, sincronización global, contador dinámico
 * y apertura del menú desplegable de la bolsa de compras.
 */
class CartManager {
    constructor() {
        this.storageKey = 'ax_carrito';
        this.cart = this.loadCart();
        this.initListeners();
    }

    loadCart() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            if (!raw) return [];
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            console.error('Error al cargar carrito:', e);
            return [];
        }
    }

    saveCart() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.cart));
            this.emitUpdate();
        } catch (e) {
            console.error('Error al guardar carrito:', e);
        }
    }

    emitUpdate() {
        window.dispatchEvent(new CustomEvent('ax-cart-updated', { detail: this.cart }));
    }

    getItems() {
        return this.cart;
    }

    getTotalCount() {
        return this.cart.reduce((total, item) => total + (Number(item.cantidad) || 0), 0);
    }

    getSubtotal() {
        return this.cart.reduce((total, item) => {
            const precio = Number(item.precio_venta || item.precio || 0);
            const cant = Number(item.cantidad || 0);
            const extra = Number(item.costo_extra || 0);
            return total + (precio * cant + extra);
        }, 0);
    }

    /**
     * Agrega un producto / variante a la bolsa con validación de stock
     */
    addItem(item) {
        if (!item || !item.id) {
            console.warn('Ítem inválido para el carrito:', item);
            return false;
        }

        const id = Number(item.id);
        const stockDisponible = Number(item.stock ?? 9999);
        const existing = this.cart.find(i => Number(i.id) === id);

        if (stockDisponible <= 0) {
            this.showToast(`El producto "${item.producto || 'seleccionado'}" está agotado en tienda.`, 'error');
            return false;
        }

        if (existing) {
            if (existing.cantidad + 1 > stockDisponible) {
                this.showToast(`Stock máximo alcanzado para "${item.producto || 'este producto'}" (${stockDisponible} uds disponibles).`, 'warning');
                return false;
            }
            existing.cantidad += 1;
        } else {
            this.cart.push({
                id: item.id,
                producto: item.producto || 'Producto',
                variante: item.variante || '',
                sku: item.sku || '',
                precio_venta: Number(item.precio_venta || item.precio || 0),
                stock: stockDisponible,
                imagen: item.imagen || item.imgUrl || null,
                cantidad: Number(item.cantidad || 1),
                costo_extra: Number(item.costo_extra || 0),
            });
        }

        this.saveCart();
        this.showToast(`"${item.producto || 'Producto'}" se agregó a tu bolsa.`, 'success');
        this.openDropdown();
        return true;
    }

    /**
     * Actualiza la cantidad de un producto
     */
    updateQuantity(id, newQty) {
        const item = this.cart.find(i => Number(i.id) === Number(id));
        if (!item) return;

        newQty = parseInt(newQty, 10);
        if (isNaN(newQty) || newQty <= 0) {
            this.removeItem(id);
            return;
        }

        if (item.stock && newQty > item.stock) {
            this.showToast(`Solo hay ${item.stock} unidades disponibles en tienda.`, 'warning');
            item.cantidad = item.stock;
        } else {
            item.cantidad = newQty;
        }

        this.saveCart();
    }

    /**
     * Actualiza el costo extra de un producto
     */
    updateCostoExtra(id, extra) {
        const item = this.cart.find(i => Number(i.id) === Number(id));
        if (!item) return;

        const val = Math.max(0, parseFloat(extra) || 0);
        item.costo_extra = val;
        this.saveCart();
    }

    /**
     * Remueve un ítem de la bolsa
     */
    removeItem(id) {
        this.cart = this.cart.filter(i => Number(i.id) !== Number(id));
        this.saveCart();
    }

    /**
     * Vacía completamente la bolsa
     */
    clearCart() {
        this.cart = [];
        this.saveCart();
    }

    /**
     * Abre el dropdown de la bolsa de compras
     */
    openDropdown() {
        const menu = document.getElementById('ax-cart-menu');
        const btn = document.getElementById('ax-cart-btn');
        if (!menu) return;
        menu.classList.remove('opacity-0', 'invisible', '-translate-y-2', 'pointer-events-none');
        menu.classList.add('opacity-100', 'visible', 'translate-y-0', 'pointer-events-auto');
        if (btn) btn.setAttribute('aria-expanded', 'true');
    }

    /**
     * Cierra el dropdown de la bolsa de compras
     */
    closeDropdown() {
        const menu = document.getElementById('ax-cart-menu');
        const btn = document.getElementById('ax-cart-btn');
        if (!menu) return;
        menu.classList.add('opacity-0', 'invisible', '-translate-y-2', 'pointer-events-none');
        menu.classList.remove('opacity-100', 'visible', 'translate-y-0', 'pointer-events-auto');
        if (btn) btn.setAttribute('aria-expanded', 'false');
    }

    /**
     * Abre el modal de Registrar Entrega de Producto para procesar la venta
     */
    openEntregaModal() {
        if (this.getTotalCount() === 0) {
            this.showToast('Tu bolsa está vacía. Agrega productos antes de pagar.', 'warning');
            return;
        }
        this.closeDropdown();
        window.dispatchEvent(new CustomEvent('abrir-modal-entrega', { detail: this.cart }));
    }

    /**
     * Alterna la visibilidad del dropdown
     */
    toggleDropdown(event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        const menu = document.getElementById('ax-cart-menu');
        if (!menu) return;
        if (menu.classList.contains('opacity-0')) {
            this.openDropdown();
        } else {
            this.closeDropdown();
        }
    }

    initListeners() {
        window.addEventListener('storage', (e) => {
            if (e.key === this.storageKey) {
                this.cart = this.loadCart();
                this.render();
            }
        });

        window.addEventListener('ax-cart-updated', (e) => {
            if (e.detail && Array.isArray(e.detail)) {
                this.cart = e.detail;
            } else {
                this.cart = this.loadCart();
            }
            this.render();
        });

        // Cerrar al hacer clic fuera del dropdown
        document.addEventListener('click', (e) => {
            const container = document.getElementById('ax-cart-container');
            const menu = document.getElementById('ax-cart-menu');
            if (!container || !menu) return;

            // Si el menú no está visible, ignorar
            if (menu.classList.contains('opacity-0') || menu.classList.contains('invisible')) {
                return;
            }

            // Validar recorrido del evento (evita falsos clics externos si se redibuja el DOM)
            const path = (typeof e.composedPath === 'function') ? e.composedPath() : [];
            if (path.length > 0) {
                if (path.includes(container) || path.includes(menu)) {
                    return;
                }
            } else {
                if (container.contains(e.target) || menu.contains(e.target)) {
                    return;
                }
            }

            this.closeDropdown();
        });

        // Inicializar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.render());
        } else {
            this.render();
        }
    }

    formatMoney(val) {
        return new Intl.NumberFormat('es-SV', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
        }).format(Number(val || 0));
    }

    showToast(message, type = 'success') {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                customClass: { popup: 'swal-axstore' }
            });
            Toast.fire({
                icon: type,
                title: message
            });
        }
    }

    /**
     * Renderiza la UI del dropdown estilo Apple
     */
    render() {
        const badge = document.getElementById('ax-cart-badge');
        const headerCount = document.getElementById('ax-cart-header-count');
        const emptyState = document.getElementById('ax-cart-empty');
        const filledState = document.getElementById('ax-cart-filled');
        const itemsList = document.getElementById('ax-cart-items-list');
        const subtotalEl = document.getElementById('ax-cart-subtotal');

        const totalCount = this.getTotalCount();
        const subtotal = this.getSubtotal();

        // Actualizar badge del ícono de carrito
        if (badge) {
            badge.textContent = totalCount;
            if (totalCount > 0) {
                badge.classList.remove('scale-0', 'opacity-0');
                badge.classList.add('scale-100', 'opacity-100');
            } else {
                badge.classList.add('scale-0', 'opacity-0');
                badge.classList.remove('scale-100', 'opacity-100');
            }
        }

        if (headerCount) {
            headerCount.textContent = `${totalCount} ${totalCount === 1 ? 'artículo' : 'artículos'}`;
        }

        if (subtotalEl) {
            subtotalEl.textContent = this.formatMoney(subtotal);
        }

        // Estado vacío vs Estado con productos
        if (totalCount === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            if (filledState) filledState.classList.add('hidden');
            if (itemsList) itemsList.innerHTML = '';
        } else {
            if (emptyState) emptyState.classList.add('hidden');
            if (filledState) filledState.classList.remove('hidden');

            if (itemsList) {
                itemsList.innerHTML = this.cart.map(item => {
                    const imgHtml = item.imagen
                        ? `<img src="${item.imagen}" alt="${item.producto}" class="w-12 h-12 rounded-xl object-cover border border-slate-100 bg-slate-50 flex-shrink-0">`
                        : `<div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 text-blue-500 flex items-center justify-center text-sm flex-shrink-0"><i class="fas fa-box"></i></div>`;

                    return `
                        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition-colors">
                            ${imgHtml}
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-bold text-slate-800 truncate" title="${item.producto}">${item.producto}</h4>
                                <p class="text-[10px] text-slate-400 truncate">${item.variante || item.sku || 'Estándar'}</p>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs font-black text-slate-900">${this.formatMoney(item.precio_venta)}</span>
                                    
                                    <!-- Controles de cantidad minimalistas Apple -->
                                    <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-lg px-1.5 py-0.5 shadow-2xs">
                                        <button type="button" onclick="event.stopPropagation(); window.AXCart.updateQuantity(${item.id}, ${item.cantidad - 1})" class="text-slate-400 hover:text-blue-600 text-[10px] w-4 h-4 flex items-center justify-center font-bold cursor-pointer" title="Reducir">
                                            <i class="fas fa-minus text-[8px] pointer-events-none"></i>
                                        </button>
                                        <span class="text-[11px] font-bold text-slate-700 min-w-[14px] text-center select-none">${item.cantidad}</span>
                                        <button type="button" onclick="event.stopPropagation(); window.AXCart.updateQuantity(${item.id}, ${item.cantidad + 1})" class="text-slate-400 hover:text-blue-600 text-[10px] w-4 h-4 flex items-center justify-center font-bold cursor-pointer" title="Aumentar">
                                            <i class="fas fa-plus text-[8px] pointer-events-none"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); window.AXCart.removeItem(${item.id})" class="text-slate-300 hover:text-rose-500 p-1.5 rounded-lg transition-colors cursor-pointer" title="Eliminar de la bolsa">
                                <i class="fas fa-trash-can text-xs pointer-events-none"></i>
                            </button>
                        </div>
                    `;
                }).join('');
            }
        }
    }
}

// Instancia global
window.AXCart = new CartManager();
