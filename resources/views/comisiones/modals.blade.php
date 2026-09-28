{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL: EDITAR COMISIÓN (admin)
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeEditModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-pen text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Editar Comisión</h2>
                    <p class="text-xs text-slate-500">Ajusta el monto o concepto antes de liquidar</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formEdit" class="px-6 py-5 space-y-4">
            <input type="hidden" id="editComisionId">

            {{-- Concepto --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Concepto / Motivo</label>
                <input type="text" id="editConcepto" maxlength="150"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all"
                       placeholder="Ej: Venta de producto o bono...">
            </div>

            {{-- Monto --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Monto de Comisión <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">$</span>
                    <input type="number" id="editMonto" step="0.01" min="0.01" required
                           class="w-full pl-8 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all">
                </div>
            </div>

            {{-- Notas --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Notas de ajuste</label>
                <textarea id="editNotas" rows="2" maxlength="500"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all resize-none"
                          placeholder="Motivo del ajuste..."></textarea>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-lg shadow-blue-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-save mr-1.5"></i>Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL: LIQUIDAR COMISIONES CON MÉTODO DE PAGO (admin)
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modalLiquidar" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeLiquidarModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden max-h-[90vh] flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Liquidar y Pagar Comisiones</h2>
                    <p class="text-xs text-slate-500">Selecciona el vendedor y método de pago utilizado</p>
                </div>
            </div>
            <button type="button" onclick="closeLiquidarModal()"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formLiquidar" class="px-6 py-5 space-y-4 overflow-y-auto flex-1" enctype="multipart/form-data">

            {{-- 1. Seleccionar Vendedor --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    1. Vendedor a Pagar <span class="text-red-500">*</span>
                </label>
                <select id="liquidarVendedor" required onchange="cargarPendientesVendedor(this.value)"
                        class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all">
                    <option value="">Selecciona un vendedor para calcular pendientes...</option>
                    @foreach($vendedores as $v)
                        <option value="{{ $v->id }}">{{ $v->nombre_real }} ({{ $v->username }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Tarjeta de Resumen Dinámica --}}
            <div id="liquidarResumenBox" class="hidden rounded-xl border border-emerald-200 bg-emerald-50/60 p-4 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Total Pendiente</span>
                        <div class="text-2xl font-black text-emerald-700 mt-0.5" id="liquidarTotalMonto">$0.00</div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold text-emerald-700 block" id="liquidarCountBadge">0 comisiones</span>
                        <button type="button" onclick="toggleDetallePendientes()" class="text-xs text-emerald-600 underline hover:text-emerald-800 font-bold mt-1">
                            Ver desglose <i class="fas fa-chevron-down text-[10px]"></i>
                        </button>
                    </div>
                </div>

                {{-- Desglose de comisiones con checkboxes --}}
                <div id="liquidarDetalleLista" class="hidden mt-3 pt-3 border-t border-emerald-200/60 space-y-2 max-h-40 overflow-y-auto pr-1">
                    {{-- Llenado dinámicamente con JS --}}
                </div>
            </div>

            <div id="liquidarLoadingBox" class="hidden py-4 text-center text-slate-500 text-xs">
                <i class="fas fa-spinner fa-spin text-emerald-600 text-base mb-1"></i>
                <p>Cargando comisiones pendientes...</p>
            </div>

            <div id="liquidarEmptyBox" class="hidden rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                <i class="fas fa-info-circle mr-1"></i> Este usuario no tiene comisiones pendientes por pagar.
            </div>

            {{-- 2. Método de Pago --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    2. Método de Pago <span class="text-red-500">*</span>
                </label>
                <select id="liquidarMetodoPago" required
                        class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all">
                    <option value="Efectivo">💵 Efectivo</option>
                    <option value="Transferencia Bancaria">🏦 Transferencia Bancaria</option>
                    <option value="Cheque">📜 Cheque</option>
                    <option value="Billetera Digital">📱 Billetera Digital (Tigo Money / Chivo)</option>
                    <option value="Otro">💳 Otro método</option>
                </select>
            </div>

            {{-- 3. Referencia de Transacción --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    N° de Referencia / Comprobante <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                </label>
                <input type="text" id="liquidarReferencia" maxlength="100"
                       class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all"
                       placeholder="Ej: Transferencia #984213 o Cheque #402">
            </div>

            {{-- 4. Subir Comprobante de Pago --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Adjuntar Foto o Recibo <span class="text-slate-400 font-normal normal-case">(opcional - JPG, PNG, PDF)</span>
                </label>
                <input type="file" id="liquidarComprobante" accept="image/*,.pdf"
                       class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 cursor-pointer">
            </div>

            {{-- 5. Notas --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Notas de Liquidación</label>
                <textarea id="liquidarNotas" rows="2" maxlength="500"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all resize-none"
                          placeholder="Ej: Pago quincenal correspondiente a Septiembre..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeLiquidarModal()"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" id="btnConfirmLiquidar"
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-check-circle mr-1.5"></i>Confirmar Pago
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL: REGISTRAR COMISIÓN / BONO MANUAL (admin)
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modalCreate" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeCreateModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-award text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Nueva Comisión / Bono</h2>
                    <p class="text-xs text-slate-500">Asigna una comisión directa o incentivo</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formCreate" class="px-6 py-5 space-y-4">

            {{-- Vendedor --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Vendedor <span class="text-red-500">*</span>
                </label>
                <select id="createVendedor" required
                        class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
                    <option value="">Selecciona un vendedor...</option>
                    @foreach($vendedores as $v)
                        <option value="{{ $v->id }}">{{ $v->nombre_real }} ({{ $v->username }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Concepto --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Concepto o Motivo <span class="text-red-500">*</span>
                </label>
                <input type="text" id="createConcepto" required maxlength="150"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                       placeholder="Ej: Bono por meta de ventas, Incentivo especial...">
                
                {{-- Quick chips --}}
                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                    <button type="button" onclick="setConcepto('Bono por cumplimiento de meta')" class="text-[11px] bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 px-2 py-0.5 rounded-md text-slate-600 transition-colors">Bono por Meta</button>
                    <button type="button" onclick="setConcepto('Incentivo semanal')" class="text-[11px] bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 px-2 py-0.5 rounded-md text-slate-600 transition-colors">Incentivo Semanal</button>
                    <button type="button" onclick="setConcepto('Comisión especial')" class="text-[11px] bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 px-2 py-0.5 rounded-md text-slate-600 transition-colors">Comisión Especial</button>
                </div>
            </div>

            {{-- Monto --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Monto de Comisión ($) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">$</span>
                    <input type="number" id="createMonto" step="0.01" min="0.01" required
                           class="w-full pl-8 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                           placeholder="0.00">
                </div>
            </div>

            {{-- Notas --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Notas adicionales</label>
                <textarea id="createNotas" rows="2" maxlength="500"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all resize-none"
                          placeholder="Observaciones opcionales..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-plus mr-1.5"></i>Registrar Comisión
                </button>
            </div>
        </form>
    </div>
</div>
