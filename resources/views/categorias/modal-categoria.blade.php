<div id="categoriaModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modalTitle" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div id="modalBackdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300 opacity-0"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div id="modalPanel" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all duration-300 scale-95 opacity-0 sm:my-8 sm:w-full sm:max-w-4xl border border-slate-100">
                
                <!-- Header Azul -->
                <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-white font-bold flex items-center gap-2 text-base">
                            <i class="fas fa-folder-plus" id="modalHeaderIcon"></i>
                            <span id="modalTitle">Nueva Categoría</span>
                        </h3>
                        <p class="text-xs text-blue-100 mt-0.5" id="modalSubtitle">Completa los campos para registrar una categoría</p>
                    </div>
                    <button type="button" onclick="closeModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Formulario -->
                <form id="categoriaForm">
                    <!-- Configuración interna -->
                    <input type="hidden" id="modalMethod" value="POST">
                    <input type="hidden" id="categoriaId" value="">

                    <!-- Body -->
                    <div class="p-6 md:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                            
                            <!-- COLUMNA IZQUIERDA: INFORMACIÓN DE LA CATEGORÍA -->
                            <div class="md:col-span-4 flex flex-col">
                                <h4 class="text-xs font-black text-slate-500 tracking-widest uppercase mb-4">Información de la Categoría</h4>
                                
                                <div class="bg-white rounded-2xl border-2 border-slate-100 p-6 flex flex-col items-center text-center shadow-sm relative overflow-hidden h-full">
                                    <!-- Decoración top -->
                                    <div class="absolute top-0 left-0 right-0 h-1" id="previewTopBar" style="background-color: #3b82f6;"></div>
                                    
                                    <!-- Ícono Grande -->
                                    <div id="previewIconWrapper" class="w-24 h-24 rounded-2xl flex items-center justify-center mb-4 transition-all duration-300" style="background-color: #3b82f6; box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);">
                                        <i class="fas fa-tag text-white text-4xl" id="previewIcon"></i>
                                    </div>
                                    
                                    <!-- Nombre -->
                                    <h3 class="text-xl font-black text-slate-800 mb-2 truncate w-full" id="previewName">Nueva Categoría</h3>
                                    
                                    <!-- Badges -->
                                    <div class="flex items-center justify-center gap-2 mb-6 flex-wrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100" id="previewRole">
                                            <i class="fas fa-tag"></i> Categoría
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100" id="previewStatus">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Activa
                                        </span>
                                    </div>

                                    <div class="w-full border-t border-slate-100 pt-4 mt-auto">
                                        <div class="flex justify-between items-center text-sm mb-2">
                                            <span class="text-slate-400">Código Hex:</span>
                                            <span class="font-bold text-slate-700 font-mono" id="previewHex">#3b82f6</span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm mb-2">
                                            <span class="text-slate-400">Ícono:</span>
                                            <span class="font-bold text-slate-700 font-mono" id="previewIconClass">fa-tag</span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-slate-400">Desc:</span>
                                            <span class="font-bold text-slate-700 truncate max-w-[120px]" id="previewDesc">---</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- COLUMNA DERECHA: DATOS DE LA CATEGORÍA -->
                            <div class="md:col-span-8 flex flex-col">
                                <h4 class="text-xs font-black text-slate-500 tracking-widest uppercase mb-4">Datos de la Categoría</h4>
                                
                                <div class="space-y-5 flex-1">
                                    <!-- Nombre -->
                                    <div>
                                        <label for="inputNombre" class="block text-sm font-bold text-slate-700 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                                        <input type="text" id="inputNombre" required maxlength="100"
                                               class="w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all shadow-sm"
                                               placeholder="Ej: Electrónica">
                                        <p class="mt-1.5 text-xs text-slate-400">Nombre público que verán los clientes (Campo obligatorio)</p>
                                        <p id="errorNombre" class="mt-1 text-xs text-red-500 font-medium hidden"></p>
                                    </div>

                                    <!-- Descripción -->
                                    <div>
                                        <label for="inputDescripcion" class="block text-sm font-bold text-slate-700 mb-1.5">Descripción</label>
                                        <textarea id="inputDescripcion" rows="2"
                                                  class="w-full px-4 py-3 bg-white border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all resize-none shadow-sm"
                                                  placeholder="Breve descripción de los productos..."></textarea>
                                        <p class="mt-1.5 text-xs text-slate-400">Opcional. Breve resumen de lo que incluye esta categoría.</p>
                                        <p id="errorDescripcion" class="mt-1 text-xs text-red-500 font-medium hidden"></p>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <!-- Color -->
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Color representativo <span class="text-red-500">*</span></label>
                                            <input type="hidden" name="color" id="inputColor" value="#3b82f6">
                                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 max-h-[140px] overflow-y-auto p-1.5 rounded-xl bg-slate-50 border border-slate-200" id="colorPalette">
                                                <!-- Swatches dinámicos -->
                                            </div>
                                            <p class="mt-2 text-xs text-slate-400">Identificador de color visual.</p>
                                        </div>

                                        <!-- Ícono -->
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">Ícono representativo <span class="text-red-500">*</span></label>
                                            <input type="hidden" name="icono" id="inputIcono" value="fa-tag">
                                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 max-h-[140px] overflow-y-auto p-1.5 rounded-xl bg-slate-50 border border-slate-200" id="iconPalette">
                                                <!-- Iconos dinámicos -->
                                            </div>
                                            <p class="mt-2 text-xs text-slate-400">Ícono gráfico para la categoría.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 md:px-8 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" onclick="closeModal()"
                                class="px-6 py-2.5 rounded-xl font-bold text-sm text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                            CANCELAR
                        </button>
                        <button type="submit" id="btnSubmitModal"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-black text-sm text-white shadow-lg transition-all duration-300 hover:scale-[1.02] cursor-pointer bg-slate-900 shadow-slate-900/20">
                            <i class="fas fa-check"></i>
                            <span id="btnSubmitText">GUARDAR CAMBIOS</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const COLOR_PALETTE = window.COLOR_PALETTE = [
    { hex: '#3b82f6', name: 'Azul',     light: '#eff6ff' },
    { hex: '#0ea5e9', name: 'Cielo',    light: '#f0f9ff' },
    { hex: '#6366f1', name: 'Índigo',   light: '#eef2ff' },
    { hex: '#8b5cf6', name: 'Violeta',  light: '#f5f3ff' },
    { hex: '#a855f7', name: 'Púrpura',  light: '#faf5ff' },
    { hex: '#d946ef', name: 'Fucsia',   light: '#fdf4ff' },
    { hex: '#ec4899', name: 'Rosa',     light: '#fdf2f8' },
    { hex: '#f43f5e', name: 'Rosa Vivo',light: '#fff1f2' },
    { hex: '#ef4444', name: 'Rojo',     light: '#fef2f2' },
    { hex: '#f97316', name: 'Naranja',  light: '#fff7ed' },
    { hex: '#f59e0b', name: 'Ámbar',    light: '#fffbeb' },
    { hex: '#eab308', name: 'Amarillo', light: '#fefce8' },
    { hex: '#84cc16', name: 'Lima',     light: '#f7fee7' },
    { hex: '#22c55e', name: 'Verde',    light: '#f0fdf4' },
    { hex: '#10b981', name: 'Esmeralda',light: '#ecfdf5' },
    { hex: '#14b8a6', name: 'Teal',     light: '#f0fdfa' },
    { hex: '#06b6d4', name: 'Cyan',     light: '#ecfeff' },
    { hex: '#64748b', name: 'Pizarra',  light: '#f8fafc' },
];

const ICON_PALETTE = window.ICON_PALETTE = [
    'fa-tag', 'fa-box', 'fa-laptop', 'fa-mobile-screen', 'fa-shirt', 
    'fa-shoe-prints', 'fa-camera', 'fa-headphones', 'fa-gamepad', 'fa-book',
    'fa-dumbbell', 'fa-basketball', 'fa-car', 'fa-motorcycle', 'fa-couch',
    'fa-utensils', 'fa-mug-hot', 'fa-gift', 'fa-star', 'fa-heart',
    'fa-bolt', 'fa-crown', 'fa-gem', 'fa-music',
    'fa-clock', 'fa-glasses', 'fa-hat-cowboy', 'fa-baby-carriage', 'fa-paw',
    'fa-spray-can', 'fa-blender', 'fa-tv', 'fa-chair', 'fa-bed',
    'fa-football', 'fa-baseball-bat-ball', 'fa-tools', 'fa-hammer', 'fa-seedling',
    'fa-guitar', 'fa-palette', 'fa-paint-roller', 'fa-video', 'fa-microphone',
    'fa-plane', 'fa-suitcase', 'fa-shopping-bag', 'fa-store', 'fa-pills',
    'fa-tooth', 'fa-leaf', 'fa-bicycle', 'fa-campground', 'fa-camera-retro'
];

function renderColorPalette() {
    const container = document.getElementById('colorPalette');
    container.innerHTML = COLOR_PALETTE.map(c => `
        <button type="button" 
                onclick="selectColor('${c.hex}', '${c.light}')"
                class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center transition-all duration-200 focus:outline-none hover:scale-110 color-swatch"
                style="background-color: ${c.hex};"
                data-hex="${c.hex}"
                title="${c.name}">
            <i class="fas fa-check text-white text-sm opacity-0 scale-50 transition-all duration-200"></i>
        </button>
    `).join('');
}

function renderIconPalette() {
    const container = document.getElementById('iconPalette');
    container.innerHTML = ICON_PALETTE.map(icon => `
        <button type="button" 
                onclick="selectIcon('${icon}')"
                class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center transition-all duration-200 focus:outline-none hover:bg-slate-200 text-slate-500 hover:text-slate-800 icon-swatch"
                data-icon="${icon}"
                title="${icon}">
            <i class="fas ${icon} text-lg"></i>
        </button>
    `).join('');
}

function selectColor(hex, light) {
    document.getElementById('inputColor').value = hex;
    
    // Formulario (Swatches)
    document.querySelectorAll('.color-swatch').forEach(btn => {
        const isSelected = btn.dataset.hex === hex;
        btn.classList.toggle('ring-2', isSelected);
        btn.classList.toggle('ring-offset-2', isSelected);
        btn.style.setProperty('--tw-ring-color', hex);
        
        const icon = btn.querySelector('.fa-check');
        icon.classList.toggle('opacity-100', isSelected);
        icon.classList.toggle('scale-100', isSelected);
        icon.classList.toggle('opacity-0', !isSelected);
        icon.classList.toggle('scale-50', !isSelected);
    });

    // Vista Previa (Izquierda)
    document.getElementById('previewTopBar').style.backgroundColor = hex;
    document.getElementById('previewIconWrapper').style.backgroundColor = hex;
    document.getElementById('previewIconWrapper').style.boxShadow = `0 10px 25px -5px ${hex}66`;
    document.getElementById('previewHex').textContent = hex;
}

function selectIcon(iconClass) {
    document.getElementById('inputIcono').value = iconClass;
    
    // Formulario (Swatches)
    document.querySelectorAll('.icon-swatch').forEach(btn => {
        const isSelected = btn.dataset.icon === iconClass;
        if(isSelected) {
            btn.classList.add('bg-blue-100', 'text-blue-600', 'ring-2', 'ring-blue-300');
            btn.classList.remove('text-slate-500', 'hover:bg-slate-200');
        } else {
            btn.classList.remove('bg-blue-100', 'text-blue-600', 'ring-2', 'ring-blue-300');
            btn.classList.add('text-slate-500', 'hover:bg-slate-200');
        }
    });

    // Vista Previa (Izquierda)
    document.getElementById('previewIcon').className = `fas ${iconClass} text-white text-4xl`;
    document.getElementById('previewIconClass').textContent = iconClass;
}

document.addEventListener('DOMContentLoaded', () => {
    renderColorPalette();
    renderIconPalette();

    // Actualización en vivo de la tarjeta de vista previa
    const inputNombre = document.getElementById('inputNombre');
    const inputDesc = document.getElementById('inputDescripcion');
    
    inputNombre.addEventListener('input', (e) => {
        const val = e.target.value.trim();
        document.getElementById('previewName').textContent = val || 'Nueva Categoría';
    });

    inputDesc.addEventListener('input', (e) => {
        const val = e.target.value.trim();
        document.getElementById('previewDesc').textContent = val || '---';
    });
});
</script>
