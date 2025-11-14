<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Just4U Gaming - Your Trusted Gaming Account Marketplace</title>
    <meta name="description" content="Learn about Just4U Gaming - Pakistan's premier gaming account marketplace. Discover our story, safety measures, and commitment to verified, secure gaming accounts.">
    <meta name="keywords" content="about just4u gaming, gaming marketplace pakistan, verified gaming accounts, secure gaming transactions">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="style.css">
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
                        <li><a href="shop.php" class="nav-link">Shop</a></li>
                        <li><a href="about.php" class="nav-link active">About</a></li>
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

    <!-- About Hero -->
    <section class="about-hero">
        <div class="container">
            <div class="about-hero-content">
                <h1 class="page-title">About Just4U Gaming</h1>
                <p class="page-subtitle">Your trusted partner in the gaming world since 2020</p>
            </div>
        </div>
    </section>

    <!-- Our Story -->
    <section class="our-story">
        <div class="container">
            <div class="story-content">
                <div class="story-text">
                    <h2>Our Story</h2>
                    <p>Just4U Gaming was born from a simple idea: to create a safe, reliable marketplace where gamers could buy and sell premium gaming accounts without the fear of scams or fraud. Founded in 2020 by a team of passionate gamers and tech entrepreneurs, we've grown to become Pakistan's most trusted gaming account marketplace.</p>
                    
                    <p>What started as a small community of Fortnite players looking to trade accounts has evolved into a comprehensive platform serving thousands of gamers across multiple titles including Valorant, PUBG Mobile, GTA V, Counter-Strike 2, and many more.</p>
                    
                    <p>Our mission is simple: to make premium gaming accessible to everyone while maintaining the highest standards of security, verification, and customer service.</p>
                </div>
                <div class="story-image">
                    <img src="https://placehold.co/500x400/1FB6FF/ffffff?text=Gaming+Community" alt="Gaming Community">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="our-values">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Core Values</h2>
                <p class="section-subtitle">The principles that guide everything we do</p>
            </div>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Security First</h3>
                    <p>Every account is thoroughly verified and every transaction is protected with bank-level security. Your safety is our top priority.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Trust & Transparency</h3>
                    <p>We believe in complete transparency. Every account listing includes detailed information, and we're always honest about what you're getting.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Instant Delivery</h3>
                    <p>Most of our accounts are delivered instantly. No waiting, no delays - just immediate access to your new gaming experience.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Support</h3>
                    <p>Our dedicated support team is available around the clock to help with any questions or issues you might have.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3>Quality Assurance</h3>
                    <p>Every account goes through our rigorous verification process to ensure you get exactly what you pay for.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Community Driven</h3>
                    <p>We're built by gamers, for gamers. Our community feedback shapes every decision we make.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Safety & Verification -->
    <section class="safety-verification">
        <div class="container">
            <div class="safety-content">
                <div class="safety-text">
                    <h2>Safety & Verification Process</h2>
                    <p>We take account security seriously. Here's how we ensure every account is legitimate and safe:</p>
                    
                    <div class="verification-steps">
                        <div class="verification-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Account Verification</h4>
                                <p>Every account is tested to ensure it's active, accessible, and contains the advertised features.</p>
                            </div>
                        </div>
                        
                        <div class="verification-step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Security Check</h4>
                                <p>We verify that accounts have no bans, restrictions, or security issues that could affect gameplay.</p>
                            </div>
                        </div>
                        
                        <div class="verification-step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Feature Validation</h4>
                                <p>All listed features, skins, levels, and items are verified to match the account description.</p>
                            </div>
                        </div>
                        
                        <div class="verification-step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4>Seller Verification</h4>
                                <p>All sellers are verified and rated based on their transaction history and customer feedback.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="safety-image">
                    <img src="https://placehold.co/500x400/8A4DFF/ffffff?text=Security+Shield" alt="Security Shield">
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="team">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Meet Our Team</h2>
                <p class="section-subtitle">The passionate gamers behind Just4U</p>
            </div>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">
                        <img src="https://placehold.co/200x200/1FB6FF/ffffff?text=CEO" alt="Ahmad Hassan">
                    </div>
                    <div class="member-info">
                        <h3>Ahmad Hassan</h3>
                        <p class="member-role">CEO & Founder</p>
                        <p class="member-bio">Gaming enthusiast with 10+ years in the industry. Former professional esports player turned entrepreneur.</p>
                        <div class="member-social">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-image">
                        <img src="https://placehold.co/200x200/8A4DFF/ffffff?text=CTO" alt="Sarah Khan">
                    </div>
                    <div class="member-info">
                        <h3>Sarah Khan</h3>
                        <p class="member-role">CTO</p>
                        <p class="member-bio">Tech expert focused on building secure, scalable platforms. Passionate about creating seamless user experiences.</p>
                        <div class="member-social">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-image">
                        <img src="https://placehold.co/200x200/4ECDC4/ffffff?text=CS" alt="Usman Ali">
                    </div>
                    <div class="member-info">
                        <h3>Usman Ali</h3>
                        <p class="member-role">Head of Customer Support</p>
                        <p class="member-bio">Customer service veteran with a deep understanding of gaming communities and their needs.</p>
                        <div class="member-social">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-discord"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">10,000+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number">50,000+</div>
                    <div class="stat-label">Accounts Sold</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number">99.8%</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support Available</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">What Our Customers Say</h2>
                <p class="section-subtitle">Real feedback from real gamers</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Just4U Gaming has been my go-to platform for gaming accounts. The verification process is thorough, and I've never had any issues. Highly recommended!"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="https://placehold.co/50x50/1FB6FF/ffffff?text=AM" alt="Ahmad M.">
                        </div>
                        <div class="author-info">
                            <h4>Ahmad M.</h4>
                            <span>Verified Buyer</span>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"The instant delivery feature is amazing! I got my Valorant account within minutes of purchase. The customer support is also top-notch."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="https://placehold.co/50x50/8A4DFF/ffffff?text=SK" alt="Sara K.">
                        </div>
                        <div class="author-info">
                            <h4>Sara K.</h4>
                            <span>Verified Buyer</span>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"I've been using Just4U for over a year now. The quality of accounts is consistently high, and the prices are fair. Great platform!"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="https://placehold.co/50x50/4ECDC4/ffffff?text=MR" alt="Muhammad R.">
                        </div>
                        <div class="author-info">
                            <h4>Muhammad R.</h4>
                            <span>Verified Buyer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="about-cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Start Gaming?</h2>
                <p>Join thousands of satisfied customers and find your perfect gaming account today.</p>
                <div class="cta-buttons">
                    <a href="shop.php" class="btn btn-primary">
                        <span>Browse Accounts</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="support.php" class="btn btn-secondary">
                        <span>Contact Us</span>
                    </a>
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
