<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center | Just4U Gaming - Help & FAQ</title>
    <meta name="description" content="Get help with your Just4U Gaming account. Find answers to common questions, contact support, and resolve any issues with your gaming account purchases.">
    <meta name="keywords" content="just4u gaming support, gaming account help, customer service, faq, contact support">
    
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
                        <li><a href="about.php" class="nav-link">About</a></li>
                        <li><a href="support.php" class="nav-link active">Support</a></li>
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

    <!-- Support Hero -->
    <section class="support-hero">
        <div class="container">
            <div class="support-hero-content">
                <h1 class="page-title">Support Center</h1>
                <p class="page-subtitle">We're here to help you with any questions or issues</p>
            </div>
        </div>
    </section>

    <!-- Support Content -->
    <section class="support-content">
        <div class="container">
            <div class="support-layout">
                <!-- Support Navigation -->
                <aside class="support-nav">
                    <h3>Help Topics</h3>
                    <ul class="support-links">
                        <li><a href="#faq" class="support-link active">FAQ</a></li>
                        <li><a href="#contact" class="support-link">Contact Us</a></li>
                        <li><a href="#disputes" class="support-link">Dispute Resolution</a></li>
                        <li><a href="#delivery" class="support-link">Delivery Issues</a></li>
                        <li><a href="#account" class="support-link">Account Problems</a></li>
                        <li><a href="#payment" class="support-link">Payment Issues</a></li>
                    </ul>
                </aside>

                <!-- Main Content -->
                <main class="support-main">
                    <!-- FAQ Section -->
                    <div class="support-section active" id="faq">
                        <h2>Frequently Asked Questions</h2>
                        
                        <div class="faq-categories">
                            <div class="faq-category">
                                <h3>General Questions</h3>
                                <div class="faq-items">
                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>What is Just4U Gaming?</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="faq-answer">
                                            <p>Just4U Gaming is Pakistan's premier marketplace for verified gaming accounts. We provide a safe, secure platform where gamers can buy and sell premium gaming accounts for popular titles like Fortnite, Valorant, PUBG Mobile, and more.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>How do I know the accounts are legitimate?</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="faq-answer">
                                            <p>Every account goes through our rigorous verification process. We test each account to ensure it's active, contains the advertised features, and has no bans or restrictions. All sellers are verified and rated based on their transaction history.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>What payment methods do you accept?</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="faq-answer">
                                            <p>We accept all major credit cards, PayPal, bank transfers, and local payment methods including JazzCash and EasyPaisa. All payments are processed securely through encrypted payment gateways.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="faq-category">
                                <h3>Account Delivery</h3>
                                <div class="faq-items">
                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>How quickly will I receive my account?</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="faq-answer">
                                            <p>Most accounts are delivered instantly within 5 minutes of successful payment. You'll receive account details via email and can access them through your account dashboard. Some manual delivery accounts may take up to 24 hours.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>What if I don't receive my account?</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="faq-answer">
                                            <p>If you don't receive your account within the specified time, please contact our support team immediately. We'll investigate and provide a full refund if we cannot resolve the issue within 24 hours.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>Can I change the account password after purchase?</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="faq-answer">
                                            <p>Yes, absolutely! We encourage you to change the password and enable two-factor authentication immediately after receiving your account to ensure maximum security.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="faq-category">
                                <h3>Refunds & Returns</h3>
                                <div class="faq-items">
                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>What is your refund policy?</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="faq-answer">
                                            <p>We offer a 30-day money-back guarantee. If you're not satisfied with your purchase or if the account doesn't match the description, you can request a full refund within 30 days of purchase.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>How do I request a refund?</span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <div class="faq-answer">
                                            <p>To request a refund, contact our support team with your order number and reason for the refund. We'll review your request and process the refund within 3-5 business days if approved.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Section -->
                    <div class="support-section" id="contact">
                        <h2>Contact Us</h2>
                        <p>Need help? We're here for you 24/7. Choose the best way to reach us:</p>
                        
                        <div class="contact-methods">
                            <div class="contact-method">
                                <div class="contact-icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <h3>Live Chat</h3>
                                <p>Get instant help from our support team</p>
                                <button class="btn btn-primary">Start Chat</button>
                            </div>
                            
                            <div class="contact-method">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <h3>Email Support</h3>
                                <p>Send us a detailed message</p>
                                <a href="mailto:support@just4u.com" class="btn btn-secondary">Send Email</a>
                            </div>
                            
                            <div class="contact-method">
                                <div class="contact-icon">
                                    <i class="fab fa-discord"></i>
                                </div>
                                <h3>Discord</h3>
                                <p>Join our community server</p>
                                <a href="#" class="btn btn-secondary">Join Discord</a>
                            </div>
                        </div>
                        
                        <div class="contact-form">
                            <h3>Send us a Message</h3>
                            <form class="support-form">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" id="name" name="name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <select id="subject" name="subject" required>
                                        <option value="">Select a topic</option>
                                        <option value="delivery">Delivery Issue</option>
                                        <option value="account">Account Problem</option>
                                        <option value="payment">Payment Issue</option>
                                        <option value="refund">Refund Request</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="message">Message</label>
                                    <textarea id="message" name="message" rows="5" required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </form>
                        </div>
                    </div>

                    <!-- Dispute Resolution -->
                    <div class="support-section" id="disputes">
                        <h2>Dispute Resolution</h2>
                        <p>If you have a dispute with a seller or are unsatisfied with a purchase, we're here to help resolve it fairly.</p>
                        
                        <div class="dispute-process">
                            <h3>Our Dispute Resolution Process</h3>
                            <ol>
                                <li><strong>Contact Support:</strong> Reach out to our support team with your order details and the nature of the dispute.</li>
                                <li><strong>Investigation:</strong> We'll investigate the matter by reviewing the account, seller history, and your complaint.</li>
                                <li><strong>Resolution:</strong> We'll work with both parties to find a fair solution, which may include a refund, account replacement, or other compensation.</li>
                                <li><strong>Follow-up:</strong> We'll ensure the resolution is implemented and follow up to confirm your satisfaction.</li>
                            </ol>
                        </div>
                        
                        <div class="dispute-guidelines">
                            <h3>Dispute Guidelines</h3>
                            <ul>
                                <li>Disputes must be reported within 30 days of purchase</li>
                                <li>Provide clear evidence and detailed descriptions</li>
                                <li>Be respectful and professional in all communications</li>
                                <li>Allow reasonable time for investigation and resolution</li>
                            </ul>
                        </div>
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
    <script src="support.js"></script>
</body>
</html>
