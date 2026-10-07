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

        <form id="formEdit" novalidate class="px-6 py-5 space-y-4">
            <input type="hidden" id="editComisionId">

            {{-- Concepto --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Concepto / Motivo</label>
                <input type="text" id="editConcepto" maxlength="150"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all"
                       placeholder="Ej: Venta de producto o bono..."
                       oninput="clearFieldError('editConcepto')">
            </div>

            {{-- Monto --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Monto de Comisión <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">$</span>
                    <input type="number" id="editMonto" step="0.01" min="0.01"
                           class="w-full pl-8 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all"
                           oninput="clearFieldError('editMonto')">
                </div>
                <p id="editMonto-error" class="hidden mt-1.5 text-xs font-semibold text-red-500 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation"></i> El monto es obligatorio y debe ser mayor a $0.
                </p>
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
                    <p id="liquidarAlcance" class="hidden mt-1 text-[11px] font-semibold text-emerald-700"></p>
                </div>
            </div>
            <button type="button" onclick="closeLiquidarModal()"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Step Indicator --}}
        <div class="flex items-center px-6 pt-3 pb-3 bg-slate-50/50 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-1.5">
                <div class="w-6 h-6 rounded-full bg-emerald-600 text-white text-[11px] font-black flex items-center justify-center" id="step1Circle">1</div>
                <span class="text-[11px] font-bold text-emerald-700">Datos del Pago</span>
            </div>
            <div class="flex-1 mx-3 h-0.5 bg-slate-200 rounded-full">
                <div class="h-full bg-emerald-400 rounded-full transition-all duration-500" id="stepProgressLine" style="width:0%"></div>
            </div>
            <div class="flex items-center gap-1.5 opacity-35" id="stepIndicator2">
                <div class="w-6 h-6 rounded-full bg-slate-300 text-white text-[11px] font-black flex items-center justify-center" id="step2Circle">2</div>
                <span class="text-[11px] font-bold text-slate-500">Confirmar</span>
            </div>
        </div>

        <form id="formLiquidar" novalidate class="px-6 py-5 space-y-4 overflow-y-auto flex-1" enctype="multipart/form-data">
            <input type="hidden" id="liquidarFechaDesde" name="fecha_desde" value="">
            <input type="hidden" id="liquidarFechaHasta" name="fecha_hasta" value="">

            {{-- 1. Buscador y Selección de Vendedor --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    1. Vendedor a Pagar <span class="text-red-500">*</span>
                </label>
                <input type="hidden" id="liquidarVendedor" name="id_vendedor" value="">

                {{-- Tarjeta de Vendedor Seleccionado (Visible al elegir) --}}
                <div id="vendedorSeleccionadoBox" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-300 flex items-center justify-between gap-3 shadow-xs transition-all">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-black text-xs flex items-center justify-center flex-shrink-0 shadow-xs" id="selectedVendedorAvatar">
                            --
                        </div>
                        <div class="truncate">
                            <span class="text-[9px] font-black uppercase tracking-wider text-emerald-700 block">Vendedor Seleccionado</span>
                            <p class="font-bold text-sm text-slate-900 truncate" id="selectedVendedorNombre">Nombre</p>
                            <p class="text-[11px] text-slate-500 truncate" id="selectedVendedorUsername">correo</p>
                        </div>
                    </div>
                    <button type="button" id="btnCambiarVendedorLiquidar" onclick="deseleccionarVendedorLiquidar()"
                            class="px-3 py-1.5 rounded-lg bg-white hover:bg-emerald-100/80 border border-emerald-200 text-emerald-800 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs flex-shrink-0">
                        <i class="fas fa-arrows-rotate text-[10px]"></i> Cambiar
                    </button>
                    <span id="badgeVendedorFijo" class="hidden px-2.5 py-1 rounded-lg bg-emerald-100/90 border border-emerald-300 text-emerald-800 text-xs font-bold flex items-center gap-1.5 shadow-2xs flex-shrink-0" title="Vendedor fijado para esta liquidación">
                        <i class="fas fa-lock text-[10px] text-emerald-600"></i> Vendedor fijo
                    </span>
                </div>

                {{-- Buscador y Lista Desplegable de Vendedores --}}
                <div id="vendedorBuscadorBox" class="space-y-2">
                    <div class="relative">
                        <input type="text" id="liquidarSearchVendedor"
                               placeholder="Escribe el nombre o correo para buscar..."
                               oninput="filtrarVendedoresLiquidar(this.value)"
                               autocomplete="off"
                               class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:bg-white rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all">
                        <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                        <button type="button" id="btnClearSearchVendedor" onclick="limpiarBusquedaVendedor()" class="hidden absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs cursor-pointer">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>

                    <div id="listaVendedoresLiquidar" class="space-y-1.5 max-h-44 overflow-y-auto p-1.5 bg-slate-50/80 rounded-xl border border-slate-200">
                        @foreach($vendedores as $v)
                        <div class="vendedor-item flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200/70 hover:border-emerald-500 hover:bg-emerald-50/50 transition-all cursor-pointer group"
                             data-id="{{ $v->id }}"
                             data-nombre="{{ strtolower($v->nombre_real ?? '') }}"
                             data-username="{{ strtolower($v->username ?? '') }}"
                             data-display-name="{{ $v->nombre_real ?: $v->username }}"
                             data-display-user="{{ $v->username }}"
                             data-initials="{{ strtoupper(substr($v->nombre_real ?: $v->username, 0, 2)) }}"
                             onclick="seleccionarVendedorLiquidar({{ $v->id }}, '{{ addslashes($v->nombre_real ?: $v->username) }}', '{{ addslashes($v->username) }}', '{{ strtoupper(substr($v->nombre_real ?: $v->username, 0, 2)) }}'); clearFieldError('liquidarVendedor')">
                            <div class="flex items-center gap-2.5 overflow-hidden">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs flex-shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    {{ strtoupper(substr($v->nombre_real ?: $v->username, 0, 2)) }}
                                </div>
                                <div class="truncate">
                                    <p class="font-bold text-xs text-slate-800 truncate group-hover:text-emerald-950">{{ $v->nombre_real ?: $v->username }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ $v->username }}</p>
                                </div>
                            </div>
                            <span class="text-[11px] text-emerald-600 font-bold opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 flex-shrink-0">
                                <span>Seleccionar</span> <i class="fas fa-arrow-right text-[9px]"></i>
                            </span>
                        </div>
                        @endforeach
                        <div id="vendedoresEmptyMsg" class="hidden py-4 text-center text-xs text-slate-400">
                            <i class="fas fa-user-slash text-slate-300 text-base mb-1 block"></i>
                            No se encontraron vendedores coincidentes.
                        </div>
                    </div>
                </div>
                <p id="liquidarVendedor-error" class="hidden mt-1.5 text-xs font-semibold text-red-500 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation"></i> Debes seleccionar un vendedor para continuar.
                </p>
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
                        <button type="button" onclick="toggleDetallePendientes()" class="text-xs text-emerald-600 underline hover:text-emerald-800 font-bold mt-1 cursor-pointer">
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
                <select id="liquidarMetodoPago" onchange="handleMetodoPagoLiquidar(this.value)"
                        class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                    <option value="Compensación de Saldo" id="optCompensacionSaldo" class="hidden">Compensación de Saldo (Neto $0.00)</option>
                </select>
            </div>

            {{-- 3. Referencia de Transacción --}}
            <div>
                <label id="labelReferencia" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    N° de Referencia / Comprobante <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                </label>
                <input type="text" id="liquidarReferencia" maxlength="100"
                       class="w-full py-2.5 px-3.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm text-slate-700 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition-all"
                       placeholder="Ej: Recibo #402 o Referencia...">
            </div>

            {{-- 4. Subir Comprobante de Pago (Solo para Transferencia Bancaria) --}}
            <div id="containerComprobante" class="hidden transition-all bg-emerald-50/50 p-3.5 rounded-xl border border-emerald-100">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    <i class="fas fa-file-invoice-dollar text-emerald-600 mr-1"></i> Comprobante de Transferencia <span class="text-red-500">*</span>
                    <span class="text-slate-400 font-normal normal-case text-[11px] block mt-0.5">(Obligatorio: JPG, PNG, WEBP o PDF - Máx 5MB)</span>
                </label>
                <input type="file" id="liquidarComprobante" accept="image/*,.pdf"
                       onchange="clearFieldError('liquidarComprobante')"
                       class="w-full py-2 px-3 bg-white border border-emerald-200 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 cursor-pointer">
                <p id="liquidarComprobante-error" class="hidden mt-1.5 text-xs font-semibold text-red-500 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation"></i> El comprobante es obligatorio para transferencias bancarias.
                </p>
                <p class="text-[11px] text-emerald-700 mt-1 font-medium">Sube la captura de pantalla o recibo bancario emitido.</p>
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
                <button type="submit" id="btnConfirmLiquidar" disabled
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100">
                    <span>Revisar y Confirmar</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
        </form>

        {{-- STEP 2: Revisión --}}
        <div id="liquidarStep2" class="hidden px-6 py-5 overflow-y-auto flex-1 flex flex-col gap-4">

            {{-- Banner vendedor + total --}}
            <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl border border-emerald-200">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white font-black text-sm flex items-center justify-center flex-shrink-0 shadow-md" id="reviewVendedorAvatar">--</div>
                <div class="flex-1 min-w-0">
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 block">Vendedor a Pagar</span>
                    <p class="font-black text-base text-slate-900 truncate" id="reviewVendedorNombre">—</p>
                    <p class="text-xs text-slate-500 truncate" id="reviewVendedorUser">—</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Total</span>
                    <span class="text-2xl font-black text-emerald-700" id="reviewTotal">$0.00</span>
                </div>
            </div>

            {{-- Detalles del pago --}}
            <div class="rounded-2xl border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-100">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Detalles del Pago</span>
                </div>
                <div class="divide-y divide-slate-100">
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs text-slate-400 flex items-center gap-2"><i class="fas fa-hashtag text-blue-400 w-3.5"></i> Comisiones</span>
                        <span class="font-bold text-slate-800 text-xs" id="reviewCantidad">—</span>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs text-slate-400 flex items-center gap-2"><i class="fas fa-credit-card text-indigo-400 w-3.5"></i> Método de Pago</span>
                        <span class="font-bold text-slate-800 text-sm" id="reviewMetodo">—</span>
                    </div>
                    <div id="reviewReferenciaRow" class="hidden flex items-center justify-between px-4 py-3">
                        <span class="text-xs text-slate-400 flex items-center gap-2"><i class="fas fa-tag text-amber-400 w-3.5"></i> Referencia</span>
                        <span class="font-bold text-slate-800 text-xs font-mono bg-slate-100 px-2 py-0.5 rounded-lg" id="reviewReferencia">—</span>
                    </div>
                    <div id="reviewComprobanteRow" class="hidden flex items-center justify-between px-4 py-3">
                        <span class="text-xs text-slate-400 flex items-center gap-2"><i class="fas fa-file-invoice-dollar text-purple-500 w-3.5"></i> Comprobante</span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200" id="reviewComprobanteNombre">—</span>
                    </div>
                    <div id="reviewNotasRow" class="hidden flex items-start justify-between px-4 py-3 gap-4">
                        <span class="text-xs text-slate-400 flex items-center gap-2 flex-shrink-0"><i class="fas fa-sticky-note text-slate-400 w-3.5"></i> Notas</span>
                        <span class="font-medium text-slate-700 text-xs text-right italic" id="reviewNotas">—</span>
                    </div>
                </div>
            </div>

            {{-- Lista de comisiones incluidas --}}
            <div>
                <p class="text-[11px] font-black uppercase tracking-wider text-slate-400 mb-2">Comisiones incluidas</p>
                <div id="reviewListaComisiones" class="space-y-1.5 max-h-36 overflow-y-auto pr-0.5"></div>
            </div>

            {{-- Advertencia --}}
            <div class="flex items-start gap-2.5 bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
                <i class="fas fa-triangle-exclamation text-amber-500 mt-0.5 flex-shrink-0"></i>
                <span>Una vez confirmado, el pago <strong>no puede revertirse</strong>. Verifica que todos los datos sean correctos.</span>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 gap-3">
                <button type="button" onclick="volverStep1Liquidar()"
                        class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-arrow-left text-xs"></i> Volver a editar
                </button>
                <button type="button" id="btnFinalConfirmLiquidar" onclick="ejecutarLiquidacion()"
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-black shadow-lg shadow-emerald-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Confirmar Pago</span>
                </button>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL: REGISTRAR BONO (admin)
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
                    <h2 class="text-base font-bold text-slate-800">Nuevo Bono</h2>
                    <p class="text-xs text-slate-500">Asigna un bono o incentivo directo al vendedor</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formCreate" novalidate class="px-6 py-5 space-y-4">

            {{-- Vendedor con Buscador --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Vendedor Asignado <span class="text-red-500">*</span>
                </label>
                <input type="hidden" id="createVendedor" name="id_vendedor" value="">

                {{-- Tarjeta de Vendedor Seleccionado --}}
                <div id="createVendedorSeleccionadoBox" class="hidden p-3 rounded-xl bg-indigo-50 border border-indigo-300 flex items-center justify-between gap-3 shadow-xs transition-all">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center flex-shrink-0 shadow-xs" id="createSelectedVendedorAvatar">
                            --
                        </div>
                        <div class="truncate">
                            <span class="text-[9px] font-black uppercase tracking-wider text-indigo-700 block">Vendedor Asignado</span>
                            <p class="font-bold text-sm text-slate-900 truncate" id="createSelectedVendedorNombre">Nombre</p>
                            <p class="text-[11px] text-slate-500 truncate" id="createSelectedVendedorUsername">correo</p>
                        </div>
                    </div>
                    <button type="button" onclick="deseleccionarVendedorCreate()"
                            class="px-3 py-1.5 rounded-lg bg-white hover:bg-indigo-100/80 border border-indigo-200 text-indigo-800 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs flex-shrink-0">
                        <i class="fas fa-arrows-rotate text-[10px]"></i> Cambiar
                    </button>
                </div>

                {{-- Buscador y Lista de Vendedores --}}
                <div id="createVendedorBuscadorBox" class="space-y-2">
                    <div class="relative">
                        <input type="text" id="createSearchVendedor"
                               placeholder="Escribe el nombre o correo para buscar..."
                               oninput="filtrarVendedoresCreate(this.value)"
                               autocomplete="off"
                               class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 focus:bg-white rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
                        <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                        <button type="button" id="btnClearSearchVendedorCreate" onclick="limpiarBusquedaVendedorCreate()" class="hidden absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs cursor-pointer">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>

                    <div id="listaVendedoresCreate" class="space-y-1.5 max-h-40 overflow-y-auto p-1.5 bg-slate-50/80 rounded-xl border border-slate-200">
                        @foreach($vendedores as $v)
                        <div class="vendedor-create-item flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200/70 hover:border-indigo-500 hover:bg-indigo-50/50 transition-all cursor-pointer group"
                             data-id="{{ $v->id }}"
                             data-nombre="{{ strtolower($v->nombre_real ?? '') }}"
                             data-username="{{ strtolower($v->username ?? '') }}"
                             onclick="seleccionarVendedorCreate({{ $v->id }}, '{{ addslashes($v->nombre_real ?: $v->username) }}', '{{ addslashes($v->username) }}', '{{ strtoupper(substr($v->nombre_real ?: $v->username, 0, 2)) }}'); clearFieldError('createVendedor')">
                            <div class="flex items-center gap-2.5 overflow-hidden">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    {{ strtoupper(substr($v->nombre_real ?: $v->username, 0, 2)) }}
                                </div>
                                <div class="truncate">
                                    <p class="font-bold text-xs text-slate-800 truncate group-hover:text-indigo-950">{{ $v->nombre_real ?: $v->username }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ $v->username }}</p>
                                </div>
                            </div>
                            <span class="text-[11px] text-indigo-600 font-bold opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 flex-shrink-0">
                                <span>Asignar</span> <i class="fas fa-arrow-right text-[9px]"></i>
                            </span>
                        </div>
                        @endforeach
                        <div id="vendedoresCreateEmptyMsg" class="hidden py-4 text-center text-xs text-slate-400">
                            <i class="fas fa-user-slash text-slate-300 text-base mb-1 block"></i>
                            No se encontraron vendedores coincidentes.
                        </div>
                    </div>
                </div>
                <p id="createVendedor-error" class="hidden mt-1.5 text-xs font-semibold text-red-500 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation"></i> Debes seleccionar un vendedor para asignar el bono.
                </p>
            </div>

            {{-- Concepto --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Concepto o Motivo del Bono <span class="text-red-500">*</span>
                </label>
                <input type="text" id="createConcepto" maxlength="150"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                       placeholder="Ej: Bono por cumplimiento de meta, Bono puntualidad..."
                       oninput="clearFieldError('createConcepto')">
                
                {{-- Quick chips --}}
                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                    <button type="button" onclick="setConcepto('Bono por cumplimiento de meta')" class="text-[11px] bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 px-2 py-0.5 rounded-md text-slate-600 transition-colors cursor-pointer">Bono por Meta</button>
                    <button type="button" onclick="setConcepto('Bono de puntualidad')" class="text-[11px] bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 px-2 py-0.5 rounded-md text-slate-600 transition-colors cursor-pointer">Bono Puntualidad</button>
                    <button type="button" onclick="setConcepto('Bono especial')" class="text-[11px] bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 px-2 py-0.5 rounded-md text-slate-600 transition-colors cursor-pointer">Bono Especial</button>
                </div>
                <p id="createConcepto-error" class="hidden mt-1.5 text-xs font-semibold text-red-500 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation"></i> El concepto del bono es obligatorio.
                </p>
            </div>

            {{-- Monto --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Monto del Bono ($) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">$</span>
                    <input type="number" id="createMonto" step="0.01" min="0.01"
                           class="w-full pl-8 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-indigo-500 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                           placeholder="0.00"
                           oninput="clearFieldError('createMonto')">
                </div>
                <p id="createMonto-error" class="hidden mt-1.5 text-xs font-semibold text-red-500 flex items-center gap-1">
                    <i class="fas fa-circle-exclamation"></i> El monto del bono es obligatorio y debe ser mayor a $0.
                </p>
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
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-plus"></i>
                    <span>Registrar Bono</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MODAL: DETALLE COMPLETO DE COMISIÓN (Auditoría, Desglose y Venta)
═══════════════════════════════════════════════════════════════════════════ --}}
<div id="modalDetalleComision" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4">
    {{-- Backdrop con blur --}}
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeDetalleModal()"></div>

    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden flex flex-col max-h-[92vh] border border-slate-100 animate-in fade-in zoom-in-95 duration-200">
        
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-slate-100 flex-shrink-0 bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-base shadow-xs">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-black text-slate-800 tracking-tight">Detalle de Comisión</h2>
                        <span id="detIdBadge" class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">#COM-00000</span>
                    </div>
                    <p class="text-xs text-slate-500" id="detFechaHeader">Registrada el —</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span id="detEstadoBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    --
                </span>
                <button type="button" onclick="closeDetalleModal()"
                        class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Loading Skeleton --}}
        <div id="detLoadingSkeleton" class="p-8 space-y-4 animate-pulse">
            <div class="h-24 bg-slate-100 rounded-2xl"></div>
            <div class="h-36 bg-slate-100 rounded-2xl"></div>
            <div class="h-28 bg-slate-100 rounded-2xl"></div>
        </div>

        {{-- Content Container (scrollable) --}}
        <div id="detContentContainer" class="hidden overflow-y-auto flex-1 p-6 space-y-5">
            
            {{-- 1. HERO FINANCIERO Y VENDEDOR --}}
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-stretch">
                {{-- Monto neto ganado --}}
                <div class="sm:col-span-6 rounded-2xl p-4 bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-teal-500/10 border border-emerald-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-800">Monto Comisión Neta</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div class="my-2">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-emerald-600 tracking-tight" id="detMontoTotal">$0.00</span>
                        </div>
                        <span class="text-[11px] font-medium text-emerald-700/80" id="detPorcentajeRef">Ref: $0.00/ud</span>
                    </div>
                    <div id="detExtraBadgeHero" class="hidden inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-100/90 text-amber-800 border border-amber-300 text-[11px] font-bold w-fit">
                        <i class="fas fa-sparkles text-amber-600"></i>
                        <span id="detExtraTextoHero">+ Extra incluido</span>
                    </div>
                </div>

                {{-- Vendedor Asignado --}}
                <div class="sm:col-span-6 rounded-2xl p-4 bg-slate-50 border border-slate-200/80 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Vendedor Asignado</span>
                        <div class="w-7 h-7 rounded-lg bg-slate-200/70 text-slate-600 flex items-center justify-center text-xs">
                            <i class="fas fa-user-tag"></i>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 my-2 overflow-hidden">
                        <div class="w-11 h-11 rounded-2xl bg-indigo-600 text-white font-black text-sm flex items-center justify-center flex-shrink-0 shadow-xs" id="detVendedorAvatar">
                            --
                        </div>
                        <div class="truncate">
                            <p class="font-bold text-sm text-slate-900 truncate" id="detVendedorNombre">Nombre</p>
                            <p class="text-xs text-slate-500 truncate" id="detVendedorUser">correo@ejemplo.com</p>
                            <p class="text-[11px] text-slate-400 truncate" id="detVendedorTel"><i class="fas fa-phone mr-1 text-[10px]"></i>—</p>
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium">
                        Beneficiario del pago de esta comisión
                    </div>
                </div>
            </div>

            {{-- 2. DESGLOSE MATEMÁTICO (Base + Extra - Descuentos) --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4.5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calculator text-indigo-600 text-xs"></i>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-700">Desglose de Cálculo de Comisión</h3>
                    </div>
                    <span class="text-[11px] font-bold text-slate-400" id="detUnidadesTexto">1 unidad</span>
                </div>

                <div class="space-y-2 text-xs">
                    {{-- Línea Comisión Base --}}
                    <div class="flex items-center justify-between text-slate-600">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>Comisión base del producto (<span id="detBaseFormula">0 uds x $0.00</span>):</span>
                        </div>
                        <span class="font-bold text-slate-800" id="detComisionBase">$0.00</span>
                    </div>

                    {{-- Línea Costo Extra --}}
                    <div id="detRowCostoExtra" class="flex items-center justify-between text-amber-800 bg-amber-50/70 px-2.5 py-1.5 rounded-xl border border-amber-200/60">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-plus text-amber-600 text-[10px]"></i>
                            <span class="font-bold">Costo Extra (Terminal POS / Envío sumado a comisión):</span>
                        </div>
                        <span class="font-black text-amber-700" id="detCostoExtraVal">+$0.00</span>
                    </div>

                    {{-- Línea Descuento aplicado --}}
                    <div id="detRowDescuento" class="hidden flex items-center justify-between text-rose-700 bg-rose-50/60 px-2.5 py-1.5 rounded-xl border border-rose-200/60">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-minus text-rose-500 text-[10px]"></i>
                            <span>Descuento de venta deducido de comisión:</span>
                        </div>
                        <span class="font-black text-rose-700" id="detDescuentoVal">-$0.00</span>
                    </div>

                    {{-- Total Sumatoria --}}
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 font-black text-slate-900 text-sm">
                        <span>Total Comisión Reconocida:</span>
                        <span class="text-base text-emerald-600" id="detComisionTotalResumen">$0.00</span>
                    </div>
                </div>
            </div>

            {{-- 3. DETALLE DE SALIDA / VENTA ASOCIADA --}}
            <div id="detSalidaCard" class="rounded-2xl border border-slate-200 bg-white p-4.5 shadow-2xs space-y-3.5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-box-open text-blue-600 text-xs"></i>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-700">Producto y Venta Asociada</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="detSalidaBadge" class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">Salida #—</span>
                        <span id="detVentaBadge" class="hidden inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200 font-mono">
                            <span id="detVentaFolio">VNT-00000</span>
                        </span>
                    </div>
                </div>

                {{-- Datos del Producto --}}
                <div class="flex items-start gap-3.5 bg-slate-50 p-3.5 rounded-2xl border border-slate-200/70">
                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 overflow-hidden flex items-center justify-center flex-shrink-0 shadow-2xs" id="detProductoImgBox">
                        <i class="fas fa-box text-slate-300 text-xl"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-sm text-slate-900 leading-snug truncate" id="detProductoNombre">Nombre del Producto</h4>
                        <p class="text-xs text-slate-500 mt-0.5 truncate" id="detVarianteNombre">Variante</p>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap text-[11px]">
                            <span class="font-mono text-slate-500 font-bold bg-white px-2 py-0.5 rounded border border-slate-200" id="detProductoSKU">SKU: —</span>
                            <span class="font-bold text-slate-700" id="detProductoCant">Cantidad: 1 ud</span>
                            <span class="text-slate-400">|</span>
                            <span class="text-slate-600 font-medium" id="detProductoPrecioUnit">P. Unit: $0.00</span>
                        </div>
                    </div>
                </div>

                {{-- Datos de Entrega y Cliente (si existen) --}}
                <div id="detClienteBox" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs bg-slate-50/70 p-3 rounded-xl border border-slate-200/60">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Cliente Receptor</span>
                        <p class="font-bold text-slate-800 truncate" id="detClienteNombre">—</p>
                        <p class="text-slate-500 text-[11px]" id="detClienteTel"><i class="fas fa-phone mr-1 text-[9px]"></i>—</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Ubicación / Dirección</span>
                        <p class="font-semibold text-slate-700 text-[11px] leading-snug" id="detClienteDireccion">—</p>
                    </div>
                </div>
            </div>

            {{-- 4. INFORMACIÓN DE PAGO Y LIQUIDACIÓN (Si está Pagada) --}}
            <div id="detPagoCard" class="hidden rounded-2xl border border-emerald-200 bg-emerald-50/30 p-4.5 space-y-3">
                <div class="flex items-center justify-between border-b border-emerald-100 pb-2.5">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-xs"></i>
                        <h3 class="text-xs font-black uppercase tracking-wider text-emerald-900">Liquidación y Pago</h3>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">Liquidada con éxito</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Método de Pago Utilizado</span>
                        <p class="font-bold text-slate-800 flex items-center gap-1.5" id="detMetodoPago">
                            <i class="fas fa-money-bill-wave text-emerald-600"></i> Efectivo
                        </p>
                        <p class="text-[11px] font-mono text-slate-500 mt-0.5" id="detReferenciaPago">Ref: —</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-400 block mb-0.5">Fecha y Liquidado Por</span>
                        <p class="font-bold text-slate-800" id="detFechaLiquidacion">—</p>
                        <p class="text-[11px] text-slate-500 mt-0.5" id="detLiquidadoPor">Por: Administrador</p>
                    </div>
                </div>

                {{-- Botón comprobante si hay --}}
                <div id="detComprobanteBox" class="hidden pt-2 border-t border-emerald-100/70">
                    <a id="detComprobanteLink" href="#" target="_blank"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all cursor-pointer">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Ver Comprobante de Pago Adjunto</span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 5. NOTAS Y AUDITORÍA COMPLETA --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4.5 shadow-2xs space-y-2">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                    <i class="fas fa-clipboard-list text-slate-400 text-xs"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700">Notas de Auditoría y Observaciones</h3>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70 text-xs text-slate-700 leading-relaxed font-mono whitespace-pre-line" id="detNotasCompletas">
                    Sin observaciones adicionales registradas.
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/80 flex-shrink-0">
            <div class="flex items-center gap-2" id="detAdminActions">
                {{-- Botones directos si está pendiente --}}
            </div>
            <button type="button" onclick="closeDetalleModal()"
                    class="px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition-colors cursor-pointer ml-auto">
                <i class="fas fa-times mr-1.5"></i>Cerrar
            </button>
        </div>

    </div>
</div>

<script>
/**
 * ─── VALIDACIÓN INLINE DE CAMPOS ─────────────────────────────────────────────
 */
function showFieldError(fieldId, message) {
    // Marcar el campo con borde rojo
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.add('!border-red-400', '!bg-red-50/40', '!ring-0');
        // Si es input type file, marcar el contenedor
        if (field.type === 'file') {
            field.classList.add('border-red-400');
        }
    }
    // Mostrar el mensaje de error
    const errorEl = document.getElementById(fieldId + '-error');
    if (errorEl) {
        if (message) errorEl.innerHTML = `<i class="fas fa-circle-exclamation"></i> ${message}`;
        errorEl.classList.remove('hidden');
    }
}

function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.remove('!border-red-400', '!bg-red-50/40', '!ring-0', 'border-red-400');
    }
    const errorEl = document.getElementById(fieldId + '-error');
    if (errorEl) {
        errorEl.classList.add('hidden');
    }
}

function clearAllFieldErrors(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    form.querySelectorAll('[id$="-error"]').forEach(el => el.classList.add('hidden'));
    form.querySelectorAll('input, textarea, select').forEach(el => {
        el.classList.remove('!border-red-400', '!bg-red-50/40', '!ring-0', 'border-red-400');
    });
}

function seleccionarVendedorLiquidar(id, nombre, username, initials, bloquear = false) {
    const inputHidden = document.getElementById('liquidarVendedor');
    const nombreEl    = document.getElementById('selectedVendedorNombre');
    const userEl      = document.getElementById('selectedVendedorUsername');
    const avatarEl    = document.getElementById('selectedVendedorAvatar');
    const btnCambiar  = document.getElementById('btnCambiarVendedorLiquidar');
    const badgeFijo   = document.getElementById('badgeVendedorFijo');

    if (inputHidden) inputHidden.value = id;
    if (nombreEl) nombreEl.textContent = nombre;
    if (userEl) userEl.textContent = username;
    if (avatarEl) avatarEl.textContent = initials || nombre.substring(0, 2).toUpperCase();

    // Bloquear o desbloquear posibilidad de cambiar de vendedor
    if (bloquear) {
        window._vendedorLiquidarBloqueado = true;
        if (btnCambiar) btnCambiar.classList.add('hidden');
        if (badgeFijo) badgeFijo.classList.remove('hidden');
    } else {
        window._vendedorLiquidarBloqueado = false;
        if (btnCambiar) btnCambiar.classList.remove('hidden');
        if (badgeFijo) badgeFijo.classList.add('hidden');
    }

    document.getElementById('vendedorBuscadorBox')?.classList.add('hidden');
    document.getElementById('vendedorSeleccionadoBox')?.classList.remove('hidden');

    if (typeof cargarPendientesVendedor === 'function') {
        cargarPendientesVendedor(id);
    }
}

function deseleccionarVendedorLiquidar() {
    if (window._vendedorLiquidarBloqueado) {
        return; // REGLA: No se permite cambiar de vendedor si la liquidación se abrió desde el botón de ese vendedor
    }

    const inputHidden = document.getElementById('liquidarVendedor');
    if (inputHidden) inputHidden.value = '';

    document.getElementById('vendedorSeleccionadoBox')?.classList.add('hidden');
    document.getElementById('vendedorBuscadorBox')?.classList.remove('hidden');
    limpiarBusquedaVendedor();

    document.getElementById('liquidarResumenBox')?.classList.add('hidden');
    document.getElementById('liquidarEmptyBox')?.classList.add('hidden');
    document.getElementById('liquidarLoadingBox')?.classList.add('hidden');

    const lista = document.getElementById('liquidarDetalleLista');
    if (lista) {
        lista.classList.add('hidden');
        lista.innerHTML = '';
    }

    const btnSubmit = document.getElementById('btnConfirmLiquidar');
    if (btnSubmit) btnSubmit.disabled = true;
}

function filtrarVendedoresLiquidar(query) {
    const q = query.trim().toLowerCase();
    const items = document.querySelectorAll('#listaVendedoresLiquidar .vendedor-item');
    const emptyMsg = document.getElementById('vendedoresEmptyMsg');
    const btnClear = document.getElementById('btnClearSearchVendedor');

    if (btnClear) {
        if (q.length > 0) btnClear.classList.remove('hidden');
        else btnClear.classList.add('hidden');
    }

    let matches = 0;
    items.forEach(item => {
        const nombre = item.dataset.nombre || '';
        const username = item.dataset.username || '';
        if (!q || nombre.includes(q) || username.includes(q)) {
            item.classList.remove('hidden');
            matches++;
        } else {
            item.classList.add('hidden');
        }
    });

    if (emptyMsg) {
        if (matches === 0) emptyMsg.classList.remove('hidden');
        else emptyMsg.classList.add('hidden');
    }
}

function limpiarBusquedaVendedor() {
    const input = document.getElementById('liquidarSearchVendedor');
    if (input) {
        input.value = '';
        filtrarVendedoresLiquidar('');
    }
}

function seleccionarVendedorCreate(id, nombre, username, initials) {
    const inputHidden = document.getElementById('createVendedor');
    const nombreEl    = document.getElementById('createSelectedVendedorNombre');
    const userEl      = document.getElementById('createSelectedVendedorUsername');
    const avatarEl    = document.getElementById('createSelectedVendedorAvatar');

    if (inputHidden) inputHidden.value = id;
    if (nombreEl) nombreEl.textContent = nombre;
    if (userEl) userEl.textContent = username;
    if (avatarEl) avatarEl.textContent = initials || nombre.substring(0, 2).toUpperCase();

    document.getElementById('createVendedorBuscadorBox')?.classList.add('hidden');
    document.getElementById('createVendedorSeleccionadoBox')?.classList.remove('hidden');
}

function deseleccionarVendedorCreate() {
    const inputHidden = document.getElementById('createVendedor');
    if (inputHidden) inputHidden.value = '';

    document.getElementById('createVendedorSeleccionadoBox')?.classList.add('hidden');
    document.getElementById('createVendedorBuscadorBox')?.classList.remove('hidden');
    limpiarBusquedaVendedorCreate();
}

function filtrarVendedoresCreate(query) {
    const q = query.trim().toLowerCase();
    const items = document.querySelectorAll('#listaVendedoresCreate .vendedor-create-item');
    const emptyMsg = document.getElementById('vendedoresCreateEmptyMsg');
    const btnClear = document.getElementById('btnClearSearchVendedorCreate');

    if (btnClear) {
        if (q.length > 0) btnClear.classList.remove('hidden');
        else btnClear.classList.add('hidden');
    }

    let matches = 0;
    items.forEach(item => {
        const nombre = item.dataset.nombre || '';
        const username = item.dataset.username || '';
        if (!q || nombre.includes(q) || username.includes(q)) {
            item.classList.remove('hidden');
            matches++;
        } else {
            item.classList.add('hidden');
        }
    });

    if (emptyMsg) {
        if (matches === 0) emptyMsg.classList.remove('hidden');
        else emptyMsg.classList.add('hidden');
    }
}

function limpiarBusquedaVendedorCreate() {
    const input = document.getElementById('createSearchVendedor');
    if (input) {
        input.value = '';
        filtrarVendedoresCreate('');
    }
}

function handleMetodoPagoLiquidar(metodo) {
    const container = document.getElementById('containerComprobante');
    const input = document.getElementById('liquidarComprobante');
    const labelRef = document.getElementById('labelReferencia');
    const inputRef = document.getElementById('liquidarReferencia');

    if (!container || !input) return;

    if (metodo === 'Transferencia Bancaria') {
        container.classList.remove('hidden');
        if (labelRef) labelRef.innerHTML = 'N° de Referencia Bancaria <span class="text-slate-400 font-normal normal-case">(opcional)</span>';
        if (inputRef) inputRef.placeholder = 'Ej: Transferencia #984213, Cuenta origen/destino...';
    } else if (metodo === 'Compensación de Saldo') {
        container.classList.add('hidden');
        input.value = '';
        if (labelRef) labelRef.innerHTML = 'N° de Referencia / Comprobante de Cruce <span class="text-slate-400 font-normal normal-case">(opcional)</span>';
        if (inputRef) inputRef.placeholder = 'Ej: Compensación mutua de saldos #001';
    } else {
        container.classList.add('hidden');
        input.value = '';
        if (labelRef) labelRef.innerHTML = 'N° de Referencia / Comprobante <span class="text-slate-400 font-normal normal-case">(opcional)</span>';
        if (inputRef) inputRef.placeholder = 'Ej: Recibo manual #402';
    }
}

function formatNum(n) {
    return parseFloat(n || 0).toLocaleString('es-SV', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

async function verDetalleComision(id) {
    const modal = document.getElementById('modalDetalleComision');
    const skeleton = document.getElementById('detLoadingSkeleton');
    const content = document.getElementById('detContentContainer');
    if (!modal) {
        console.error('Modal modalDetalleComision no encontrado en el DOM');
        return;
    }

    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    skeleton?.classList.remove('hidden');
    content?.classList.add('hidden');

    try {
        const baseEndpoint = (typeof ROUTES !== 'undefined' && ROUTES.update && ROUTES.update !== 'null')
            ? ROUTES.update
            : "{{ url('comisiones') }}";
        const response = await fetch(`${baseEndpoint}/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) throw new Error('No se pudo cargar la información de la comisión.');
        const res = await response.json();
        if (!res.success || !res.comision) throw new Error(res.message || 'Error al obtener datos.');

        const c = res.comision;
        const salida = c.salida;
        const venta = c.venta;
        const desglose = c.desglose || {};

        // 1. Header
        const badgeEl = document.getElementById('detIdBadge');
        if (badgeEl) badgeEl.textContent = `#COM-${String(c.id).padStart(5, '0')}`;
        const fechaHeader = document.getElementById('detFechaHeader');
        if (fechaHeader) fechaHeader.textContent = `Registrada el ${c.fecha_registro}`;

        // Badge Estado
        const estadoBadge = document.getElementById('detEstadoBadge');
            if (c.estado === 'Pendiente') {
                if (c.es_liquidacion_bloqueada) {
                    estadoBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300';
                    estadoBadge.innerHTML = `<i class="fas fa-shield-halved text-amber-600 text-[10px]"></i> En garantía (${c.motivo_bloqueo || 'Garantía activa'})`;
                } else {
                    estadoBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200';
                    estadoBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Liquidación pendiente';
                }
            } else if (c.estado === 'Pagada') {
                estadoBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
                estadoBadge.innerHTML = '<i class="fas fa-check text-[10px]"></i> Pagada';
            } else {
                estadoBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200';
                estadoBadge.innerHTML = '<i class="fas fa-ban text-[10px]"></i> Cancelada';
            }

        // 2. Hero Financiero
        const montoSign = c.monto < 0 ? `-$${formatNum(Math.abs(c.monto))}` : `$${formatNum(c.monto)}`;
        const montoEl = document.getElementById('detMontoTotal');
        if (montoEl) {
            montoEl.textContent = montoSign;
            montoEl.className = c.monto < 0 ? 'text-3xl font-black text-rose-600 tracking-tight' : 'text-3xl font-black text-emerald-600 tracking-tight';
        }
        const refEl = document.getElementById('detPorcentajeRef');
        if (refEl) refEl.textContent = c.porcentaje > 0 ? `Ref: $${formatNum(c.porcentaje)}/unidad` : 'Monto asignado';

        const extraBadgeHero = document.getElementById('detExtraBadgeHero');
        const extraTextoHero = document.getElementById('detExtraTextoHero');
        if (desglose.costo_extra > 0) {
            extraBadgeHero?.classList.remove('hidden');
            if (extraTextoHero) extraTextoHero.textContent = `+$${formatNum(desglose.costo_extra)} costo extra incluido`;
        } else {
            extraBadgeHero?.classList.add('hidden');
        }

        // Vendedor
        const vAvatar = document.getElementById('detVendedorAvatar');
        if (vAvatar) vAvatar.textContent = c.vendedor?.iniciales || 'VE';
        const vNombre = document.getElementById('detVendedorNombre');
        if (vNombre) vNombre.textContent = c.vendedor?.nombre || 'Vendedor no asignado';
        const vUser = document.getElementById('detVendedorUser');
        if (vUser) vUser.textContent = c.vendedor?.username || '';
        const vTel = document.getElementById('detVendedorTel');
        if (vTel) vTel.innerHTML = c.vendedor?.telefono ? `<i class="fas fa-phone mr-1 text-[10px]"></i>${c.vendedor.telefono}` : '<i class="fas fa-phone-slash mr-1 text-[10px]"></i>Sin teléfono';

        // 3. Desglose Matemático
        const cant = desglose.cantidad || 1;
        const uTexto = document.getElementById('detUnidadesTexto');
        if (uTexto) uTexto.textContent = `${cant} ${cant === 1 ? 'unidad' : 'unidades'}`;
        const bFormula = document.getElementById('detBaseFormula');
        if (bFormula) bFormula.textContent = `${cant} uds x $${formatNum(desglose.comision_unitaria_base || 0)}`;
        const cBase = document.getElementById('detComisionBase');
        if (cBase) cBase.textContent = `$${formatNum(desglose.comision_base_total || 0)}`;

        const rowExtra = document.getElementById('detRowCostoExtra');
        const extraVal = document.getElementById('detCostoExtraVal');
        if (desglose.costo_extra > 0) {
            rowExtra?.classList.remove('hidden');
            if (extraVal) extraVal.textContent = `+$${formatNum(desglose.costo_extra)}`;
        } else {
            rowExtra?.classList.add('hidden');
        }

        const rowDesc = document.getElementById('detRowDescuento');
        const descVal = document.getElementById('detDescuentoVal');
        if (desglose.descuento_aplicado > 0) {
            rowDesc?.classList.remove('hidden');
            if (descVal) descVal.textContent = `-$${formatNum(desglose.descuento_aplicado)}`;
        } else {
            rowDesc?.classList.add('hidden');
        }
        const cTotalResumen = document.getElementById('detComisionTotalResumen');
        if (cTotalResumen) cTotalResumen.textContent = montoSign;

        // 4. Salida / Venta
        const salidaCard = document.getElementById('detSalidaCard');
        if (salida) {
            salidaCard?.classList.remove('hidden');
            const sBadge = document.getElementById('detSalidaBadge');
            if (sBadge) sBadge.textContent = `Salida #${salida.id}`;
            const pNombre = document.getElementById('detProductoNombre');
            if (pNombre) pNombre.textContent = salida.producto_nombre;
            const vNombreVar = document.getElementById('detVarianteNombre');
            if (vNombreVar) vNombreVar.textContent = salida.variante_nombre ? `Variante: ${salida.variante_nombre}` : 'Variante estándar';
            const pSku = document.getElementById('detProductoSKU');
            if (pSku) pSku.textContent = salida.sku ? `SKU: ${salida.sku}` : 'Sin SKU';
            const pCant = document.getElementById('detProductoCant');
            if (pCant) pCant.textContent = `Cantidad: ${salida.cantidad} uds`;
            const pPrecioUnit = document.getElementById('detProductoPrecioUnit');
            if (pPrecioUnit) pPrecioUnit.textContent = `P. Unit: $${formatNum(salida.precio_unitario)} (Total salida: $${formatNum(salida.total)})`;

            // Imagen producto
            const imgBox = document.getElementById('detProductoImgBox');
            if (imgBox) {
                if (salida.imagen) {
                    const src = salida.imagen.startsWith('http') || salida.imagen.startsWith('/') ? salida.imagen : '/storage/' + salida.imagen;
                    imgBox.innerHTML = `<img src="${src}" class="w-full h-full object-cover">`;
                } else {
                    imgBox.innerHTML = `<i class="fas fa-box text-slate-300 text-xl"></i>`;
                }
            }

            // Cliente
            const clienteBox = document.getElementById('detClienteBox');
            if (salida.nombre_cliente || salida.telefono || salida.direccion) {
                clienteBox?.classList.remove('hidden');
                const cNom = document.getElementById('detClienteNombre');
                if (cNom) cNom.textContent = salida.nombre_cliente || 'Consumidor Final';
                const cTel = document.getElementById('detClienteTel');
                if (cTel) cTel.innerHTML = salida.telefono ? `<i class="fas fa-phone mr-1 text-[9px]"></i>${salida.telefono}` : '<i class="fas fa-phone-slash mr-1 text-[9px]"></i>Sin teléfono';
                const partesDir = [salida.direccion, salida.municipio, salida.departamento].filter(Boolean);
                const cDir = document.getElementById('detClienteDireccion');
                if (cDir) cDir.textContent = partesDir.join(' - ') || 'En mostrador / Tienda';
            } else {
                clienteBox?.classList.add('hidden');
            }

            // Folio de Venta (sin enlace)
            const ventaBadge = document.getElementById('detVentaBadge') || document.getElementById('detVentaLink');
            const ventaFolio = document.getElementById('detVentaFolio');
            if (venta && venta.folio) {
                ventaBadge?.classList.remove('hidden');
                if (ventaFolio) ventaFolio.textContent = venta.folio;
            } else {
                ventaBadge?.classList.add('hidden');
            }
        } else {
            salidaCard?.classList.add('hidden');
        }

        // 5. Liquidación / Pago
        const pagoCard = document.getElementById('detPagoCard');
        if (c.estado === 'Pagada') {
            pagoCard?.classList.remove('hidden');
            const mPago = document.getElementById('detMetodoPago');
            if (mPago) mPago.innerHTML = `<i class="fas ${c.metodo_pago === 'Transferencia Bancaria' ? 'fa-university text-blue-600' : (c.metodo_pago === 'Compensación de Saldo' ? 'fa-scale-balanced text-indigo-600' : (c.metodo_pago === 'Efectivo' ? 'fa-money-bill-wave text-emerald-600' : 'fa-wallet text-indigo-600'))} mr-1"></i> ${c.metodo_pago || 'Efectivo'}`;
            const rPago = document.getElementById('detReferenciaPago');
            if (rPago) rPago.textContent = c.referencia_pago ? `Ref: ${c.referencia_pago}` : 'Sin referencia bancaria';
            const fLiq = document.getElementById('detFechaLiquidacion');
            if (fLiq) fLiq.textContent = c.fecha_liquidacion || '—';
            const lPor = document.getElementById('detLiquidadoPor');
            if (lPor) lPor.textContent = `Liquidado por: ${c.liquidado_por || 'Administrador'}`;

            const compBox = document.getElementById('detComprobanteBox');
            const compLink = document.getElementById('detComprobanteLink');
            if (c.comprobante_url) {
                compBox?.classList.remove('hidden');
                compLink.href = c.comprobante_url;
            } else {
                compBox?.classList.add('hidden');
            }
        } else {
            pagoCard?.classList.add('hidden');
        }

        // 6. Notas completas
        const notasEl = document.getElementById('detNotasCompletas');
        if (notasEl) notasEl.textContent = c.notas || 'Sin observaciones adicionales registradas.';

        // 7. Botones Admin
        const adminActions = document.getElementById('detAdminActions');
        if (adminActions) {
            if (c.estado === 'Pendiente' && typeof openEditModal === 'function') {
                adminActions.innerHTML = `
                    <button type="button" onclick="closeDetalleModal(); openEditModal(${c.id}, '${c.monto}', '${(c.concepto || '').replace(/'/g, "\\'")}', '${(c.notas || '').replace(/'/g, "\\'")}')"
                            class="px-4 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition-colors cursor-pointer flex items-center gap-1.5">
                        <i class="fas fa-pen text-[10px]"></i>
                        <span>Editar</span>
                    </button>
                    <button type="button" onclick="closeDetalleModal(); cancelarComision(${c.id})"
                            class="px-4 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs transition-colors cursor-pointer flex items-center gap-1.5">
                        <i class="fas fa-ban text-[10px]"></i>
                        <span>Cancelar</span>
                    </button>
                `;
            } else {
                adminActions.innerHTML = '';
            }
        }

        skeleton?.classList.add('hidden');
        content?.classList.remove('hidden');

    } catch (err) {
        if (skeleton) skeleton.classList.add('hidden');
        if (content) {
            content.classList.remove('hidden');
            content.innerHTML = `
                <div class="py-12 text-center text-slate-500">
                    <i class="fas fa-exclamation-triangle text-amber-500 text-3xl mb-2"></i>
                    <p class="font-bold text-sm text-slate-800">${err.message || 'Error al cargar detalle de comisión'}</p>
                    <button type="button" onclick="closeDetalleModal()" class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
                        Cerrar
                    </button>
                </div>
            `;
        }
    }
}

function closeDetalleModal() {
    const modal = document.getElementById('modalDetalleComision');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}

window.verDetalleComision = verDetalleComision;
window.closeDetalleModal = closeDetalleModal;

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeDetalleModal();
    }
});
</script>
