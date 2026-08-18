<!-- Modal Tailwind (Rediseño Total - Light Mode) -->
    <div id="modal" class="fixed inset-0 z-[100] hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        
        <!-- Modal Panel -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Contenedor Light Mode Minimalista -->
            <div class="relative bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl w-full border border-gray-100 flex flex-col md:flex-row max-h-[90vh] md:max-h-[85vh]">
                
                <!-- Botón Cerrar Flotante -->
                <button onclick="closeModal()" class="absolute top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-800 transition-all z-[60] shadow-sm">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Panel Izquierdo (Imagen) -->
                <div class="w-full md:w-1/2 bg-gray-50/50 p-8 sm:p-10 flex items-center justify-center min-h-[250px] md:min-h-[400px] relative overflow-hidden group border-b md:border-b-0 md:border-r border-gray-100">
                    <div class="absolute inset-0 bg-blue-50 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <img id="v-modal-img" src="" alt="Product" class="max-h-[200px] md:max-h-[350px] object-contain drop-shadow-xl transform group-hover:scale-105 transition-transform duration-700 z-10 relative">
                    
                    <!-- Botón Descargar Imagen -->
                    <a id="v-modal-download" href="#" download="imagen.jpg" class="absolute bottom-4 md:bottom-8 left-1/2 transform -translate-x-1/2 z-20 bg-white/95 backdrop-blur text-gray-800 hover:text-blue-600 hover:bg-white font-bold py-2 sm:py-2.5 px-5 sm:px-6 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.12)] md:opacity-0 md:group-hover:opacity-100 transition-all duration-300 flex items-center gap-2 text-xs sm:text-sm border border-gray-100 whitespace-nowrap" title="Descargar Imagen">
                        <i class="fas fa-download"></i> Descargar
                    </a>
                </div>

                <!-- Panel Derecho (Info Light) -->
                <div class="w-full md:w-1/2 p-6 sm:p-8 md:p-12 flex flex-col justify-center bg-white overflow-y-auto custom-scrollbar">
                    
                    <!-- Header: Categoría, Nombre y SKU -->
                    <!-- pr-12 o pr-16 evita que el botón X se superponga al contenido en pantallas móviles -->
                    <div class="flex flex-col mb-4 sm:mb-6 pr-12 sm:pr-14">
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <span id="v-modal-category" class="text-blue-600 text-[10px] sm:text-xs font-black uppercase tracking-widest">Categoría</span>
                            <span id="v-modal-sku" class="bg-gray-100 text-gray-500 text-[9px] sm:text-[10px] font-bold px-2.5 py-1 rounded-full border border-gray-200 whitespace-nowrap">SKU</span>
                        </div>
                        <h2 id="v-modal-name" class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight">Nombre</h2>
                    </div>
                    
                    <div id="v-modal-price" class="text-3xl sm:text-4xl font-light text-gray-900 mb-4 sm:mb-6 flex items-center">
                        <span class="text-blue-600 font-bold mr-1">$</span><span id="v-modal-price-val">0.00</span>
                    </div>
                    
                    <p id="v-modal-description" class="text-gray-500 mb-6 sm:mb-8 leading-relaxed text-xs sm:text-sm">Descripción</p>

                    <!-- Tarjetas de Stock Estilo Minimalista -->
                    <div class="flex gap-3 sm:gap-4 mb-8 sm:mb-10">
                        <div class="flex-1 bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-gray-100 shadow-sm shadow-gray-100/50 flex flex-col items-center justify-center text-center group hover:border-green-200 transition-colors">
                            <i class="fas fa-box text-green-500 mb-1 sm:mb-2 text-lg sm:text-xl group-hover:scale-110 transition-transform"></i>
                            <p id="v-modal-stock" class="text-lg sm:text-xl font-black text-gray-800">0</p>
                            <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-wider font-bold">Disp.</p>
                        </div>
                        <div class="flex-1 bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-gray-100 shadow-sm shadow-gray-100/50 flex flex-col items-center justify-center text-center group hover:border-orange-200 transition-colors">
                            <i class="fas fa-clock text-orange-500 mb-1 sm:mb-2 text-lg sm:text-xl group-hover:scale-110 transition-transform"></i>
                            <p id="v-modal-reserva" class="text-lg sm:text-xl font-black text-gray-800">0</p>
                            <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-wider font-bold">Reserva</p>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex flex-col sm:flex-row gap-3 mt-auto">
                        <button onclick="copyKit(event)" class="w-full sm:flex-1 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold py-3 sm:py-4 px-4 sm:px-6 rounded-xl sm:rounded-2xl transition-colors flex items-center justify-center gap-2 sm:gap-3 border border-gray-200 hover:border-gray-300 text-sm sm:text-base">
                            <i class="fas fa-copy text-gray-400"></i> 
                            Copiar Info
                        </button>
                        <button class="w-full sm:flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 sm:py-4 px-4 sm:px-6 rounded-xl sm:rounded-2xl shadow-lg shadow-blue-600/30 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 sm:gap-3 text-sm sm:text-base">
                            <i class="fas fa-shopping-cart"></i>
                            Al Carrito
                        </button>
                    </div>
                    
                    <!-- Textarea oculta para poder copiar -->
                    <textarea id="modal-copy" class="fixed opacity-0 pointer-events-none"></textarea>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyKit(event) {
            const modalName = document.getElementById('v-modal-name').innerText;
            const modalPrice = document.getElementById('v-modal-price-val').innerText;
            const modalDesc = document.getElementById('v-modal-description').innerText;
            const modalStock = document.getElementById('v-modal-stock').innerText;
            const modalReserva = document.getElementById('v-modal-reserva').innerText;

            // Construir la descripción en Markdown (compatible con WhatsApp)
            const markdownText = `*${modalName}*\n\n*Precio:* $${modalPrice}\n\n${modalDesc}\n\n*Stock:* ${modalStock} | *Reserva:* ${modalReserva}`;

            // Seleccionar y copiar al textarea
            const textarea = document.getElementById('modal-copy');
            textarea.value = markdownText;
            textarea.select();
            textarea.setSelectionRange(0, 99999); /* Para móviles */

            try {
                document.execCommand('copy');
                
                // Pequeña animación de confirmación en el botón
                const btn = event.currentTarget;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check mr-2"></i> Copiado';
                btn.disabled = true;
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
            } catch (err) {
                console.error('Error al copiar:', err);
                alert('Copia esto manualmente:\n\n' + markdownText);
            }
        }

        function openModal(btn) {
            // Extraer los datos del botón
            const name = btn.getAttribute('data-name') || '';
            const price = (btn.getAttribute('data-price') || '').replace('$', '');
            const category = btn.getAttribute('data-category') || '';
            const sku = btn.getAttribute('data-sku') || '';
            const stock = (btn.getAttribute('data-stock') || '').replace(' un.', '');
            const reserva = (btn.getAttribute('data-reserva') || '').replace(' un.', '');
            const image = btn.getAttribute('data-image') || '';
            const desc = btn.getAttribute('data-description') || '';

            // Asignar los valores a los elementos del modal
            document.getElementById('v-modal-name').innerText = name;
            document.getElementById('v-modal-price-val').innerText = price;
            document.getElementById('v-modal-category').innerText = category;
            document.getElementById('v-modal-sku').innerText = sku;
            document.getElementById('v-modal-stock').innerText = stock;
            document.getElementById('v-modal-reserva').innerText = reserva;
            document.getElementById('v-modal-img').src = image;
            document.getElementById('v-modal-download').href = image;
            document.getElementById('v-modal-description').innerText = desc;

            const modal = document.getElementById('modal');
            modal.classList.remove('hidden');
            // Agregar scroll-lock al body
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            modal.classList.add('hidden');
            // Restaurar scroll
            document.body.style.overflow = '';
        }

        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>