<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Just4U - Premium Gaming Accounts | Ready To Play</title>
    <meta name="description" content="Buy verified gaming accounts for Fortnite, PUBG, Valorant, GTA and more. Instant delivery, secure checkout, premium quality accounts.">
    <meta name="keywords" content="gaming accounts, fortnite accounts, pubg accounts, valorant accounts, gta accounts, instant delivery">
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
        "@type": "WebSite",
        "name": "Just4U",
        "description": "Premium Gaming Accounts - Ready To Play",
        "url": "https://just4u.com",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "https://just4u.com/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
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
                        <li><a href="index.php" class="nav-link active">Home</a></li>
                        <li><a href="shop.php" class="nav-link">Shop</a></li>
                        <li><a href="about.php" class="nav-link">About</a></li>
                        <li><a href="support.php" class="nav-link">Support</a></li>
                    </ul>
                </nav>
                
                <!-- Search & Cart -->
                <div class="header-actions">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search games, accounts...">
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-background">
            <div class="hero-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">
                        <span class="title-main">Premium Gaming Accounts</span>
                        <span class="title-accent">Ready To Play</span>
                    </h1>
                    <p class="hero-subtitle">
                        Verified accounts • Instant delivery • Secure checkout
                    </p>
                    <div class="hero-actions">
                        <a href="shop.php" class="btn btn-primary">
                            <span>Shop Accounts</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="#how-it-works" class="btn btn-secondary">
                            <span>How it Works</span>
                        </a>
                    </div>
                </div>
                
                <div class="hero-visual">
                    <div class="gaming-showcase">
                        <div class="game-card featured">
                            <div class="game-image">
                                <img src="https://images.unsplash.com/photo-1556438064-2d7646166914?w=400&h=300&fit=crop&crop=center&auto=format&q=80" alt="Fortnite Account">
                                <div class="game-badge instant">Instant</div>
                            </div>
                            <div class="game-info">
                                <h3>Fortnite Account</h3>
                                <p>Level 100+ • Rare Skins</p>
                                <div class="price">$29.99</div>
                            </div>
                        </div>
                        
                        <div class="game-card">
                            <div class="game-image">
                                <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?w=300&h=200&fit=crop&crop=center&auto=format&q=80" alt="Valorant Account">
                                <div class="game-badge verified">Verified</div>
                            </div>
                            <div class="game-info">
                                <h3>Valorant</h3>
                                <p>Immortal Rank</p>
                                <div class="price">$45.99</div>
                            </div>
                        </div>
                        
                        <div class="game-card">
                            <div class="game-image">
                                <img src="https://images.unsplash.com/photo-1556438064-2d7646166914?w=300&h=200&fit=crop&crop=center&auto=format&q=80" alt="PUBG Account">
                                <div class="game-badge new">New</div>
                            </div>
                            <div class="game-info">
                                <h3>PUBG Mobile</h3>
                                <p>Conqueror</p>
                                <div class="price">$19.99</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Badges -->
    <section class="trust-badges">
        <div class="container">
            <div class="trust-content">
                <div class="trust-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>100% Secure</span>
                </div>
                <div class="trust-item">
                    <i class="fas fa-bolt"></i>
                    <span>Instant Delivery</span>
                </div>
                <div class="trust-item">
                    <i class="fas fa-medal"></i>
                    <span>Verified Accounts</span>
                </div>
                <div class="trust-item">
                    <i class="fas fa-undo"></i>
                    <span>Money Back Guarantee</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="featured-categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Popular Games</h2>
                <p class="section-subtitle">Choose from our most popular gaming accounts</p>
            </div>
            
            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1556438064-2d7646166914?w=400&h=300&fit=crop&crop=center&auto=format&q=80" alt="Fortnite">
                        <div class="category-overlay">
                            <span class="account-count">150+ Accounts</span>
                        </div>
                    </div>
                    <div class="category-info">
                        <h3>Fortnite</h3>
                        <p>Battle Royale accounts with rare skins</p>
                        <div class="category-stats">
                            <span class="price-range">$15 - $200</span>
                            <span class="delivery-time">Instant</span>
                        </div>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400&h=300&fit=crop&crop=center&auto=format&q=80" alt="Valorant">
                        <div class="category-overlay">
                            <span class="account-count">89+ Accounts</span>
                        </div>
                    </div>
                    <div class="category-info">
                        <h3>Valorant</h3>
                        <p>Competitive FPS accounts</p>
                        <div class="category-stats">
                            <span class="price-range">$25 - $300</span>
                            <span class="delivery-time">Instant</span>
                        </div>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1556438064-2d7646166914?w=400&h=300&fit=crop&crop=center&auto=format&q=80" alt="PUBG">
                        <div class="category-overlay">
                            <span class="account-count">120+ Accounts</span>
                        </div>
                    </div>
                    <div class="category-info">
                        <h3>PUBG Mobile</h3>
                        <p>Mobile battle royale accounts</p>
                        <div class="category-stats">
                            <span class="price-range">$10 - $150</span>
                            <span class="delivery-time">Instant</span>
                        </div>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1552820728-8b83bb6b773f?w=400&h=300&fit=crop&crop=center&auto=format&q=80" alt="GTA V">
                        <div class="category-overlay">
                            <span class="account-count">75+ Accounts</span>
                        </div>
                    </div>
                    <div class="category-info">
                        <h3>GTA V</h3>
                        <p>Online accounts with money & cars</p>
                        <div class="category-stats">
                            <span class="price-range">$20 - $500</span>
                            <span class="delivery-time">Manual</span>
                        </div>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?w=400&h=300&fit=crop&crop=center&auto=format&q=80" alt="CS2">
                        <div class="category-overlay">
                            <span class="account-count">95+ Accounts</span>
                        </div>
                    </div>
                    <div class="category-info">
                        <h3>Counter-Strike 2</h3>
                        <p>FPS competitive accounts</p>
                        <div class="category-stats">
                            <span class="price-range">$30 - $400</span>
                            <span class="delivery-time">Instant</span>
                        </div>
                    </div>
                </div>
                
                <div class="category-card">
                    <div class="category-image">
                        <img src="https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=400&h=300&fit=crop&crop=center&auto=format&q=80" alt="League of Legends">
                        <div class="category-overlay">
                            <span class="account-count">110+ Accounts</span>
                        </div>
                    </div>
                    <div class="category-info">
                        <h3>League of Legends</h3>
                        <p>MOBA accounts with champions</p>
                        <div class="category-stats">
                            <span class="price-range">$15 - $250</span>
                            <span class="delivery-time">Instant</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Top Deals -->
    <section class="top-deals">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Hot Deals</h2>
                <p class="section-subtitle">Limited time offers on premium accounts</p>
            </div>
            
            <div class="deals-grid">
                <div class="deal-card featured">
                    <div class="deal-badge">50% OFF</div>
                    <div class="deal-image">
                        <img src="https://images.unsplash.com/photo-1556438064-2d7646166914?w=400&h=300&fit=crop&crop=center&auto=format&q=80" alt="Fortnite Pro Account">
                    </div>
                    <div class="deal-info">
                        <h3>Fortnite Pro Account</h3>
                        <p>Level 200+ • All Battle Passes • Rare Skins</p>
                        <div class="deal-price">
                            <span class="original-price">$199.99</span>
                            <span class="sale-price">$99.99</span>
                        </div>
                        <button class="btn btn-primary">Buy Now</button>
                    </div>
                </div>
                
                <div class="deal-card">
                    <div class="deal-badge">30% OFF</div>
                    <div class="deal-image">
                        <img src="https://images.unsplash.com/photo-1542751371-adc38448a05e?w=300&h=200&fit=crop&crop=center&auto=format&q=80" alt="Valorant Immortal">
                    </div>
                    <div class="deal-info">
                        <h3>Valorant Immortal</h3>
                        <p>High Rank • Premium Skins</p>
                        <div class="deal-price">
                            <span class="original-price">$149.99</span>
                            <span class="sale-price">$104.99</span>
                        </div>
                        <button class="btn btn-primary">Buy Now</button>
                    </div>
                </div>
                
                <div class="deal-card">
                    <div class="deal-badge">25% OFF</div>
                    <div class="deal-image">
                        <img src="https://images.unsplash.com/photo-1556438064-2d7646166914?w=300&h=200&fit=crop&crop=center&auto=format&q=80" alt="PUBG Conqueror">
                    </div>
                    <div class="deal-info">
                        <h3>PUBG Conqueror</h3>
                        <p>Top Rank • Premium Outfits</p>
                        <div class="deal-price">
                            <span class="original-price">$79.99</span>
                            <span class="sale-price">$59.99</span>
                        </div>
                        <button class="btn btn-primary">Buy Now</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Reviews -->
    <section class="customer-reviews">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">What Our Customers Say</h2>
                <p class="section-subtitle">Trusted by thousands of gamers worldwide</p>
            </div>
            
            <div class="reviews-grid">
                <div class="review-card">
                    <div class="review-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="review-text">"Got my Fortnite account within minutes! Everything was exactly as described. Highly recommend!"</p>
                    <div class="review-author">
                        <div class="author-avatar">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=80&h=80&fit=crop&crop=face&auto=format&q=80" alt="Alex">
                        </div>
                        <div class="author-info">
                            <h4>Alex M.</h4>
                            <span>Verified Buyer</span>
                        </div>
                    </div>
                </div>
                
                <div class="review-card">
                    <div class="review-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="review-text">"Amazing service! The Valorant account I bought was perfect and delivery was instant. Will definitely buy again!"</p>
                    <div class="review-author">
                        <div class="author-avatar">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face&auto=format&q=80" alt="Sarah">
                        </div>
                        <div class="author-info">
                            <h4>Sarah K.</h4>
                            <span>Verified Buyer</span>
                        </div>
                    </div>
                </div>
                
                <div class="review-card">
                    <div class="review-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="review-text">"Best place to buy gaming accounts! Fast, secure, and the accounts are always high quality. 5 stars!"</p>
                    <div class="review-author">
                        <div class="author-avatar">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face&auto=format&q=80" alt="Mike">
                        </div>
                        <div class="author-info">
                            <h4>Mike R.</h4>
                            <span>Verified Buyer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Signup -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h2>Stay Updated</h2>
                    <p>Get notified about new accounts, exclusive deals, and gaming updates</p>
                </div>
                <div class="newsletter-form">
                    <input type="email" placeholder="Enter your email" class="newsletter-input">
                    <button class="btn btn-primary">Subscribe</button>
                </div>
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
</body>
</html>
