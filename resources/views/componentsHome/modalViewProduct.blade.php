<!-- Modal Tailwind (Rediseño Total - Light Mode) -->
    <div id="modal" class="fixed inset-0 z-[100] hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        
        <!-- Modal Panel -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Contenedor Light Mode Minimalista -->
            <div class="relative bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl w-full border border-gray-100 flex flex-col md:flex-row h-[90vh] md:h-[85vh] max-h-[90vh] md:max-h-[85vh]">
                
                <!-- Botón Cerrar Flotante -->
                <button onclick="closeModal()" class="absolute top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-800 transition-all z-[60] shadow-sm">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Panel Izquierdo (Imagen y Galería) -->
                <div class="w-full md:w-1/2 min-h-0 bg-gray-50/50 p-6 sm:p-8 flex flex-col items-center relative overflow-hidden group border-b md:border-b-0 md:border-r border-gray-100">
                    <div class="absolute inset-0 bg-blue-50 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    
                    <!-- Contenedor Imagen Principal -->
                    <div class="flex-1 flex items-center justify-center w-full relative z-10 min-h-[200px] md:min-h-[300px] mb-4">
                        <img id="v-modal-img" src="" alt="Product" class="max-h-[200px] md:max-h-[300px] object-contain drop-shadow-xl transform transition-transform duration-700">
                        <!-- Botón Descargar Imagen Principal -->
                        <a id="v-modal-download" href="#" download="imagen.jpg" class="absolute bottom-2 left-1/2 transform -translate-x-1/2 z-20 bg-white/95 backdrop-blur text-gray-800 hover:text-blue-600 hover:bg-white font-bold py-2 px-5 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.12)] md:opacity-0 md:group-hover:opacity-100 transition-all duration-300 flex items-center gap-2 text-xs sm:text-sm border border-gray-100 whitespace-nowrap" title="Descargar Imagen">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                    </div>

                    <!-- Miniaturas -->
                    <div id="v-modal-gallery" class="w-full flex justify-center gap-3 z-20 flex-wrap pb-2">
                        <!-- Se llenan con JS -->
                    </div>
                </div>

                <!-- Panel Derecho (Info Light) -->
                <div class="w-full md:w-1/2 min-h-0 p-6 sm:p-8 md:p-12 flex flex-col justify-start bg-white overflow-y-auto custom-scrollbar">
                    
                    <!-- Header: Categoría, Nombre y SKU -->
                    <!-- pr-12 o pr-16 evita que el botón X se superponga al contenido en pantallas móviles -->
                    <div class="flex flex-col mb-4 sm:mb-6 pr-12 sm:pr-14">
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <span id="v-modal-category" class="text-blue-600 text-[10px] sm:text-xs font-black uppercase tracking-widest">Categoría</span>
                            <span id="v-modal-marca" class="hidden text-[10px] sm:text-xs font-bold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-md uppercase tracking-wider border border-orange-200">Marca</span>
                            <span id="v-modal-sku" class="bg-gray-100 text-gray-500 text-[9px] sm:text-[10px] font-bold px-2.5 py-1 rounded-full border border-gray-200 whitespace-nowrap">SKU</span>
                        </div>
                        <h2 id="v-modal-name" class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight">Nombre</h2>
                    </div>
                    
                    <div id="v-modal-price" class="text-3xl sm:text-4xl font-light text-gray-900 mb-4 sm:mb-6 flex items-center">
                        <span class="text-blue-600 font-bold mr-1">$</span><span id="v-modal-price-val">0.00</span>
                    </div>
                    {{-- Chips de atributos con valor (se actualizan al cambiar variante) --}}
                    <div id="v-modal-attrs-section" class="hidden mb-5">
                        <div id="v-modal-attrs-list" class="flex flex-wrap gap-1.5">
                            {{-- JS inserta chips aquí --}}
                        </div>
                    </div>

                    {{-- Descripción con leer más --}}
                    <div class="mb-6 sm:mb-8">
                        <div id="v-modal-desc-wrapper" class="relative">
                            <p id="v-modal-description"
                               class="text-gray-500 leading-relaxed text-xs sm:text-sm whitespace-pre-line break-words overflow-hidden transition-all duration-500"
                               style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                                Descripción
                            </p>
                        </div>
                        <button id="v-modal-read-more"
                                onclick="toggleDescription()"
                                class="mt-2 inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 text-xs font-bold uppercase tracking-wider transition-colors hidden">
                            <span id="v-modal-read-more-text">Leer más</span>
                            <i id="v-modal-read-more-icon" class="fas fa-chevron-down text-[10px] transition-transform duration-300"></i>
                        </button>
                    </div>

                    <!-- Selector de Variantes (Estilo Shein/Temu) -->
                    <div id="v-modal-variants-container" class="mb-6 hidden">
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-widest mb-3">Variantes Disponibles:</p>
                        <div id="v-modal-variants-list" class="flex flex-wrap gap-2 sm:gap-3">
                            <!-- JS inserta las variantes aquí -->
                        </div>
                    </div>

                    <!-- Información de Inventario y Reserva (Dinámico según variante) -->
                    <div class="flex gap-3 sm:gap-4 mb-8 sm:mb-10">
                        <div class="flex-1 bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-gray-100 shadow-sm shadow-gray-100/50 flex flex-col items-center justify-center text-center">
                            <i class="fas fa-store text-green-500 mb-1 sm:mb-2 text-lg sm:text-xl"></i>
                            <p id="v-modal-stock" class="text-lg sm:text-xl font-black text-gray-800">0</p>
                            <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-wider font-bold">Tienda</p>
                        </div>
                        <div class="flex-1 bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-gray-100 shadow-sm shadow-gray-100/50 flex flex-col items-center justify-center text-center">
                            <i class="fas fa-warehouse text-orange-500 mb-1 sm:mb-2 text-lg sm:text-xl"></i>
                            <p id="v-modal-reserva" class="text-lg sm:text-xl font-black text-gray-800">0</p>
                            <p class="text-[9px] sm:text-[10px] text-gray-400 uppercase tracking-wider font-bold">Bodega</p>
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
            const modalName  = document.getElementById('v-modal-name').innerText;
            const modalPrice = document.getElementById('v-modal-price-val').innerText;
            const modalDesc  = document.getElementById('v-modal-description').innerText.trim();

            const markdownText = `*${modalName}*\n\n*Precio:* $${modalPrice}\n\n${modalDesc}`;

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
            // Extraer los datos básicos del botón
            const name = btn.getAttribute('data-name') || '';
            const category = btn.getAttribute('data-category') || '';
            const marca = btn.getAttribute('data-marca') || '';
            const desc = btn.getAttribute('data-description') || '';

            // Extraer JSON de variantes
            let variants = [];
            try {
                variants = JSON.parse(btn.getAttribute('data-variants') || '[]');
            } catch(e) {
                console.error("Error al parsear variantes", e);
            }

            // Limpiar chips de atributos (se rellenan en selectVariant al elegir variante)
            document.getElementById('v-modal-attrs-section').classList.add('hidden');
            document.getElementById('v-modal-attrs-list').innerHTML = '';

            // Asignar los valores estáticos
            document.getElementById('v-modal-name').innerText = name;
            document.getElementById('v-modal-category').innerText = category;

            const marcaSpan = document.getElementById('v-modal-marca');
            if (marca) {
                marcaSpan.innerText = marca;
                marcaSpan.classList.remove('hidden');
            } else {
                marcaSpan.classList.add('hidden');
            }

            // Descripción con leer más
            const descEl = document.getElementById('v-modal-description');
            const readMoreBtn = document.getElementById('v-modal-read-more');
            const readMoreText = document.getElementById('v-modal-read-more-text');
            const readMoreIcon = document.getElementById('v-modal-read-more-icon');

            descEl.innerText = desc;
            // Resetear estado colapsado
            descEl.style.cssText = 'display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;';
            descEl.dataset.expanded = 'false';

            // Verificar si realmente necesita el botón (más de 4 líneas aprox.)
            requestAnimationFrame(() => {
                descEl.style.cssText = 'display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;';
                const isTruncated = descEl.scrollHeight > descEl.clientHeight + 2;
                if (isTruncated) {
                    readMoreBtn.classList.remove('hidden');
                    readMoreText.textContent = 'Leer más';
                    readMoreIcon.style.transform = 'rotate(0deg)';
                } else {
                    readMoreBtn.classList.add('hidden');
                }
            });

            const variantsContainer = document.getElementById('v-modal-variants-container');
            const variantsList = document.getElementById('v-modal-variants-list');

            variantsList.innerHTML = ''; // Limpiar lista
            
            if (variants && variants.length > 0) {
                // Hay variantes, mostramos el contenedor
                variantsContainer.classList.remove('hidden');
                
                // Función para actualizar modal según variante seleccionada
                const selectVariant = (variant, btnElement) => {
                    // Actualizar UI de botones de variante
                    Array.from(variantsList.children).forEach(c => {
                        c.classList.remove('border-blue-600', 'bg-blue-50', 'text-blue-700');
                        c.classList.add('border-gray-200', 'bg-white', 'text-gray-600');
                    });
                    btnElement.classList.add('border-blue-600', 'bg-blue-50', 'text-blue-700');
                    btnElement.classList.remove('border-gray-200', 'bg-white', 'text-gray-600');
                    
                    // Actualizar chips de atributos+valor de la variante
                    const attrsSection = document.getElementById('v-modal-attrs-section');
                    const attrsList    = document.getElementById('v-modal-attrs-list');
                    attrsList.innerHTML = '';
                    if (variant.valores && variant.valores.length > 0) {
                        variant.valores.forEach(v => {
                            if (!v.atributo || !v.valor) return;
                            const chip = document.createElement('span');
                            chip.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-wide';
                            chip.innerHTML = `<i class="fas fa-tag text-[8px] text-slate-400"></i><span class="text-slate-400 font-bold">${v.atributo}:</span>&nbsp;<span class="text-slate-700">${v.valor}</span>`;
                            attrsList.appendChild(chip);
                        });
                        attrsSection.classList.remove('hidden');
                    } else {
                        attrsSection.classList.add('hidden');
                    }

                    // Actualizar datos en modal
                    document.getElementById('v-modal-price-val').innerText = parseFloat(variant.precio).toFixed(2);
                    document.getElementById('v-modal-sku').innerText = variant.sku || 'N/A';
                    document.getElementById('v-modal-stock').innerText = variant.stock || '0';
                    document.getElementById('v-modal-reserva').innerText = variant.reserva || '0';
                    
                    // Manejar imágenes de la variante
                    const mainImg = document.getElementById('v-modal-img');
                    const downloadBtn = document.getElementById('v-modal-download');
                    const gallery = document.getElementById('v-modal-gallery');
                    gallery.innerHTML = '';
                    
                    if (variant.imagenes && variant.imagenes.length > 0) {
                        mainImg.src = variant.imagenes[0];
                        downloadBtn.href = variant.imagenes[0];
                        
                        if (variant.imagenes.length > 1) {
                            variant.imagenes.forEach((imgUrl, index) => {
                                const thumb = document.createElement('img');
                                thumb.src = imgUrl;
                                thumb.className = `w-14 h-14 rounded-xl object-cover cursor-pointer border-2 transition-all duration-300 shadow-sm ${index === 0 ? 'border-blue-500 scale-105' : 'border-transparent hover:border-blue-300 opacity-60 hover:opacity-100'}`;
                                
                                thumb.onclick = () => {
                                    mainImg.src = imgUrl;
                                    downloadBtn.href = imgUrl;
                                    
                                    Array.from(gallery.children).forEach(c => {
                                        c.classList.remove('border-blue-500', 'scale-105');
                                        c.classList.add('border-transparent', 'opacity-60');
                                    });
                                    thumb.classList.add('border-blue-500', 'scale-105');
                                    thumb.classList.remove('border-transparent', 'opacity-60');
                                };
                                gallery.appendChild(thumb);
                            });
                        }
                    } else if (variant.imgUrl) {
                        mainImg.src = variant.imgUrl;
                        downloadBtn.href = variant.imgUrl;
                    } else {
                        mainImg.src = '';
                        downloadBtn.href = '#';
                    }
                };
                
                // Renderizar botones de variantes con etiquetas de estado
                variants.forEach((v, idx) => {
                    const btn = document.createElement('button');
                    const vStock   = parseInt(v.stock)   || 0;
                    const vReserva = parseInt(v.reserva) || 0;

                    if (vStock === 0 && vReserva === 0) {
                        // Agotado
                        btn.className = 'relative px-4 py-2 text-xs sm:text-sm font-semibold border-2 rounded-full transition-all duration-200 border-red-200 bg-red-50 text-red-400 cursor-not-allowed focus:outline-none';
                        btn.innerHTML = `
                            <span class="line-through">${v.nombre}</span>
                            <span class="ml-1.5 inline-flex items-center gap-0.5 text-[9px] font-black uppercase tracking-wider bg-red-500 text-white px-1.5 py-0.5 rounded-full">
                                <i class="fas fa-ban text-[8px]"></i> Agotado
                            </span>`;
                    } else if (vStock === 0 && vReserva > 0) {
                        // En bodega
                        btn.className = 'relative px-4 py-2 text-xs sm:text-sm font-semibold border-2 rounded-full transition-all duration-200 border-amber-300 bg-amber-50 text-amber-700 hover:border-amber-400 focus:outline-none';
                        btn.innerHTML = `
                            ${v.nombre}
                            <span class="ml-1.5 inline-flex items-center gap-0.5 text-[9px] font-black uppercase tracking-wider bg-amber-400 text-amber-900 px-1.5 py-0.5 rounded-full">
                                <i class="fas fa-warehouse text-[8px]"></i> Bodega
                            </span>`;
                    } else {
                        // Con stock normal
                        btn.className = 'px-4 py-2 text-xs sm:text-sm font-semibold border-2 rounded-full transition-all duration-200 border-gray-200 bg-white text-gray-600 hover:border-blue-300 focus:outline-none';
                        btn.innerText = v.nombre;
                    }

                    btn.onclick = () => selectVariant(v, btn);
                    variantsList.appendChild(btn);
                });
                
                // Seleccionar la primera variante por defecto
                selectVariant(variants[0], variantsList.firstElementChild);
                
            } else {
                // Fallback por si acaso el producto no tiene variantes (no debería pasar)
                variantsContainer.classList.add('hidden');
                document.getElementById('v-modal-attrs-section').classList.add('hidden');
                document.getElementById('v-modal-attrs-list').innerHTML = '';
                document.getElementById('v-modal-price-val').innerText = (btn.getAttribute('data-price') || '').replace('$', '');
                document.getElementById('v-modal-sku').innerText = btn.getAttribute('data-sku') || '';
                document.getElementById('v-modal-stock').innerText = (btn.getAttribute('data-stock') || '').replace(' un.', '');
                document.getElementById('v-modal-reserva').innerText = (btn.getAttribute('data-reserva') || '').replace(' un.', '');
                
                const mainImg = document.getElementById('v-modal-img');
                const downloadBtn = document.getElementById('v-modal-download');
                const img = btn.getAttribute('data-image') || '';
                if(img) {
                    mainImg.src = img;
                    downloadBtn.href = img;
                } else {
                    mainImg.src = '';
                    downloadBtn.href = '#';
                }
                document.getElementById('v-modal-gallery').innerHTML = '';
            }

            const modal = document.getElementById('modal');
            modal.classList.remove('hidden');
            // Agregar scroll-lock al body
            document.body.style.overflow = 'hidden';
        }

        function toggleDescription() {
            const descEl = document.getElementById('v-modal-description');
            const readMoreText = document.getElementById('v-modal-read-more-text');
            const readMoreIcon = document.getElementById('v-modal-read-more-icon');
            const isExpanded = descEl.dataset.expanded === 'true';

            if (isExpanded) {
                // Colapsar
                descEl.style.cssText = 'display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;';
                descEl.dataset.expanded = 'false';
                readMoreText.textContent = 'Leer más';
                readMoreIcon.style.transform = 'rotate(0deg)';
            } else {
                // Expandir
                descEl.style.cssText = 'display: block; overflow: visible;';
                descEl.dataset.expanded = 'true';
                readMoreText.textContent = 'Ver menos';
                readMoreIcon.style.transform = 'rotate(180deg)';
            }
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