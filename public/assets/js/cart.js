// Variables globales del carrito
let cart = JSON.parse(localStorage.getItem('cart')) || [];

document.addEventListener('DOMContentLoaded', function() {
    // Variables del carrito
    const cartIcon = document.querySelector('.cart-icon');
    const cartModal = document.getElementById('cart-modal');
    const closeCart = document.querySelector('.close-cart');
    const cartBody = document.getElementById('cart-body');
    const cartTotal = document.getElementById('cart-total');
    const cartCount = document.querySelector('.cart-count');

    // ========== GESTIÓN DEL CARRITO ==========
    
    // Abrir/Cerrar carrito
    cartIcon.addEventListener('click', () => {
        // [NUEVO] Cerrar Menú Móvil si está abierto
        const navmenu = document.getElementById('navmenu');
        const mobileOverlay = document.getElementById('mobile-nav-overlay');
        const menuIcon = document.querySelector('.mobile-nav-toggle i');
        
        if (navmenu && navmenu.classList.contains('mobile-nav-active')) {
            navmenu.classList.remove('mobile-nav-active');
            if (mobileOverlay) mobileOverlay.classList.remove('active');
            document.body.classList.remove('mobile-nav-open');
            if (menuIcon) {
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        }

        // [NUEVO] Cerrar Perfil (Dropdown Bootstrap) si está abierto
        const userDropdown = document.querySelector('.user-icon .dropdown-menu');
        const userToggle = document.querySelector('.user-icon [data-bs-toggle="dropdown"]');
        if (userDropdown && userDropdown.classList.contains('show')) {
            userDropdown.classList.remove('show');
            if (userToggle) userToggle.classList.remove('show');
            if (userToggle) userToggle.setAttribute('aria-expanded', 'false');
        }

        // Abrir modal del carrito
        cartModal.style.display = 'flex';
        renderCartItems();
    });
    
    closeCart.addEventListener('click', () => {
        cartModal.style.display = 'none';
    });
    
    // Cerrar carrito al hacer clic fuera
    cartModal.addEventListener('click', (e) => {
        if (e.target === cartModal) {
            cartModal.style.display = 'none';
        }
    });

    // Inicializar contador del carrito
    updateCartCount();
});

// ========== FUNCIONES DEL CARRITO ==========

// Añadir producto al carrito
function addToCart(e) {
    const productId = parseInt(e.target.dataset.id);
    const product = products.find(p => p.id === productId);
    
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            ...product,
            quantity: 1
        });
    }
    
    updateCart();
    showAddedToCartMessage(product.name);
}

// Mostrar mensaje de producto añadido
function showAddedToCartMessage(productName) {
    const message = document.createElement('div');
    message.className = 'cart-message';
    message.innerHTML = `<span>${productName} añadido al carrito</span>`;
    document.body.appendChild(message);
    
    setTimeout(() => {
        message.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        message.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(message);
        }, 300);
    }, 3000);
}

// Renderizar items del carrito
function renderCartItems() {
    const cartBody = document.getElementById('cart-body');
    const cartTotal = document.getElementById('cart-total');
    
    if (cart.length === 0) {
        cartBody.innerHTML = '<p class="empty-cart">Tu carrito está vacío</p>';
        cartTotal.textContent = '$0.00';
        return;
    }
    
    cartBody.innerHTML = '';
    let total = 0;
    
    cart.forEach(item => {
        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="cart-item-img">
            <div class="cart-item-info">
                <h4 class="cart-item-title">${item.name}</h4>
                <div class="cart-item-price">$${(item.price * item.quantity).toFixed(2)}</div>
                <div class="cart-item-actions">
                    <button class="quantity-btn minus" data-id="${item.id}">-</button>
                    <span class="quantity">${item.quantity}</span>
                    <button class="quantity-btn plus" data-id="${item.id}">+</button>
                    <span class="remove-item" data-id="${item.id}"><i class="fas fa-trash"></i></span>
                </div>
            </div>
        `;
        cartBody.appendChild(cartItem);
        
        total += item.price * item.quantity;
    });
    
    cartTotal.textContent = `$${total.toFixed(2)}`;
    
    // Agregar eventos a los botones de cantidad y eliminar
    document.querySelectorAll('.minus').forEach(btn => {
        btn.addEventListener('click', decreaseQuantity);
    });
    
    document.querySelectorAll('.plus').forEach(btn => {
        btn.addEventListener('click', increaseQuantity);
    });
    
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', removeItem);
    });
}

// Disminuir cantidad
function decreaseQuantity(e) {
    const productId = parseInt(e.target.dataset.id);
    const item = cart.find(item => item.id === productId);
    
    if (item.quantity > 1) {
        item.quantity -= 1;
    } else {
        cart = cart.filter(item => item.id !== productId);
    }
    
    updateCart();
}

// Aumentar cantidad
function increaseQuantity(e) {
    const productId = parseInt(e.target.dataset.id);
    const item = cart.find(item => item.id === productId);
    item.quantity += 1;
    updateCart();
}

// Eliminar item
function removeItem(e) {
    const productId = parseInt(e.target.closest('.remove-item').dataset.id);
    cart = cart.filter(item => item.id !== productId);
    updateCart();
}

// Actualizar carrito
function updateCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCartItems();
    updateCartCount();
}

// Actualizar contador del carrito
function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    const count = cart.reduce((total, item) => total + item.quantity, 0);
    cartCount.textContent = count;
}