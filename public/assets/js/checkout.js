document.addEventListener('DOMContentLoaded', function() {
    // Variables para checkout
    const checkoutModal = document.getElementById('checkout-modal');
    const closeCheckout = document.querySelector('.close-checkout');
    const backToCartBtn = document.querySelector('.back-to-cart');
    const checkoutForm = document.getElementById('checkout-form');
    const confirmationModal = document.getElementById('confirmation-modal');
    const closeConfirmation = document.querySelector('.close-confirmation');

    // ========== CHECKOUT FUNCTIONALITY ==========
    
    // Abrir checkout al hacer clic en "Finalizar Compra"
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('checkout-btn')) {
            // Asegúrate de que 'cart' está definido globalmente o accesible
            if (typeof cart === 'undefined' || cart.length === 0) {
                alert('Tu carrito está vacío. Agrega productos antes de finalizar la compra.');
                return;
            }
            document.getElementById('cart-modal').style.display = 'none';
            checkoutModal.style.display = 'flex';
            renderCheckoutSummary();
        }
    });
    
    // Cerrar checkout
    closeCheckout.addEventListener('click', () => {
        checkoutModal.style.display = 'none';
    });
    
    // Volver al carrito desde checkout
    backToCartBtn.addEventListener('click', () => {
        checkoutModal.style.display = 'none';
        document.getElementById('cart-modal').style.display = 'flex';
    });
    
    // Cerrar checkout al hacer clic fuera
    checkoutModal.addEventListener('click', (e) => {
        if (e.target === checkoutModal) {
            checkoutModal.style.display = 'none';
        }
    });
    
    // Procesar formulario de checkout
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        processCheckout();
    });
    
    // Cerrar confirmación
    closeConfirmation.addEventListener('click', () => {
        confirmationModal.style.display = 'none';
    });
    
    // Cerrar confirmación al hacer clic fuera
    confirmationModal.addEventListener('click', (e) => {
        if (e.target === confirmationModal) {
            confirmationModal.style.display = 'none';
        }
    });
});


// =================================================================
// ========== FUNCIONES DE CHECKOUT Y DATOS (FUERA DEL DOMContentLoaded) ==========
// =================================================================

// Renderizar resumen en checkout
function renderCheckoutSummary() {
    const checkoutItems = document.getElementById('checkout-items');
    const checkoutTotal = document.getElementById('checkout-total');
    
    checkoutItems.innerHTML = '';
    let total = 0;
    
    if (typeof cart !== 'undefined') {
        cart.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'checkout-item';
            itemElement.innerHTML = `
                <span>${item.name} x${item.quantity}</span>
                <span>$${(item.price * item.quantity).toFixed(2)}</span>
            `;
            checkoutItems.appendChild(itemElement);
            
            total += item.price * item.quantity;
        });
    }
    
    checkoutTotal.textContent = `$${total.toFixed(2)}`;
}

// Procesar checkout
function processCheckout() {
    
    // PASO 1: Recoger todos los datos base con claves en español
    const formData = {
        // DATOS DEL FORMULARIO
        nombre: document.getElementById('customer-name').value,
        telefono: document.getElementById('customer-phone').value,
        direccion: document.getElementById('customer-address').value,
        
        // DATOS GENERADOS POR JS
        id_orden: generateOrderId(),
        fecha: new Date().toLocaleString(),
        total_general: document.getElementById('checkout-total').textContent,
    };

    // PASO 2: Crear el objeto FormData e incluir los productos por separado
    const form = new FormData();

    // 2a. Añadir los datos base al FormData
    for (let key in formData) {
        form.append(key, formData[key]);
    }

    // 2b. Añadir los productos individualmente (hasta donde el carrito tenga)
    if (typeof cart !== 'undefined') {
        cart.forEach((item, index) => {
            // Claves únicas: prod_1_nombre, prod_1_cantidad, prod_1_total
            form.append(`prod_${index + 1}_nombre`, item.name);
            form.append(`prod_${index + 1}_cantidad`, item.quantity);
            form.append(`prod_${index + 1}_total`, (item.price * item.quantity).toFixed(2));
        });
    }
    
    // PASO 3: Guardar el pedido (Local Storage y Google Sheets) y mostrar confirmación
    saveOrder(form, formData); // Enviamos 'form' para la Sheet y 'formData' para el modal
    showConfirmation(formData);
}

// Generar ID de pedido
function generateOrderId() {
    return 'LJ-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
}

// Guardar pedido (Local Storage y Google Sheets COMBINADOS)
// Ahora recibe el objeto FormData listo para el envío y el objeto 'formData' para el LS/modal
function saveOrder(form, orderData) { 
    // 1. Lógica para guardar en Local Storage
    const orders = JSON.parse(localStorage.getItem('luxuryJewelsOrders')) || [];
    orders.push(orderData); // Guardamos el objeto base para localStorage
    localStorage.setItem('luxuryJewelsOrders', JSON.stringify(orders));
    
    console.log('Datos base del pedido listos para Sheets:', orderData);


    // 2. Lógica para enviar a Google Sheets
    
    // ⚠️ REEMPLAZA ESTA URL con la URL de la Aplicación Web de tu Apps Script
    const WEB_APP_URL = 'https://script.google.com/macros/s/AKfycbwFpQwpY1ucAMiCFEbaRkNCuHo2yzG5NaWRgEMGZ55H52yEXldlzdXc8Ce5hKINCgm2/exec';

    // Enviar los datos usando fetch
    fetch(WEB_APP_URL, {
        method: 'POST',
        body: form // Usamos el objeto FormData que contiene todos los campos individuales
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta de Google Sheets:', data);
        if (data.resultado === 'éxito') {
            console.log('¡Pedido guardado en Google Sheets!'); 
        } else {
            console.error('Error al guardar el pedido en Sheets: ' + data.mensaje);
        }
    })
    .catch(error => {
        console.error('Error de conexión al servidor de Google Sheets:', error);
    });
}

// Mostrar confirmación
function showConfirmation(orderData) {
    const checkoutModal = document.getElementById('checkout-modal');
    const confirmationModal = document.getElementById('confirmation-modal');
    const confirmationMessage = document.getElementById('confirmation-message');
    
    checkoutModal.style.display = 'none';
    confirmationMessage.textContent = `¡Gracias por tu compra, ${orderData.nombre}! Tu pedido #${orderData.id_orden} ha sido procesado.`;
    confirmationModal.style.display = 'flex';
    
    // Limpiar carrito 
    if (typeof cart !== 'undefined') {
        cart = [];
    }
    if (typeof updateCart !== 'undefined') {
        updateCart(); 
    }
}