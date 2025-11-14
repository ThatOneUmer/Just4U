<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Just4U Gaming</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Modern Cart Page Styles */
        .cart-page-modern {
            min-height: 100vh;
            background: linear-gradient(135deg, #0F1620 0%, #1A1F2E 100%);
            padding: 80px 0 40px;
            position: relative;
            overflow: hidden;
        }
        
        .cart-page-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(0, 212, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 255, 136, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255, 107, 107, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .cart-container-modern {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
            position: relative;
            z-index: 1;
        }
        
        .cart-header-modern {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .cart-title-modern {
            font-size: 3.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #00FF88, #00D4FF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            font-family: 'Orbitron', monospace;
            text-shadow: 0 0 30px rgba(0, 255, 136, 0.3);
        }
        
        .cart-subtitle {
            font-size: 1.2rem;
            color: #8892B0;
            margin-bottom: 30px;
        }
        
        .breadcrumb-modern {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
        }
        
        .breadcrumb-item {
            color: #8892B0;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .breadcrumb-item:hover {
            color: #00FF88;
        }
        
        .breadcrumb-item:not(:last-child)::after {
            content: '›';
            margin-left: 15px;
            color: #8892B0;
        }
        
        .breadcrumb-item.active {
            color: #00D4FF;
            font-weight: 600;
        }
        
        .cart-content-modern {
            display: grid;
            grid-template-columns: 1fr 450px;
            gap: 40px;
            align-items: start;
        }
        
        @media (max-width: 1200px) {
            .cart-content-modern {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
        
        .cart-items-section-modern {
            background: rgba(26, 31, 46, 0.8);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(0, 212, 255, 0.2);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .section-title-modern {
            font-size: 1.8rem;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .section-title-modern i {
            color: #00FF88;
            font-size: 1.5rem;
        }
        
        .cart-item-card-modern {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            gap: 25px;
            padding: 25px;
            background: rgba(15, 22, 32, 0.6);
            border-radius: 16px;
            margin-bottom: 20px;
            border: 1px solid rgba(136, 146, 176, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        .cart-item-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 136, 0.1), transparent);
            transition: left 0.6s;
        }
        
        .cart-item-card-modern:hover::before {
            left: 100%;
        }
        
        .cart-item-card-modern:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 255, 136, 0.4);
            box-shadow: 0 15px 40px rgba(0, 255, 136, 0.1);
        }
        
        .cart-item-image-modern {
            width: 120px;
            height: 90px;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            border: 2px solid rgba(0, 212, 255, 0.3);
        }
        
        .cart-item-image-modern img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .cart-item-card-modern:hover .cart-item-image-modern img {
            transform: scale(1.1);
        }
        
        .cart-item-info-modern {
            flex: 1;
        }
        
        .cart-item-name-modern {
            font-size: 1.4rem;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        
        .cart-item-meta-modern {
            color: #8892B0;
            font-size: 1rem;
            margin-bottom: 15px;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .cart-item-meta-modern span {
            background: rgba(0, 212, 255, 0.1);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.9rem;
            border: 1px solid rgba(0, 212, 255, 0.2);
        }
        
        .cart-item-price-modern {
            font-size: 1.6rem;
            font-weight: 800;
            color: #00FF88;
            text-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
        }
        
        .cart-item-actions-modern {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: flex-end;
        }
        
        .quantity-controls-modern {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(15, 22, 32, 0.8);
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid rgba(136, 146, 176, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .quantity-btn-modern {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1A1F2E, #0F1620);
            border: 1px solid rgba(0, 212, 255, 0.4);
            border-radius: 8px;
            color: #00D4FF;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.3rem;
            font-weight: 700;
        }
        
        .quantity-btn-modern:hover {
            background: linear-gradient(135deg, #00D4FF, #00FF88);
            color: #0F1620;
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
        }
        
        .quantity-value-modern {
            min-width: 50px;
            text-align: center;
            font-weight: 700;
            color: #FFFFFF;
            font-size: 1.2rem;
        }
        
        .remove-btn-modern {
            background: linear-gradient(135deg, #FF6B6B, #FF4757);
            border: none;
            color: #FFFFFF;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .remove-btn-modern:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.5);
        }
        
        .cart-summary-modern {
            background: rgba(26, 31, 46, 0.8);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(0, 255, 136, 0.3);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 120px;
        }
        
        .summary-header-modern {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .summary-icon-modern {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #00FF88, #00D4FF);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.3);
        }
        
        .summary-icon-modern i {
            font-size: 2rem;
            color: #0F1620;
        }
        
        .summary-title-modern {
            font-size: 1.8rem;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 10px;
        }
        
        .summary-row-modern {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid rgba(136, 146, 176, 0.2);
            transition: all 0.3s ease;
        }
        
        .summary-row-modern:hover {
            background: rgba(0, 212, 255, 0.05);
            margin: 0 -20px;
            padding: 18px 20px;
            border-radius: 8px;
        }
        
        .summary-row-modern:last-child {
            border-bottom: none;
            margin-top: 20px;
            padding-top: 25px;
            border-top: 2px solid rgba(0, 255, 136, 0.3);
            font-size: 1.4rem;
            font-weight: 800;
        }
        
        .summary-label-modern {
            color: #8892B0;
            font-weight: 500;
        }
        
        .summary-value-modern {
            color: #FFFFFF;
            font-weight: 600;
        }
        
        .summary-total-modern {
            color: #00FF88;
            text-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
        }
        
        .checkout-btn-modern {
            width: 100%;
            padding: 20px;
            background: linear-gradient(135deg, #00FF88, #00D4FF);
            color: #0F1620;
            border: none;
            border-radius: 16px;
            font-size: 1.3rem;
            font-weight: 800;
            cursor: pointer;
            margin-top: 30px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .checkout-btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }
        
        .checkout-btn-modern:hover::before {
            left: 100%;
        }
        
        .checkout-btn-modern:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 20px 50px rgba(0, 255, 136, 0.4);
        }
        
        .checkout-btn-modern:active {
            transform: translateY(-1px) scale(1.01);
        }
        
        .cart-empty-modern {
            text-align: center;
            padding: 100px 40px;
            background: rgba(15, 22, 32, 0.6);
            border-radius: 16px;
            border: 1px solid rgba(136, 146, 176, 0.2);
        }
        
        .empty-icon-modern {
            font-size: 5rem;
            color: #8892B0;
            margin-bottom: 30px;
            opacity: 0.6;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .empty-title-modern {
            font-size: 2rem;
            color: #FFFFFF;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .empty-text-modern {
            color: #8892B0;
            font-size: 1.2rem;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .empty-btn-modern {
            background: linear-gradient(135deg, #00D4FF, #00FF88);
            color: #0F1620;
            padding: 16px 40px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }
        
        .empty-btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.4);
        }
        
        .cart-actions-modern {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            justify-content: space-between;
        }
        
        .action-btn-modern {
            padding: 12px 24px;
            border: 2px solid rgba(0, 212, 255, 0.4);
            background: transparent;
            color: #00D4FF;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .action-btn-modern:hover {
            background: rgba(0, 212, 255, 0.1);
            border-color: #00D4FF;
            color: #FFFFFF;
        }
        
        .action-btn-primary-modern {
            background: linear-gradient(135deg, #00D4FF, #00FF88);
            color: #0F1620;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 212, 255, 0.3);
        }
        
        .action-btn-primary-modern:hover {
            background: linear-gradient(135deg, #00FF88, #00D4FF);
            box-shadow: 0 8px 30px rgba(0, 212, 255, 0.4);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .cart-title-modern {
                font-size: 2.5rem;
            }
            
            .cart-content-modern {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .cart-item-card-modern {
                grid-template-columns: 80px 1fr;
                gap: 15px;
                padding: 20px;
            }
            
            .cart-item-image-modern {
                width: 80px;
                height: 60px;
            }
            
            .cart-item-actions-modern {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                grid-column: 1 / -1;
                margin-top: 15px;
            }
            
            .cart-summary-modern {
                padding: 30px 25px;
            }
            
            .cart-container-modern {
                padding: 0 20px;
            }
        }
        
        /* Loading Animation */
        .cart-loading {
            display: none;
            text-align: center;
            padding: 60px;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(0, 212, 255, 0.2);
            border-top: 4px solid #00D4FF;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            color: #8892B0;
            font-size: 1.1rem;
        }
        
        /* Success Animation */
        .cart-success {
            display: none;
            text-align: center;
            padding: 60px;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #00FF88, #00D4FF);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: successPulse 0.6s ease-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .success-icon i {
            font-size: 2.5rem;
            color: #0F1620;
        }
        
        .success-title {
            color: #FFFFFF;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .success-text {
            color: #8892B0;
            font-size: 1.1rem;
        }
        
        /* Newsletter Styles */
        .newsletter {
            background: linear-gradient(135deg, #1A1F2E, #0F1620);
            padding: 80px 0;
            border-top: 1px solid rgba(0, 212, 255, 0.2);
            border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        }
        
        .newsletter-content {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 40px;
            align-items: center;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .newsletter-text h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #00FF88, #00D4FF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .newsletter-text p {
            color: #8892B0;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .newsletter-form {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .newsletter-input {
            padding: 18px 25px;
            background: rgba(15, 22, 32, 0.8);
            border: 2px solid rgba(0, 212, 255, 0.3);
            border-radius: 12px;
            color: #FFFFFF;
            font-size: 1rem;
            min-width: 300px;
            transition: all 0.3s ease;
        }
        
        .newsletter-input:focus {
            outline: none;
            border-color: #00D4FF;
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        }
        
        .newsletter-input::placeholder {
            color: #8892B0;
        }
        
        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, #0F1620, #1A1F2E);
            color: #FFFFFF;
            padding: 60px 0 20px;
            border-top: 1px solid rgba(0, 212, 255, 0.2);
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-section h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #00FF88;
        }
        
        .footer-logo {
            margin-bottom: 20px;
        }
        
        .footer-description {
            color: #8892B0;
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 1rem;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: #8892B0;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .footer-links a:hover {
            color: #00D4FF;
            transform: translateX(5px);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-link {
            width: 45px;
            height: 45px;
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #00D4FF;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }
        
        .social-link:hover {
            background: linear-gradient(135deg, #00D4FF, #00FF88);
            color: #0F1620;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 212, 255, 0.4);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(136, 146, 176, 0.2);
            padding-top: 20px;
        }
        
        .footer-bottom-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #8892B0;
            font-size: 0.9rem;
        }
        
        .footer-bottom-links {
            display: flex;
            gap: 20px;
        }
        
        .footer-bottom-links a {
            color: #8892B0;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-bottom-links a:hover {
            color: #00D4FF;
        }
        
        /* Responsive Footer */
        @media (max-width: 1200px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }
        }
        
        @media (max-width: 768px) {
            .newsletter-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 30px;
            }
            
            .newsletter-form {
                justify-content: center;
            }
            
            .newsletter-input {
                min-width: 250px;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }
            
            .social-links {
                justify-content: center;
            }
            
            .footer-bottom-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
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
                        <li><a href="support.php" class="nav-link">Support</a></li>
                    </ul>
                </nav>
                
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
                                <?php if (!empty($_SESSION['user_id'])): ?>
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
                                <a href="logout.php" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </a>
                                <?php else: ?>
                                <a href="login.php" class="dropdown-item">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Login
                                </a>
                                <a href="signup.php" class="dropdown-item">
                                    <i class="fas fa-user-plus"></i>
                                    Sign Up
                                </a>
                                <?php endif; ?>
                            </div>
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
    </header>

    <!-- Modern Cart Page Content -->
    <div class="cart-page-modern">
        <div class="cart-container-modern">
            <div class="cart-header-modern">
                <h1 class="cart-title-modern">Shopping Cart</h1>
                <p class="cart-subtitle">Review your selected gaming items and proceed to checkout</p>
                
                <nav class="breadcrumb-modern">
                    <a href="index.php" class="breadcrumb-item">Home</a>
                    <a href="shop.php" class="breadcrumb-item">Shop</a>
                    <span class="breadcrumb-item active">Cart</span>
                </nav>
            </div>
            
            <div class="cart-content-modern">
                <!-- Cart Items Section -->
                <div class="cart-items-section-modern">
                    <h2 class="section-title-modern">
                        <i class="fas fa-gamepad"></i>
                        Your Gaming Items
                        <span id="itemCountBadge" class="badge-modern" style="display: none;">0</span>
                    </h2>
                    
                    <div id="cartItemsContainerModern">
                        <!-- Cart items will be loaded here -->
                    </div>
                    
                    <div class="cart-loading" id="cartLoading">
                        <div class="loading-spinner"></div>
                        <p class="loading-text">Loading your cart...</p>
                    </div>
                    
                    <div class="cart-success" id="cartSuccess">
                        <div class="success-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <h3 class="success-title">Success!</h3>
                        <p class="success-text">Your cart has been updated.</p>
                    </div>
                    
                    <div class="cart-actions-modern" id="cartActions" style="display: none;">
                        <a href="shop.php" class="action-btn-modern">
                            <i class="fas fa-arrow-left"></i>
                            Continue Shopping
                        </a>
                        <button class="action-btn-modern" id="clearCartBtn">
                            <i class="fas fa-trash"></i>
                            Clear Cart
                        </button>
                    </div>
                </div>
                
                <!-- Order Summary Section -->
                <div class="cart-summary-modern">
                    <div class="summary-header-modern">
                        <div class="summary-icon-modern">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <h2 class="summary-title-modern">Order Summary</h2>
                    </div>
                    
                    <div class="summary-row-modern">
                        <span class="summary-label-modern">Subtotal (<span id="itemCountText">0</span> items)</span>
                        <span class="summary-value-modern" id="subtotalAmount">$0.00</span>
                    </div>
                    
                    <div class="summary-row-modern">
                        <span class="summary-label-modern">Processing Fee</span>
                        <span class="summary-value-modern">$0.00</span>
                    </div>
                    
                    <div class="summary-row-modern">
                        <span class="summary-label-modern">Estimated Tax</span>
                        <span class="summary-value-modern" id="taxAmount">$0.00</span>
                    </div>
                    
                    <div class="summary-row-modern">
                        <span class="summary-label-modern">Total</span>
                        <span class="summary-value-modern summary-total-modern" id="totalAmount">$0.00</span>
                    </div>
                    
                    <button class="checkout-btn-modern" id="checkoutBtnModern">
                        <i class="fas fa-lock"></i>
                        Proceed to Secure Checkout
                    </button>
                    
                    <div style="margin-top: 20px; text-align: center;">
                        <p style="color: #8892B0; font-size: 0.9rem; margin-bottom: 10px;">
                            <i class="fas fa-shield-alt" style="color: #00FF88;"></i>
                            Secure checkout powered by SSL encryption
                        </p>
                        <p style="color: #8892B0; font-size: 0.8rem;">
                            <i class="fas fa-clock" style="color: #00D4FF;"></i>
                            Instant delivery guaranteed
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <script src="script.js"></script>
    <script>
        // Modern Cart Page Functionality
        (function() {
            const CART_KEY = 'j4u_cart';
            let cart = [];
            
            // DOM Elements
            const cartItemsContainer = document.getElementById('cartItemsContainerModern');
            const cartLoading = document.getElementById('cartLoading');
            const cartSuccess = document.getElementById('cartSuccess');
            const cartActions = document.getElementById('cartActions');
            const itemCountText = document.getElementById('itemCountText');
            const itemCountBadge = document.getElementById('itemCountBadge');
            const subtotalAmount = document.getElementById('subtotalAmount');
            const taxAmount = document.getElementById('taxAmount');
            const totalAmount = document.getElementById('totalAmount');
            const checkoutBtn = document.getElementById('checkoutBtnModern');
            const clearCartBtn = document.getElementById('clearCartBtn');
            
            // Initialize cart
            function initCart() {
                showLoading();
                setTimeout(() => {
                    loadCart();
                    renderCart();
                    hideLoading();
                }, 800);
            }
            
            // Show loading state
            function showLoading() {
                cartItemsContainer.style.display = 'none';
                cartLoading.style.display = 'block';
                cartSuccess.style.display = 'none';
                cartActions.style.display = 'none';
            }
            
            // Hide loading state
            function hideLoading() {
                cartLoading.style.display = 'none';
                cartItemsContainer.style.display = 'block';
            }
            
            // Show success state
            function showSuccess(message = 'Cart updated successfully!') {
                cartItemsContainer.style.display = 'none';
                cartSuccess.style.display = 'block';
                cartSuccess.querySelector('.success-text').textContent = message;
                
                setTimeout(() => {
                    cartSuccess.style.display = 'none';
                    cartItemsContainer.style.display = 'block';
                }, 2000);
            }
            
            // Load cart from localStorage
            function loadCart() {
                try {
                    const savedCart = localStorage.getItem(CART_KEY);
                    cart = savedCart ? JSON.parse(savedCart) : [];
                } catch (e) {
                    console.error('Error loading cart:', e);
                    cart = [];
                }
            }
            
            // Save cart to localStorage
            function saveCart() {
                try {
                    localStorage.setItem(CART_KEY, JSON.stringify(cart));
                    updateCartCount();
                    window.dispatchEvent(new CustomEvent('cartUpdated', { detail: cart }));
                } catch (e) {
                    console.error('Error saving cart:', e);
                }
            }
            
            // Update cart count in header
            function updateCartCount() {
                const count = cart.reduce((total, item) => total + item.quantity, 0);
                const cartCountEl = document.getElementById('cartCount');
                if (cartCountEl) {
                    cartCountEl.textContent = count;
                }
                
                // Update badge
                if (count > 0) {
                    itemCountBadge.textContent = count;
                    itemCountBadge.style.display = 'inline-block';
                } else {
                    itemCountBadge.style.display = 'none';
                }
            }
            
            // Format price with currency
            function formatPrice(price) {
                return '$' + Number(price).toFixed(2);
            }
            
            // Calculate tax (8% default)
            function calculateTax(subtotal) {
                return subtotal * 0.08;
            }
            
            // Calculate all totals
            function calculateTotals() {
                const itemCount = cart.reduce((total, item) => total + item.quantity, 0);
                const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
                const tax = calculateTax(subtotal);
                const total = subtotal + tax;
                
                // Update UI
                itemCountText.textContent = itemCount;
                subtotalAmount.textContent = formatPrice(subtotal);
                taxAmount.textContent = formatPrice(tax);
                totalAmount.textContent = formatPrice(total);
                
                return { itemCount, subtotal, tax, total };
            }
            
            // Render cart items
            function renderCart() {
                const { itemCount } = calculateTotals();
                
                if (cart.length === 0) {
                    renderEmptyCart();
                    cartActions.style.display = 'none';
                    return;
                }
                
                cartActions.style.display = 'flex';
                
                cartItemsContainer.innerHTML = cart.map(item => `
                    <div class="cart-item-card-modern" data-item-id="${item.id}">
                        <div class="cart-item-image-modern">
                            <img src="${item.image || 'https://placehold.co/120x90/0F1620/FFFFFF?text=IMG'}" 
                                 alt="${item.name}" 
                                 onerror="this.src='https://placehold.co/120x90/0F1620/FFFFFF?text=IMG'">
                        </div>
                        <div class="cart-item-info-modern">
                            <h3 class="cart-item-name-modern">${item.name}</h3>
                            <div class="cart-item-meta-modern">
                                <span><i class="fas fa-gamepad"></i> ${item.game || 'Game'}</span>
                                <span><i class="fas fa-layer-group"></i> ${item.level || 'Level N/A'}</span>
                            </div>
                            <div class="cart-item-price-modern">${formatPrice(item.price)}</div>
                        </div>
                        <div class="cart-item-actions-modern">
                            <div class="quantity-controls-modern">
                                <button class="quantity-btn-modern" onclick="updateCartItem(${item.id}, -1)" 
                                        ${item.quantity <= 1 ? 'disabled' : ''}>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity-value-modern">${item.quantity}</span>
                                <button class="quantity-btn-modern" onclick="updateCartItem(${item.id}, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button class="remove-btn-modern" onclick="removeCartItem(${item.id})" title="Remove item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
            }
            
            // Render empty cart
            function renderEmptyCart() {
                cartItemsContainer.innerHTML = `
                    <div class="cart-empty-modern">
                        <div class="empty-icon-modern">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3 class="empty-title-modern">Your cart is empty</h3>
                        <p class="empty-text-modern">Looks like you haven't added any gaming items to your cart yet.</p>
                        <a href="shop.php" class="empty-btn-modern">
                            <i class="fas fa-gamepad"></i>
                            Start Shopping
                        </a>
                    </div>
                `;
            }
            
            // Update cart item quantity
            window.updateCartItem = function(productId, change) {
                const item = cart.find(item => item.id === productId);
                if (item) {
                    const newQuantity = item.quantity + change;
                    if (newQuantity > 0) {
                        item.quantity = newQuantity;
                        saveCart();
                        renderCart();
                        showSuccess('Item quantity updated!');
                    }
                }
            };
            
            // Remove cart item
            window.removeCartItem = function(productId) {
                if (confirm('Are you sure you want to remove this item from your cart?')) {
                    cart = cart.filter(item => item.id !== productId);
                    saveCart();
                    renderCart();
                    showSuccess('Item removed from cart!');
                }
            };
            
            // Clear entire cart
            function clearCart() {
                if (confirm('Are you sure you want to clear your entire cart?')) {
                    cart = [];
                    saveCart();
                    renderCart();
                    showSuccess('Cart cleared successfully!');
                }
            }
            
            // Handle checkout
            function handleCheckout() {
                if (cart.length === 0) {
                    alert('Your cart is empty! Please add some items before checkout.');
                    return;
                }
                
                <?php if (empty($_SESSION['user_id'])): ?>
                    // User not logged in
                    if (confirm('Please login to proceed with checkout. Would you like to login now?')) {
                        window.location.href = 'login.php?redirect=cart-new.php';
                    }
                <?php else: ?>
                    // User logged in - proceed to checkout
                    showLoading();
                    setTimeout(() => {
                        hideLoading();
                        alert('Redirecting to secure checkout...');
                        // window.location.href = 'checkout.php';
                    }, 1500);
                <?php endif; ?>
            }
            
            // Prevent cart icon redirect on cart page
            function setupCartButton() {
                const cartBtn = document.getElementById('cartBtn');
                if (cartBtn) {
                    cartBtn.onclick = function(e) {
                        e.preventDefault();
                        // Scroll to top or refresh cart
                        window.scrollTo({top: 0, behavior: 'smooth'});
                        initCart();
                    };
                }
            }
            
            // Event listeners
            clearCartBtn.addEventListener('click', clearCart);
            checkoutBtn.addEventListener('click', handleCheckout);
            
            // Listen for cart updates from other pages
            window.addEventListener('cartUpdated', function(e) {
                loadCart();
                renderCart();
            });
            
            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                initCart();
                setupCartButton();
            });
            
            // Expose functions globally
            window.renderCart = renderCart;
            window.loadCart = loadCart;
            
        })();
    </script>
</body>
</html>