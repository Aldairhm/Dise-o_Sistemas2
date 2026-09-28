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
                    <p class="text-xs text-slate-500">Ajusta el monto antes de liquidar</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formEdit" class="px-6 py-5 space-y-4">
            <input type="hidden" id="editComisionId">

            {{-- Monto --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Monto <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">$</span>
                    <input type="number" id="editMonto" step="0.01" min="0.01" required
                           class="w-full pl-8 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all">
                </div>
            </div>

            {{-- Porcentaje --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Porcentaje (%)</label>
                <input type="number" id="editPorcentaje" step="0.01" min="0" max="100"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all"
                       placeholder="Opcional">
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
     MODAL: LIQUIDAR EN LOTE (admin)
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modalLiquidar" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeLiquidarModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-circle-check text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Liquidar Comisiones</h2>
                    <p class="text-xs text-slate-500">Marca como Pagadas todas las comisiones pendientes</p>
                </div>
            </div>
            <button type="button" onclick="closeLiquidarModal()"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formLiquidar" class="px-6 py-5 space-y-4">

            {{-- Vendedor --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Vendedor <span class="text-red-500">*</span>
                </label>
                <select id="liquidarVendedor" required
                        class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all">
                    <option value="">Selecciona un vendedor...</option>
                    @foreach($vendedores as $v)
                        <option value="{{ $v->id }}">{{ $v->nombre_real }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Hasta fecha --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Hasta fecha <span class="text-slate-400 font-normal normal-case">(opcional — si vacío, liquida todas)</span>
                </label>
                <input type="date" id="liquidarHasta"
                       class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all">
            </div>

            {{-- Notas --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Notas de pago</label>
                <textarea id="liquidarNotas" rows="2" maxlength="500"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all resize-none"
                          placeholder="Ej: Planilla quincenal octubre 2026..."></textarea>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700 flex items-start gap-2.5">
                <i class="fas fa-triangle-exclamation mt-0.5 flex-shrink-0"></i>
                <span>Esta acción marcará como <strong>Pagadas</strong> todas las comisiones Pendiente del vendedor seleccionado. El sistema registrará quién realizó la liquidación.</span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeLiquidarModal()"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-circle-check mr-1.5"></i>Liquidar ahora
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL: REGISTRAR COMISIÓN MANUAL (admin)
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modalCreate" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeCreateModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-plus text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Nueva Comisión</h2>
                    <p class="text-xs text-slate-500">Registro manual de comisión</p>
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
                        <option value="{{ $v->id }}">{{ $v->nombre_real }}</option>
                    @endforeach
                </select>
                <p id="createErrorVendedor" class="mt-1 text-xs text-red-500 hidden"></p>
            </div>

            {{-- Salida (opcional) --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    ID de Salida <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                </label>
                <input type="number" id="createSalida" min="1"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                       placeholder="Ej: 42">
            </div>

            {{-- Monto --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Monto <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">$</span>
                    <input type="number" id="createMonto" step="0.01" min="0.01" required
                           class="w-full pl-8 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                           placeholder="0.00">
                </div>
                <p id="createErrorMonto" class="mt-1 text-xs text-red-500 hidden"></p>
            </div>

            {{-- Porcentaje --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Porcentaje (%)</label>
                <input type="number" id="createPorcentaje" step="0.01" min="0" max="100"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                       placeholder="Opcional — ej: 5">
            </div>

            {{-- Notas --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Notas</label>
                <textarea id="createNotas" rows="2" maxlength="500"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all resize-none"
                          placeholder="Motivo del registro manual..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <i class="fas fa-plus mr-1.5"></i>Registrar
                </button>
            </div>
        </form>
    </div>
</div>
