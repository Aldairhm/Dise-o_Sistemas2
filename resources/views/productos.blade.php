<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXStore Admin - Registro de Producto</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Admin Navbar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="#" class="flex items-center gap-3">
                    <span class="text-xl font-black tracking-tighter text-gray-900">
                        AX<span class="text-blue-600">STORE</span> <span class="text-gray-400 font-medium text-sm ml-2">| Panel Admin</span>
                    </span>
                </a>
                <a href="#" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors"><i class="fas fa-arrow-left mr-2"></i> Volver al Catálogo</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-gray-900">Registrar Nuevo Producto</h1>
                <p class="text-gray-500 mt-2">Crea el perfil base del producto y define sus variaciones.</p>
            </div>
        </div>

        <form action="#" method="POST" class="flex flex-col lg:flex-row gap-8">
            
            <!-- COLUMNA IZQUIERDA (70%) -->
            <div class="w-full lg:w-2/3 space-y-6">
                
                <!-- Tarjeta 1: Información General -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-cube text-blue-600"></i> Información Base
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del Producto</label>
                            <input type="text" placeholder="Ej. Camisa Polo Clásica" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-2xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Categoría</label>
                            <select class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-2xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 transition-all cursor-pointer">
                                <option>Seleccionar...</option>
                                <option>Accesorios</option>
                                <option>Ropa</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Descripción</label>
                        <textarea rows="4" placeholder="Describe las características principales..." class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-2xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 transition-all resize-none"></textarea>
                    </div>
                </div>

                <!-- Tarjeta 2: Modelo EAV / Variantes -->
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm transition-all duration-500">
                    
                    <!-- Toggle Superior -->
                    <div class="flex items-center justify-between pb-6 border-b border-gray-100">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Variantes del Producto</h2>
                            <p class="text-sm text-gray-500 mt-1">Activa esta opción si el producto tiene múltiples tallas, colores o materiales.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="variantesToggle" class="sr-only peer" onchange="toggleVariantes()">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- ESTADO B: CONSTRUCTOR DE VARIANTES (Oculto por defecto) -->
                    <div id="variantesContainer" class="hidden mt-8">
                        
                        <!-- Constructor de Atributos (Estilo Burbujas/Tags) -->
                        <div class="mb-8 bg-gray-50 p-6 rounded-2xl border border-gray-200">
                            <h3 class="text-sm font-bold text-gray-900 mb-4">1. Agrega los atributos y sus valores</h3>
                            
                            <div class="flex flex-col md:flex-row gap-4 items-start mb-4">
                                <div class="w-full md:w-1/3">
                                    <input type="text" placeholder="Atributo (Ej. Color)" class="w-full bg-white border border-gray-200 text-sm rounded-xl px-4 py-3 focus:border-blue-500 focus:outline-none transition-all">
                                </div>
                                
                                <!-- Input tipo Tag (Burbujas) -->
                                <div class="w-full md:w-2/3 bg-white border border-gray-200 rounded-xl p-2 flex flex-wrap gap-2 items-center focus-within:border-blue-500 transition-all min-h-[46px]">
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-bold px-2.5 py-1.5 rounded-lg">
                                        Negro <i class="fas fa-times cursor-pointer hover:text-red-500"></i>
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-bold px-2.5 py-1.5 rounded-lg">
                                        Rojo <i class="fas fa-times cursor-pointer hover:text-red-500"></i>
                                    </span>
                                    <input type="text" placeholder="Escribe y presiona Enter..." class="flex-grow text-sm outline-none bg-transparent min-w-[120px] px-2">
                                </div>
                            </div>
                            
                            <div class="flex justify-end pt-2">
                                <button type="button" class="bg-blue-100 text-blue-700 text-sm font-bold py-2 px-6 rounded-xl hover:bg-blue-200 transition-colors flex items-center gap-2">
                                    <i class="fas fa-magic"></i> Generar Filas
                                </button>
                            </div>
                        </div>

                        <!-- La Tabla y el Botón Masivo -->
                        <div>
                            <div class="flex flex-col md:flex-row justify-between items-center mb-4 p-4 bg-white rounded-2xl border border-blue-200 shadow-sm gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-600"><i class="fas fa-images"></i></div>
                                    <span class="text-sm font-medium text-gray-700">Selecciona filas para aplicar la misma galería de fotos.</span>
                                </div>
                                <button type="button" onclick="openGalleryModal()" class="w-full md:w-auto bg-gray-100 text-gray-500 text-sm font-bold py-2.5 px-5 rounded-xl transition-all disabled:opacity-50" disabled id="btnBulkImage">
                                    Subir fotos a seleccionados
                                </button>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-gray-200 shadow-sm bg-white">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50">
                                        <tr class="border-b border-gray-200">
                                            <th class="py-4 px-4 w-12"><input type="checkbox" class="w-4 h-4 rounded text-blue-600 cursor-pointer" onclick="toggleAllCheckboxes(this)"></th>
                                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">SKU</th>
                                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Atributos</th>
                                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Precio Venta</th>
                                            <th class="py-4 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Galería</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr class="hover:bg-blue-50/50 transition-colors group">
                                            <td class="py-4 px-4"><input type="checkbox" class="variante-checkbox w-4 h-4 rounded text-blue-600 cursor-pointer" onchange="checkBulkButton()"></td>
                                            <td class="py-4 px-4"><input type="text" value="CAM-NG-S" class="w-full bg-white border border-gray-200 text-sm rounded-lg px-3 py-2 focus:border-blue-500 focus:outline-none"></td>
                                            <td class="py-4 px-4">
                                                <div class="flex gap-1">
                                                    <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded-md">Negro</span>
                                                    <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded-md">S</span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="relative">
                                                    <span class="absolute left-3 top-2 text-gray-400 text-sm font-bold">$</span>
                                                    <input type="number" step="0.01" value="15.00" class="w-24 bg-white border border-gray-200 text-sm rounded-lg pl-6 pr-3 py-2 focus:border-blue-500 focus:outline-none">
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <button type="button" onclick="openGalleryModal()" class="text-gray-400 hover:text-blue-600 bg-gray-50 hover:bg-blue-50 px-3 py-2 rounded-lg transition-colors border border-gray-200 text-xs font-bold">
                                                    0 fotos
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-blue-50/50 transition-colors group">
                                            <td class="py-4 px-4"><input type="checkbox" class="variante-checkbox w-4 h-4 rounded text-blue-600 cursor-pointer" onchange="checkBulkButton()"></td>
                                            <td class="py-4 px-4"><input type="text" value="CAM-NG-M" class="w-full bg-white border border-gray-200 text-sm rounded-lg px-3 py-2 focus:border-blue-500 focus:outline-none"></td>
                                            <td class="py-4 px-4">
                                                <div class="flex gap-1">
                                                    <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded-md">Negro</span>
                                                    <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded-md">M</span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="relative">
                                                    <span class="absolute left-3 top-2 text-gray-400 text-sm font-bold">$</span>
                                                    <input type="number" step="0.01" value="15.00" class="w-24 bg-white border border-gray-200 text-sm rounded-lg pl-6 pr-3 py-2 focus:border-blue-500 focus:outline-none">
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <button type="button" onclick="openGalleryModal()" class="text-gray-400 hover:text-blue-600 bg-gray-50 hover:bg-blue-50 px-3 py-2 rounded-lg transition-colors border border-gray-200 text-xs font-bold">
                                                    0 fotos
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ESTADO A: PRODUCTO SIMPLE (Visible si el toggle está apagado) -->
                    <div id="simpleProductContainer" class="mt-8 border-t border-gray-100 pt-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Datos Básicos -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">SKU (Código de barra)</label>
                                    <input type="text" placeholder="Opcional" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-2xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Precio de Venta (Vitrina)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-bold">$</span>
                                        </div>
                                        <input type="number" step="0.01" placeholder="0.00" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-2xl pl-8 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Subida de Imagen Individual (Mapea directo a la variante única) -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Imagen del Producto</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 bg-gray-50 hover:bg-blue-50 hover:border-blue-300 transition-colors cursor-pointer text-center h-[140px] flex flex-col justify-center">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-blue-500 mb-2"></i>
                                    <p class="text-sm font-bold text-gray-700">Subir imagen</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- COLUMNA DERECHA (30%) - ACCIONES -->
            <div class="w-full lg:w-1/3">
                <div class="sticky top-24 space-y-6">
                    
                    <!-- Tarjeta: Estado -->
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Visibilidad</h3>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <span class="text-sm font-bold text-gray-700">Público y Activo</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Botones Fixeados -->
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col gap-3">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Guardar Producto
                        </button>
                        <button type="button" class="w-full bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold py-4 px-6 rounded-2xl border border-gray-200 transition-colors">
                            Cancelar
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </main>

    <!-- MODAL DE GALERÍA MASIVA (Oculto por defecto) -->
    <div id="galleryModal" class="fixed inset-0 z-[100] hidden">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeGalleryModal()"></div>
        
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-3xl overflow-hidden shadow-2xl transform transition-all w-full max-w-2xl border border-gray-100 relative">
                
                <!-- Header Modal -->
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Subir Galería de Imágenes</h3>
                        <p class="text-sm text-gray-500" id="modalSubtitle">Aplicando a: 2 variantes seleccionadas</p>
                    </div>
                    <button onclick="closeGalleryModal()" class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Body Modal (Dropzone) -->
                <div class="p-6">
                    <div class="border-2 border-dashed border-blue-300 rounded-2xl p-10 bg-blue-50/50 hover:bg-blue-50 transition-colors cursor-pointer text-center mb-6">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-blue-600 text-2xl">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <p class="text-base font-bold text-gray-900">Arrastra tus fotos aquí</p>
                        <p class="text-sm text-gray-500 mt-1">Soporta múltiples archivos PNG y JPG</p>
                    </div>

                    <!-- Vista Previa de Imágenes (Simulando que subimos 2) -->
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Imágenes subidas (2)</h4>
                    <div class="grid grid-cols-4 gap-4">
                        <!-- Imagen 1 (Principal) -->
                        <div class="relative group rounded-xl overflow-hidden border-2 border-blue-500 aspect-square bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-tshirt text-3xl text-gray-300"></i> <!-- Simulando la imagen -->
                            <div class="absolute top-2 left-2 bg-yellow-400 text-white text-[10px] font-black px-2 py-1 rounded-md shadow-sm flex items-center gap-1">
                                <i class="fas fa-star"></i> Principal
                            </div>
                            <button class="absolute top-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-xs">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <!-- Imagen 2 -->
                        <div class="relative group rounded-xl overflow-hidden border border-gray-200 aspect-square bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-tshirt text-3xl text-gray-300"></i>
                            <button class="absolute top-2 left-2 bg-white/90 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity hover:text-blue-600">
                                Hacer principal
                            </button>
                            <button class="absolute top-2 right-2 bg-red-500 text-white w-6 h-6 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-xs">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button onclick="closeGalleryModal()" class="px-6 py-3 font-bold text-gray-600 hover:bg-gray-200 rounded-xl transition-colors">
                        Cancelar
                    </button>
                    <button onclick="closeGalleryModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-md transition-all">
                        Guardar Galería
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts de Lógica Visual -->
    <script>
        function toggleVariantes() {
            const isChecked = document.getElementById('variantesToggle').checked;
            const containerVariantes = document.getElementById('variantesContainer');
            const containerSimple = document.getElementById('simpleProductContainer');

            if (isChecked) {
                containerVariantes.classList.remove('hidden');
                containerSimple.classList.add('hidden');
            } else {
                containerVariantes.classList.add('hidden');
                containerSimple.classList.remove('hidden');
            }
        }

        function toggleAllCheckboxes(master) {
            document.querySelectorAll('.variante-checkbox').forEach(cb => cb.checked = master.checked);
            checkBulkButton();
        }

        function checkBulkButton() {
            const checkedCount = document.querySelectorAll('.variante-checkbox:checked').length;
            const btnBulk = document.getElementById('btnBulkImage');
            
            if (checkedCount > 0) {
                btnBulk.disabled = false;
                btnBulk.innerText = `Subir fotos para (${checkedCount}) seleccionados`;
                btnBulk.classList.remove('bg-gray-100', 'text-gray-500');
                btnBulk.classList.add('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-500/30', 'hover:bg-blue-700');
            } else {
                btnBulk.disabled = true;
                btnBulk.innerText = "Subir fotos a seleccionados";
                btnBulk.classList.remove('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-500/30', 'hover:bg-blue-700');
                btnBulk.classList.add('bg-gray-100', 'text-gray-500');
            }
        }

        function openGalleryModal() {
            const checkedCount = document.querySelectorAll('.variante-checkbox:checked').length;
            document.getElementById('modalSubtitle').innerText = checkedCount > 0 
                ? `Aplicando a: ${checkedCount} variantes seleccionadas` 
                : `Aplicando a: Variante individual`;
                
            document.getElementById('galleryModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeGalleryModal() {
            document.getElementById('galleryModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    </script>
</body>
</html>