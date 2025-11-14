// ===== SHOP PAGE FUNCTIONALITY =====

// Loaded dynamically from the server
let shopProducts = [];

// Shop state
const shopState = {
    currentProducts: [...shopProducts],
    filteredProducts: [...shopProducts],
    currentPage: 1,
    productsPerPage: 8,
    currentView: 'grid',
    sortBy: 'popularity',
    filters: {
        game: [],
        platform: [],
        rank: [],
        priceMin: 0,
        priceMax: 1000
    }
};

// ===== SHOP FUNCTIONALITY =====
const shop = {
    init: async () => {
        await shop.loadFromServer();
        await shop.loadGamesFilter();
        shop.applyUrlPrefilters();
        shop.renderProducts();
        shop.initFilters();
        shop.initSorting();
        shop.initViewToggle();
        shop.initLoadMore();
        shop.updateResultsCount();
    },

    loadGamesFilter: async () => {
        try {
            const res = await fetch('shop_list_games.php', { credentials: 'same-origin' });
            const json = await res.json();
            if (!res.ok || !json.ok) return;
            const container = document.getElementById('gameFilterOptions');
            if (!container) return;
            container.innerHTML = (json.games || []).map(g => `
                <label class="filter-option">
                    <input type="checkbox" name="game" value="${g.slug.toLowerCase()}">
                    <span class="checkmark"></span>
                    <span>${g.name}</span>
                    <span class="count">(${g.count})</span>
                </label>
            `).join('');
        } catch (e) {
            // ignore silently
        }
    },

    applyUrlPrefilters: () => {
        const params = new URLSearchParams(window.location.search);
        const gameParam = params.get('game');
        const search = params.get('search');

        // Map legacy game values to current slugs
        const mapLegacy = (val) => {
            const v = (val || '').toLowerCase().trim();
            if (!v) return v;
            // If it already exists among checkbox values, keep it
            const exists = !!document.querySelector(`input[name="game"][value="${v}"]`);
            if (exists) return v;
            const map = {
                'pubg': 'pubg-mobile',
                'pubg mobile': 'pubg-mobile',
                'gta v': 'gta',
                'gta5': 'gta',
                'gta 5': 'gta',
                'cs2': 'cs2',
                'counter-strike 2': 'cs2',
                'counter strike 2': 'cs2'
            };
            return map[v] || v;
        };

        if (gameParam) {
            const values = gameParam.split(',').map(s => mapLegacy(s));
            values.forEach(val => {
                const cb = document.querySelector(`input[name="game"][value="${val}"]`);
                if (cb && !cb.checked) {
                    cb.checked = true;
                    shop.handleFilterChange({ target: cb });
                }
            });
        }

        if (search) {
            const searchInput = document.getElementById('shopSearch');
            if (searchInput) {
                searchInput.value = search;
                shopSearch.handleSearch({ target: searchInput });
            }
        }
    },

    buildQueryParams: () => {
        const params = new URLSearchParams();
        // arrays: game, platform
        if (shopState.filters.game.length) params.append('game', shopState.filters.game.join(','));
        if (shopState.filters.platform.length) params.append('platform', shopState.filters.platform.join(','));
        if (shopState.filters.priceMin > 0) params.append('priceMin', String(shopState.filters.priceMin));
        if (shopState.filters.priceMax && shopState.filters.priceMax < 100000) params.append('priceMax', String(shopState.filters.priceMax));
        params.append('sort', shopState.sortBy);
        return params.toString();
    },

    loadFromServer: async () => {
        try {
            const qs = shop.buildQueryParams();
            const url = qs ? `shop_list_products.php?${qs}` : 'shop_list_products.php';
            const res = await fetch(url, { credentials: 'same-origin' });
            const json = await res.json();
            if (res.ok && json.ok){
                shopProducts = json.products || [];
                shopState.currentProducts = [...shopProducts];
                shopState.filteredProducts = [...shopProducts];
            } else {
                console.warn('Failed to load products:', json.error || res.statusText);
            }
        } catch (e) {
            console.warn('Network error loading products');
        }
    },
    
    renderProducts: () => {
        const productsGrid = document.getElementById('productsGrid');
        if (!productsGrid) return;
        
        const startIndex = (shopState.currentPage - 1) * shopState.productsPerPage;
        const endIndex = startIndex + shopState.productsPerPage;
        const productsToShow = shopState.filteredProducts.slice(startIndex, endIndex);
        
        if (productsToShow.length === 0) {
            productsGrid.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No accounts found</h3>
                    <p>Try adjusting your filters or search terms</p>
                    <button class="btn btn-primary" onclick="shop.clearFilters()">Clear Filters</button>
                </div>
            `;
            return;
        }
        
        productsGrid.innerHTML = productsToShow.map(product => shop.createProductCard(product)).join('');
        
        // Add click handlers for buy buttons
        productsGrid.querySelectorAll('.product-buy-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Validate cart object exists
                if (typeof cart === 'undefined' || !cart.add) {
                    console.error('Cart functionality not loaded');
                    alert('Cart functionality is not available. Please refresh the page.');
                    return;
                }
                
                const productId = parseInt(btn.dataset.productId);
                const product = shopProducts.find(p => p.id === productId);
                if (product) {
                    cart.add(product);
                } else {
                    console.error('Product not found:', productId);
                    alert('Product not found. Please try again.');
                }
            });
        });
    },
    
    createProductCard: (product) => {
        const discount = product.originalPrice ? 
            Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100) : 0;
        
        const sellerAvatar = product.sellerAvatar || (product.seller ? `https://placehold.co/40x40/1FB6FF/ffffff?text=${String(product.seller).charAt(0).toUpperCase()}` : 'https://placehold.co/40x40/1FB6FF/ffffff?text=S');
        const sRating = (typeof product.sellerRating === 'number' && !isNaN(product.sellerRating)) ? product.sellerRating : 0;
        const sStars = Math.round(sRating);
        return `
            <div class="product-card ${shopState.currentView === 'list' ? 'list-view' : ''}">
                <div class="product-image">
                    <a href="product.php?id=${product.id}">
                        <img src="${product.image}" alt="${product.name}" loading="lazy">
                    </a>
                    ${product.badge ? `<div class="product-badge ${product.badge}">${product.badge}</div>` : ''}
                    ${discount > 0 ? `<div class="product-discount">-${discount}%</div>` : ''}
                </div>
                <div class="product-info">
                    <div class="product-header">
                        <h3 class="product-name"><a href="product.php?id=${product.id}" style="color:inherit;text-decoration:none;">${product.name}</a></h3>
                        <div class="seller-column">
                            <div class="seller-top">
                                <img class="seller-avatar" src="${sellerAvatar}" alt="${(product.seller||'Seller').replace(/</g,'&lt;')}">
                                <div class="seller-name">${product.seller || 'Seller'}</div>
                            </div>
                            <div class="seller-rating">
                                <div class="stars">${'★'.repeat(sStars)}${'☆'.repeat(5 - sStars)}</div>
                                <span class="rating-text">${(sRating || 0).toFixed(1)}</span>
                            </div>
                        </div>
                    </div>
                    <div class="product-details">
                        <div class="product-meta">
                            <span class="game">${product.game}</span>
                            <span class="platform">${product.platform}</span>
                            <span class="region">${product.region}</span>
                        </div>
                        ${Array.isArray(product.features) && product.features.length ? `<div class="product-features">${product.features.slice(0,3).map(f=>`<span class="feature">${f}</span>`).join('')}</div>` : ''}
                        <div class="product-level">
                            <i class="fas fa-trophy"></i>
                            <span>${product.level || ''}</span>
                        </div>
                    </div>
                    <div class="product-footer">
                        <div class="product-price">
                            ${product.originalPrice ? `
                                <span class="original-price">$${product.originalPrice.toFixed(2)}</span>
                            ` : ''}
                            <span class="current-price">$${product.price.toFixed(2)}</span>
                        </div>
                        <div class="product-delivery">
                            <i class="fas fa-${product.delivery === 'Instant' ? 'bolt' : 'clock'}"></i>
                            <span>${product.delivery}</span>
                        </div>
                        <div class="product-actions">
                            <button class="action-btn product-buy-btn" title="Add to Cart" data-product-id="${product.id}"><i class="fas fa-shopping-cart"></i></button>
                            <button class="action-btn product-view-btn" title="Quick View" onclick="shop.viewProduct(${product.id})"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },
    
    initFilters: () => {
        // Game filters
        document.querySelectorAll('input[name="game"]').forEach(checkbox => {
            checkbox.addEventListener('change', shop.handleFilterChange);
        });
        
        // Platform filters
        document.querySelectorAll('input[name="platform"]').forEach(checkbox => {
            checkbox.addEventListener('change', shop.handleFilterChange);
        });
        
        // Delivery filters removed
        
        // Rank filters
        document.querySelectorAll('input[name="rank"]').forEach(checkbox => {
            checkbox.addEventListener('change', shop.handleFilterChange);
        });
        
        // Price range
        const priceMin = document.getElementById('priceMin');
        const priceMax = document.getElementById('priceMax');
        const priceRange = document.getElementById('priceRange');
        
        if (priceMin) priceMin.addEventListener('input', shop.handlePriceChange);
        if (priceMax) priceMax.addEventListener('input', shop.handlePriceChange);
        if (priceRange) priceRange.addEventListener('input', shop.handlePriceRangeChange);
        
        // Clear filters
        const clearFilters = document.getElementById('clearFilters');
        if (clearFilters) {
            clearFilters.addEventListener('click', shop.clearFilters);
        }
    },
    
    handleFilterChange: (e) => {
        const filterType = e.target.name;
        const filterValue = e.target.value;
        
        if (e.target.checked) {
            if (!shopState.filters[filterType].includes(filterValue)) {
                shopState.filters[filterType].push(filterValue);
            }
        } else {
            shopState.filters[filterType] = shopState.filters[filterType].filter(v => v !== filterValue);
        }
        
        shop.applyFilters();
    },
    
    handlePriceChange: (e) => {
        const isMin = e.target.id === 'priceMin';
        const value = parseFloat(e.target.value) || 0;
        
        if (isMin) {
            shopState.filters.priceMin = value;
        } else {
            shopState.filters.priceMax = value;
        }
        
        shop.applyFilters();
    },
    
    handlePriceRangeChange: (e) => {
        const value = parseFloat(e.target.value);
        shopState.filters.priceMax = value;
        
        const priceMaxInput = document.getElementById('priceMax');
        if (priceMaxInput) {
            priceMaxInput.value = value;
        }
        
        shop.applyFilters();
    },
    
    applyFilters: async () => {
        await shop.loadFromServer();
        shopState.currentPage = 1;
        shop.renderProducts();
        shop.updateResultsCount();
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (loadMoreBtn) loadMoreBtn.style.display = '';
    },
    
    clearFilters: () => {
        // Reset all checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Reset price inputs
        const priceMin = document.getElementById('priceMin');
        const priceMax = document.getElementById('priceMax');
        const priceRange = document.getElementById('priceRange');
        
        if (priceMin) priceMin.value = '';
        if (priceMax) priceMax.value = '';
        if (priceRange) priceRange.value = 500;
        
        // Reset filters state
        shopState.filters = {
            game: [],
            platform: [],
            
            rank: [],
            priceMin: 0,
            priceMax: 1000
        };
        
        shop.applyFilters();
    },
    
    initSorting: () => {
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                shopState.sortBy = e.target.value;
                // Re-fetch with server sort for consistency
                shop.applyFilters();
            });
        }
    },
    
    sortProducts: () => {
        switch (shopState.sortBy) {
            case 'price-low':
                shopState.filteredProducts.sort((a, b) => a.price - b.price);
                break;
            case 'price-high':
                shopState.filteredProducts.sort((a, b) => b.price - a.price);
                break;
            case 'newest':
                shopState.filteredProducts.sort((a, b) => b.id - a.id);
                break;
            case 'rating':
                shopState.filteredProducts.sort((a, b) => b.rating - a.rating);
                break;
            case 'popularity':
            default:
                shopState.filteredProducts.sort((a, b) => b.reviews - a.reviews);
                break;
        }
        
        shopState.currentPage = 1;
        shop.renderProducts();
    },
    
    initViewToggle: () => {
        const viewButtons = document.querySelectorAll('.view-btn');
        const productsGrid = document.getElementById('productsGrid');
        
        viewButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Update active state
                viewButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                
                // Update view state
                shopState.currentView = e.target.dataset.view;
                
                // Update grid class
                if (productsGrid) {
                    productsGrid.className = `products-grid ${shopState.currentView}-view`;
                }
                
                // Re-render products
                shop.renderProducts();
            });
        });
    },
    
    initLoadMore: () => {
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => {
                shopState.currentPage++;
                shop.renderProducts();
                
                // Hide button if no more products
                const totalPages = Math.ceil(shopState.filteredProducts.length / shopState.productsPerPage);
                if (shopState.currentPage >= totalPages) {
                    loadMoreBtn.style.display = 'none';
                }
            });
        }
    },
    
    updateResultsCount: () => {
        const resultsCount = document.getElementById('resultsCount');
        if (resultsCount) {
            const count = shopState.filteredProducts.length;
            resultsCount.textContent = `Showing ${count} account${count !== 1 ? 's' : ''}`;
        }
    },
    
    viewProduct: (productId) => {
        // Navigate to product detail page
        window.location.href = `product.php?id=${productId}`;
    }
};

// ===== SEARCH FUNCTIONALITY FOR SHOP =====
const shopSearch = {
    init: () => {
        const searchInput = document.getElementById('shopSearch');
        if (searchInput) {
            searchInput.addEventListener('input', utils.debounce(shopSearch.handleSearch, 300));
        }
    },
    
    handleSearch: (e) => {
        const query = e.target.value.toLowerCase();
        
        if (query.length < 2) {
            shopState.filteredProducts = [...shopProducts];
        } else {
            shopState.filteredProducts = shopProducts.filter(product => 
                product.name.toLowerCase().includes(query) ||
                product.game.toLowerCase().includes(query) ||
                product.features.some(feature => feature.toLowerCase().includes(query))
            );
        }
        
        shopState.currentPage = 1;
        shop.renderProducts();
        shop.updateResultsCount();
    }
};

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    shop.init();
    shopSearch.init();
    
    // Handle URL parameters for pre-filtering
    const urlParams = new URLSearchParams(window.location.search);
    const game = urlParams.get('game');
    const search = urlParams.get('search');
    
    if (game) {
        const gameCheckbox = document.querySelector(`input[name="game"][value="${game}"]`);
        if (gameCheckbox) {
            gameCheckbox.checked = true;
            shop.handleFilterChange({ target: gameCheckbox });
        }
    }
    
    if (search) {
        const searchInput = document.getElementById('shopSearch');
        if (searchInput) {
            searchInput.value = search;
            shopSearch.handleSearch({ target: searchInput });
        }
    }
    
    console.log('Shop page initialized successfully!');
});
