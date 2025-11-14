<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Gaming Accounts | Just4U - Premium Gaming Accounts</title>
    <meta name="description" content="Browse and buy verified gaming accounts for Fortnite, Valorant, PUBG, GTA V, and more. Instant delivery, secure checkout, premium quality accounts.">
    <meta name="keywords" content="gaming accounts, fortnite accounts, valorant accounts, pubg accounts, gta accounts, instant delivery, verified accounts">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="style.css">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "CollectionPage",
        "name": "Gaming Accounts Shop",
        "description": "Premium gaming accounts for popular games",
        "url": "https://just4u.com/shop",
        "mainEntity": {
            "@type": "ItemList",
            "itemListElement": [
                {
                    "@type": "Product",
                    "name": "Fortnite Pro Account",
                    "description": "High-level Fortnite account with rare skins",
                    "offers": {
                        "@type": "Offer",
                        "price": "99.99",
                        "priceCurrency": "USD"
                    }
                }
            ]
        }
    }
    </script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <div class="logo">
                    <a href="index.php">
                        <span class="logo-text">Just4U</span>
                        <span class="logo-accent">Gaming</span>
                    </a>
                </div>
                
                <!-- Navigation -->
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="index.php" class="nav-link">Home</a></li>
                        <li><a href="shop.php" class="nav-link active">Shop</a></li>
                        <li><a href="about.php" class="nav-link">About</a></li>
                        <li><a href="support.php" class="nav-link">Support</a></li>
                    </ul>
                </nav>
                
                <!-- Search & Cart -->
                <div class="header-actions">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search games, accounts..." id="shopSearch">
                        <button class="search-btn"><i class="fas fa-search"></i></button>
                        <div class="search-suggestions" id="searchSuggestions"></div>
                    </div>
                    
                    <?php if (!empty($_SESSION['user_id'])): ?>
                    <div class="cart-container">
                        <button class="cart-btn" id="cartBtn">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cartCount">0</span>
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="user-actions">
                        <button class="user-btn" id="userBtn">
                            <?php if (!empty($_SESSION['avatar_url'])): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['avatar_url']); ?>" alt="Avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                            <?php else: ?>
                                <i class="fas fa-user"></i>
                            <?php endif; ?>
                        </button>
                        <!-- User Dropdown -->
                        <div class="user-dropdown" id="userDropdown">
                            <div class="dropdown-content">
                                <a href="account.php" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    My Profile
                                </a>
                                <a href="orders.php" class="dropdown-item">
                                    <i class="fas fa-shopping-bag"></i>
                                    My Orders
                                </a>
                                <a href="support.php" class="dropdown-item">
                                    <i class="fas fa-headset"></i>
                                    Support
                                </a>
                                <div class="dropdown-divider"></div>
                                <?php if (!empty($_SESSION['user_id'])): ?>
                                <a href="logout.php" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </a>
                                <?php else: ?>
                                <a href="login.php" class="dropdown-item">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Login
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Shop Header -->
    <section class="shop-header">
        <div class="container">
            <div class="shop-header-content">
                <div class="breadcrumb">
                    <a href="index.php">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Shop</span>
                </div>
                <h1 class="page-title">Gaming Accounts</h1>
                <p class="page-subtitle">Choose from our verified collection of premium gaming accounts</p>
            </div>
        </div>
    </section>

    <!-- Shop Content -->
    <section class="shop-content">
        <div class="container">
            <div class="shop-layout">
                <!-- Filters Sidebar -->
                <aside class="filters-sidebar">
                    <div class="filters-header">
                        <h3>Filters</h3>
                        <button class="clear-filters" id="clearFilters">Clear All</button>
                    </div>
                    
                    <!-- Game Filter -->
                    <div class="filter-group">
                        <h4>Game</h4>
                        <div class="filter-options" id="gameFilterOptions">
                            <!-- Dynamically populated by shop.js -->
                        </div>
                    </div>
                    
                    <!-- Platform Filter -->
                    <div class="filter-group">
                        <h4>Platform</h4>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" name="platform" value="pc">
                                <span class="checkmark"></span>
                                <span>PC</span>
                                <span class="count">(320)</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="platform" value="mobile">
                                <span class="checkmark"></span>
                                <span>Mobile</span>
                                <span class="count">(180)</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="platform" value="console">
                                <span class="checkmark"></span>
                                <span>Console</span>
                                <span class="count">(45)</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="platform" value="cross-platform">
                                <span class="checkmark"></span>
                                <span>Cross Platform</span>
                                <span class="count"></span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="filter-group">
                        <h4>Price Range</h4>
                        <div class="price-range">
                            <div class="price-inputs">
                                <input type="number" placeholder="Min" id="priceMin" min="0" max="1000">
                                <span>-</span>
                                <input type="number" placeholder="Max" id="priceMax" min="0" max="1000">
                            </div>
                            <div class="price-slider">
                                <input type="range" id="priceRange" min="0" max="1000" value="500">
                                <div class="price-labels">
                                    <span>$0</span>
                                    <span>$1000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rank/Level Filter -->
                    <div class="filter-group">
                        <h4>Rank/Level</h4>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="checkbox" name="rank" value="high">
                                <span class="checkmark"></span>
                                <span>High Rank</span>
                                <span class="count">(180)</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="rank" value="medium">
                                <span class="checkmark"></span>
                                <span>Medium Rank</span>
                                <span class="count">(220)</span>
                            </label>
                            <label class="filter-option">
                                <input type="checkbox" name="rank" value="low">
                                <span class="checkmark"></span>
                                <span>Low Rank</span>
                                <span class="count">(145)</span>
                            </label>
                        </div>
                    </div>
                </aside>
                
                <!-- Products Section -->
                <main class="products-section">
                    <!-- Sort & View Options -->
                    <div class="products-header">
                        <div class="results-info">
                            <span id="resultsCount">Showing 545 accounts</span>
                        </div>
                        <div class="view-controls">
                            <div class="sort-dropdown">
                                <select id="sortSelect">
                                    <option value="popularity">Most Popular</option>
                                    <option value="price-low">Price: Low to High</option>
                                    <option value="price-high">Price: High to Low</option>
                                    <option value="newest">Newest First</option>
                                    <option value="rating">Highest Rated</option>
                                </select>
                            </div>
                            <div class="view-toggle">
                                <button class="view-btn active" data-view="grid">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button class="view-btn" data-view="list">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Products Grid -->
                    <div class="products-grid" id="productsGrid">
                        <!-- Product cards will be populated by JavaScript -->
                    </div>
                    
                    <!-- Load More -->
                    <div class="load-more">
                        <button class="btn btn-secondary" id="loadMoreBtn">
                            <span>Load More Accounts</span>
                            <i class="fas fa-arrow-down"></i>
                        </button>
                    </div>
                </main>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <span class="logo-text">Just4U</span>
                        <span class="logo-accent">Gaming</span>
                    </div>
                    <p class="footer-description">
                        Your trusted source for premium gaming accounts. 
                        Verified, secure, and delivered instantly.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-discord"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="support.php">Support</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        <li><a href="shop.php?game=fortnite">Fortnite</a></li>
                        <li><a href="shop.php?game=valorant">Valorant</a></li>
                        <li><a href="shop.php?game=pubg">PUBG Mobile</a></li>
                        <li><a href="shop.php?game=gta">GTA V</a></li>
                        <li><a href="shop.php?game=cs2">Counter-Strike 2</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="support.php">Help Center</a></li>
                        <li><a href="support.php#contact">Contact Us</a></li>
                        <li><a href="support.php#faq">FAQ</a></li>
                        <li><a href="support.php#disputes">Dispute Resolution</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; 2024 Just4U Gaming. All rights reserved.</p>
                    <div class="footer-bottom-links">
                        <a href="privacy.php">Privacy</a>
                        <a href="terms.php">Terms</a>
                        <a href="cookies.php">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>Shopping Cart</h3>
            <button class="cart-close" id="cartClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Cart items will be populated here -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total: <span id="cartTotal">$0.00</span></span>
            </div>
            <button class="btn btn-primary cart-checkout">Checkout</button>
        </div>
    </div>

    

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js"></script>
    <script src="shop.js"></script>
</body>
</html>
