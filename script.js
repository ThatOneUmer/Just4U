// ===== JUST4U GAMING - MAIN JAVASCRIPT =====

// Cart storage key
const CART_KEY = 'j4u_cart';

// Load cart from localStorage
function loadCart() {
    try {
        const cart = localStorage.getItem(CART_KEY);
        return cart ? JSON.parse(cart) : [];
    } catch (e) {
        console.error('Error loading cart:', e);
        return [];
    }
}

// Save cart to localStorage
function saveCartToStorage(cart) {
    try {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
    } catch (e) {
        console.error('Error saving cart:', e);
    }
}

// Global state
const state = {
    cart: loadCart(),
    user: null,
    searchSuggestions: [
        'Fortnite Account',
        'Valorant Account',
        'PUBG Mobile',
        'GTA V Account',
        'Counter-Strike 2',
        'League of Legends',
        'Apex Legends',
        'Call of Duty',
        'Rocket League',
        'Minecraft Account'
    ],
    products: [
        {
            id: 1,
            name: 'Fortnite Pro Account',
            game: 'Fortnite',
            price: 99.99,
            originalPrice: 199.99,
            image: 'https://placehold.co/300x200/1FB6FF/ffffff?text=Fortnite+Pro',
            level: '200+',
            features: ['All Battle Passes', 'Rare Skins', 'V-Bucks'],
            delivery: 'Instant',
            verified: true,
            badge: 'instant'
        },
        {
            id: 2,
            name: 'Valorant Immortal',
            game: 'Valorant',
            price: 104.99,
            originalPrice: 149.99,
            image: 'https://placehold.co/300x200/8A4DFF/ffffff?text=Valorant+Immortal',
            level: 'Immortal',
            features: ['High Rank', 'Premium Skins', 'Radianite'],
            delivery: 'Instant',
            verified: true,
            badge: 'verified'
        },
        {
            id: 3,
            name: 'PUBG Conqueror',
            game: 'PUBG Mobile',
            price: 59.99,
            originalPrice: 79.99,
            image: 'https://placehold.co/300x200/FF6B6B/ffffff?text=PUBG+Conqueror',
            level: 'Conqueror',
            features: ['Top Rank', 'Premium Outfits', 'UC'],
            delivery: 'Instant',
            verified: true,
            badge: 'new'
        }
    ]
};

// ===== DOM ELEMENTS =====
const elements = {
    cartBtn: document.getElementById('cartBtn'),
    cartSidebar: document.getElementById('cartSidebar'),
    cartClose: document.getElementById('cartClose'),
    cartCount: document.getElementById('cartCount'),
    cartItems: document.getElementById('cartItems'),
    cartTotal: document.getElementById('cartTotal'),
    userBtn: document.getElementById('userBtn'),
    userDropdown: document.getElementById('userDropdown'),
    searchInput: document.querySelector('.search-input'),
    searchSuggestions: document.getElementById('searchSuggestions'),
    mobileMenuToggle: document.getElementById('mobileMenuToggle')
};

// ===== UTILITY FUNCTIONS =====
const utils = {
    formatPrice: (price) => `$${price.toFixed(2)}`,
    
    debounce: (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    showNotification: (message, type = 'success') => {
        try{
            if (window.Swal){
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type === 'success' ? 'success' : 'error',
                    title: message,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            } else {
                alert(message);
            }
        }catch(e){ try{ alert(message); }catch(_){} }
    },
    
    animateElement: (element, animation) => {
        element.style.animation = 'none';
        element.offsetHeight; // Trigger reflow
        element.style.animation = animation;
    }
};

// ===== CART FUNCTIONALITY =====
const cart = {
    add: (product) => {
        // Validate product data
        if (!product || !product.id || !product.name || !product.price) {
            console.error('Invalid product data:', product);
            utils.showNotification('Error adding item to cart', 'error');
            return;
        }
        
        const existingItem = state.cart.find(item => item.id === product.id);
        const quantityToAdd = Math.max(1, product.quantity || 1); // Ensure at least 1
        
        if (existingItem) {
            // If product already exists, increment quantity
            existingItem.quantity += quantityToAdd;
        } else {
            // Add new product with validated quantity
            state.cart.push({ ...product, quantity: quantityToAdd });
        }
        
        saveCartToStorage(state.cart);
        cart.updateUI();
        utils.showNotification(`${product.name} added to cart!`);
        
        // Add to cart animation
        const cartBtn = elements.cartBtn;
        if (cartBtn) {
            utils.animateElement(cartBtn, 'bounce 0.6s ease');
        }
    },
    
    remove: (productId) => {
        state.cart = state.cart.filter(item => item.id !== productId);
        saveCartToStorage(state.cart);
        cart.updateUI();
        utils.showNotification('Item removed from cart');
    },
    
    updateQuantity: (productId, quantity) => {
        const item = state.cart.find(item => item.id === productId);
        if (item) {
            if (quantity <= 0) {
                cart.remove(productId);
            } else {
                item.quantity = quantity;
                saveCartToStorage(state.cart);
                cart.updateUI();
            }
        }
    },
    
    getTotal: () => {
        return state.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    },
    
    getItemCount: () => {
        return state.cart.reduce((total, item) => total + item.quantity, 0);
    },
    
    updateUI: () => {
        // Update cart count
        if (elements.cartCount) {
            elements.cartCount.textContent = cart.getItemCount();
        }
        // Update cart total
        if (elements.cartTotal) {
            elements.cartTotal.textContent = utils.formatPrice(cart.getTotal());
        }
        // Update cart items
        if (elements.cartItems) {
            cart.renderItems();
        }
    },
    
    renderItems: () => {
        if (!elements.cartItems) return;
        if (state.cart.length === 0) {
            elements.cartItems.innerHTML = `
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Your cart is empty</p>
                    <a href="shop.php" class="btn btn-primary">Start Shopping</a>
                </div>
            `;
            return;
        }
        
        elements.cartItems.innerHTML = state.cart.map(item => `
            <div class="cart-item">
                <div class="cart-item-image">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p>${item.game} • ${item.level}</p>
                    <div class="cart-item-price">${utils.formatPrice(item.price)}</div>
                </div>
                <div class="cart-item-controls">
                    <button class="quantity-btn" onclick="cart.updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                    <span class="quantity">${item.quantity}</span>
                    <button class="quantity-btn" onclick="cart.updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                    <button class="remove-btn" onclick="cart.remove(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    },
    
    open: () => {
        elements.cartSidebar.classList.add('open');
        document.body.style.overflow = 'hidden';
    },
    
    close: () => {
        elements.cartSidebar.classList.remove('open');
        document.body.style.overflow = 'auto';
    }
};

// ===== SEARCH FUNCTIONALITY =====
const search = {
    init: () => {
        if (elements.searchInput) {
            elements.searchInput.addEventListener('input', utils.debounce(search.handleInput, 300));
            elements.searchInput.addEventListener('focus', search.showSuggestions);
            elements.searchInput.addEventListener('blur', search.hideSuggestions);
        }
    },
    
    handleInput: (e) => {
        const query = e.target.value.toLowerCase();
        
        if (query.length < 2) {
            search.hideSuggestions();
            return;
        }
        
        const suggestions = state.searchSuggestions.filter(item => 
            item.toLowerCase().includes(query)
        );
        
        search.renderSuggestions(suggestions);
    },
    
    renderSuggestions: (suggestions) => {
        if (suggestions.length === 0) {
            search.hideSuggestions();
            return;
        }
        
        elements.searchSuggestions.innerHTML = suggestions.map(suggestion => `
            <div class="suggestion-item" onclick="search.selectSuggestion('${suggestion}')">
                <i class="fas fa-search"></i>
                <span>${suggestion}</span>
            </div>
        `).join('');
        
        elements.searchSuggestions.style.display = 'block';
    },
    
    selectSuggestion: (suggestion) => {
        elements.searchInput.value = suggestion;
        search.hideSuggestions();
        // Navigate to shop page with search query
        window.location.href = `shop.php?search=${encodeURIComponent(suggestion)}`;
    },
    
    showSuggestions: () => {
        if (elements.searchSuggestions.children.length > 0) {
            elements.searchSuggestions.style.display = 'block';
        }
    },
    
    hideSuggestions: () => {
        setTimeout(() => {
            elements.searchSuggestions.style.display = 'none';
        }, 200);
    }
};

// ===== USER DROPDOWN =====
const userDropdown = {
    isOpen: false,
    init: () => {
        if (elements.userBtn && elements.userDropdown) {
            // Ensure dropdown is at body level and above other content
            elements.userDropdown.style.display = 'none';
            try { if (elements.userDropdown.parentElement !== document.body) document.body.appendChild(elements.userDropdown); } catch (_) {}
            elements.userDropdown.style.zIndex = '3000';
            elements.userBtn.addEventListener('click', (e) => {
                e.preventDefault();
                userDropdown.toggle();
            });
            document.addEventListener('click', (e) => {
                if (!elements.userBtn.contains(e.target) && !elements.userDropdown.contains(e.target)) {
                    userDropdown.close();
                }
            });
            const ro = () => { if (userDropdown.isOpen) userDropdown.position(); };
            window.addEventListener('scroll', ro, { passive: true });
            window.addEventListener('resize', ro);
        }
    },
    position: () => {
        const btnRect = elements.userBtn.getBoundingClientRect();
        elements.userDropdown.style.position = 'fixed';
        elements.userDropdown.style.left = '';
        const right = Math.max(8, window.innerWidth - btnRect.right - 8);
        elements.userDropdown.style.top = `${Math.round(btnRect.bottom + 8)}px`;
        elements.userDropdown.style.right = `${Math.round(right)}px`;
        elements.userDropdown.style.minWidth = '240px';
    },
    open: () => {
        if (userDropdown.isOpen) return;
        elements.userDropdown.style.display = 'block';
        userDropdown.position();
        if (window.gsap) {
            gsap.set(elements.userDropdown, { opacity: 0, y: -8, scale: 0.96, transformOrigin: 'top right' });
            gsap.to(elements.userDropdown, { opacity: 1, y: 0, scale: 1, duration: 0.18, ease: 'power2.out' });
        }
        userDropdown.isOpen = true;
        // Reposition once more after layout settles
        setTimeout(userDropdown.position, 0);
    },
    close: () => {
        if (!userDropdown.isOpen) return;
        if (window.gsap) {
            gsap.to(elements.userDropdown, { opacity: 0, y: -6, scale: 0.98, duration: 0.15, ease: 'power1.out', onComplete: () => {
                elements.userDropdown.style.display = 'none';
            }});
        } else {
            elements.userDropdown.style.display = 'none';
        }
        userDropdown.isOpen = false;
    },
    toggle: () => {
        if (userDropdown.isOpen) userDropdown.close(); else userDropdown.open();
    }
};

// ===== MOBILE MENU =====
const mobileMenu = {
    init: () => {
        if (elements.mobileMenuToggle) {
            elements.mobileMenuToggle.addEventListener('click', mobileMenu.toggle);
        }
    },
    
    toggle: () => {
        const nav = document.querySelector('.nav');
        nav.classList.toggle('mobile-open');
        
        // Animate hamburger menu
        const spans = elements.mobileMenuToggle.querySelectorAll('span');
        spans.forEach((span, index) => {
            if (nav.classList.contains('mobile-open')) {
                if (index === 0) span.style.transform = 'rotate(45deg) translate(5px, 5px)';
                if (index === 1) span.style.opacity = '0';
                if (index === 2) span.style.transform = 'rotate(-45deg) translate(7px, -6px)';
            } else {
                span.style.transform = 'none';
                span.style.opacity = '1';
            }
        });
    }
};

// ===== SMOOTH SCROLLING =====
const smoothScroll = {
    init: () => {
        // Handle anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
};

// ===== LAZY LOADING =====
const lazyLoading = {
    init: () => {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('loading');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => {
            img.classList.add('loading');
            imageObserver.observe(img);
        });
    }
};

// ===== ANIMATIONS ON SCROLL =====
const scrollAnimations = {
    init: () => {
        const animatedElements = document.querySelectorAll('.category-card, .deal-card, .review-card');
        
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });
        
        animatedElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            animationObserver.observe(el);
        });
    }
};

// ===== FORM VALIDATION =====
const formValidation = {
    init: () => {
        // Only attach to forms that explicitly opt-in
        const forms = document.querySelectorAll('form[data-validate="true"], form.js-validate');
        forms.forEach(form => form.addEventListener('submit', formValidation.handleSubmit));
    },
    
    handleSubmit: (e) => {
        // allow native submission unless explicitly validated
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        // Basic validation
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else {
                field.classList.remove('error');
            }
        });
        
        if (isValid) {
            // Proceed with normal submit after basic validation feedback
            form.removeEventListener('submit', formValidation.handleSubmit);
            form.submit();
        } else {
            utils.showNotification('Please fill in all required fields', 'error');
        }
    }
};

// ===== CURRENCY CONVERTER =====
const currencyConverter = {
    rates: {
        USD: 1,
        PKR: 280
    },
    
    currentCurrency: 'USD',
    
    init: () => {
        // Add currency toggle if needed
        const currencyToggle = document.getElementById('currencyToggle');
        if (currencyToggle) {
            currencyToggle.addEventListener('click', currencyConverter.toggle);
        }
    },
    
    toggle: () => {
        currencyConverter.currentCurrency = currencyConverter.currentCurrency === 'USD' ? 'PKR' : 'USD';
        currencyConverter.updatePrices();
    },
    
    convert: (amount, from = 'USD', to = currencyConverter.currentCurrency) => {
        const usdAmount = from === 'PKR' ? amount / currencyConverter.rates.PKR : amount;
        return to === 'PKR' ? usdAmount * currencyConverter.rates.PKR : usdAmount;
    },
    
    updatePrices: () => {
        const priceElements = document.querySelectorAll('.price, .sale-price, .original-price');
        priceElements.forEach(el => {
            const usdPrice = parseFloat(el.textContent.replace('$', ''));
            const convertedPrice = currencyConverter.convert(usdPrice);
            const symbol = currencyConverter.currentCurrency === 'PKR' ? '₨' : '$';
            el.textContent = `${symbol}${convertedPrice.toFixed(2)}`;
        });
    }
};

// ===== PERFORMANCE OPTIMIZATIONS =====
const performance = {
    init: () => {
        // Preload critical images
        const criticalImages = [
            'https://placehold.co/300x200/1FB6FF/ffffff?text=Fortnite',
            'https://placehold.co/300x200/8A4DFF/ffffff?text=Valorant'
        ];
        
        criticalImages.forEach(src => {
            const img = new Image();
            img.src = src;
        });
        
        // Optimize scroll performance
        let ticking = false;
        const updateScroll = () => {
            // Add scroll-based animations here
            ticking = false;
        };
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateScroll);
                ticking = true;
            }
        });
    }
};

// ===== ACCESSIBILITY ENHANCEMENTS =====
const accessibility = {
    init: () => {
        // Add keyboard navigation for custom elements
        const interactiveElements = document.querySelectorAll('.btn, .category-card, .deal-card');
        
        interactiveElements.forEach(el => {
            el.setAttribute('tabindex', '0');
            
            el.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    el.click();
                }
            });
        });
        
        // Add ARIA labels
        const cartBtn = elements.cartBtn;
        if (cartBtn) {
            cartBtn.setAttribute('aria-label', `Shopping cart with ${cart.getItemCount()} items`);
        }
        
        // Announce cart updates to screen readers
        const announceCartUpdate = () => {
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('aria-atomic', 'true');
            announcement.className = 'sr-only';
            announcement.textContent = `Cart updated. ${cart.getItemCount()} items in cart.`;
            document.body.appendChild(announcement);
            
            setTimeout(() => {
                document.body.removeChild(announcement);
            }, 1000);
        };
        
        // Override cart update to include announcement
        const originalUpdateUI = cart.updateUI;
        cart.updateUI = () => {
            originalUpdateUI();
            announceCartUpdate();
        };
    }
};

// ===== INITIALIZATION =====
const initAll = () => {
    // Initialize all modules
    cart.updateUI();
    search.init();
    userDropdown.init();
    mobileMenu.init();
    smoothScroll.init();
    lazyLoading.init();
    scrollAnimations.init();
    formValidation.init();
    currencyConverter.init();
    performance.init();
    accessibility.init();

    // Add loading states
    document.body.classList.add('loaded');
    
    // Initialize cart functionality - redirect to modern cart page
    if (elements.cartBtn) {
        elements.cartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // Redirect to modern cart page
            window.location.href = 'cart-new.php';
        });
    }
    
    if (elements.cartClose) {
        elements.cartClose.addEventListener('click', cart.close);
    }
    
    // Close cart when clicking outside
    if (elements.cartSidebar) {
        elements.cartSidebar.addEventListener('click', (e) => {
            if (e.target === elements.cartSidebar) {
                cart.close();
            }
        });
    }
    
    // Add click handlers for buy buttons
    document.querySelectorAll('.btn-primary').forEach(btn => {
        if (btn.textContent.includes('Buy Now') || btn.textContent.includes('Shop')) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                // Add sample product to cart for demo
                if (state.products.length > 0) {
                    cart.add(state.products[0]);
                }
            });
        }
    });
    
    console.log('Just4U Gaming website initialized successfully!');
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  // DOM already ready
  initAll();
}

// ===== GLOBAL FUNCTIONS (for HTML onclick handlers) =====
window.cart = cart;
window.search = search;
window.utils = utils;

// ===== ERROR HANDLING =====
window.addEventListener('error', (e) => {
    console.error('JavaScript error:', e.error);
    // You could send this to an error tracking service
});

// ===== SERVICE WORKER REGISTRATION (for PWA features) =====
const ENABLE_SW = false; // set to true when sw.js exists
if (ENABLE_SW && 'serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js');
            console.log('SW registered: ', registration);
        } catch (e) {
            console.log('SW registration skipped:', e && e.message ? e.message : e);
        }
    });
}
