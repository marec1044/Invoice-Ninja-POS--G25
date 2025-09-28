<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - NTI Pay</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2a1b9a;
            --primary-light: #001195;
            --primary-dark: #2f5e96;
            --accent-color: #758acd;
            --text-on-primary: #ffffff;
            --text-primary: #333333;
            --background-light: #f5f0fa;
            --background-white: #ffffff;
            --border-color: #dee2e6;
            --success-color: #28a745;
            --error-color: #dc3545;
            --warning-color: #ffc107;
            --transition-speed: 0.3s;
            --font-family: "Poppins", sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--background-light);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 80px 20px 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="white" opacity="0.1"/><circle cx="80" cy="40" r="0.5" fill="white" opacity="0.1"/><circle cx="40" cy="80" r="1.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .welcome-text {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #fff, #ffca28);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-btn {
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            transition: all var(--transition-speed) ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .cta-primary {
            background: #ffca28;
            color: var(--primary-color);
        }

        .cta-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .features-section {
            padding: 80px 0;
            background: var(--background-white);
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: var(--text-primary);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: var(--background-white);
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all var(--transition-speed) ease;
            border: 1px solid var(--border-color);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(42, 27, 154, 0.15);
            border-color: var(--primary-color);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 2rem;
        }

        .feature-title {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
            font-weight: 600;
        }

        .feature-description {
            color: var(--text-primary);
            line-height: 1.7;
        }

        .how-it-works-section {
            padding: 80px 0;
            background: var(--background-light);
        }

        .steps-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .step-card {
            background: var(--background-white);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            position: relative;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .step-number {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .step-icon {
            font-size: 3rem;
            color: var(--accent-color);
            margin: 20px 0 15px;
        }

        .step-title {
            font-size: 1.3rem;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .step-description {
            color: var(--text-primary);
        }

        .stats-section {
            padding: 60px 0;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }

        .stat-item {
            padding: 20px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #ffca28;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .quick-actions-section {
            padding: 80px 0;
            background: var(--background-white);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .action-card {
            background: linear-gradient(135deg, var(--background-white), var(--background-light));
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            border: 2px solid var(--border-color);
            transition: all var(--transition-speed) ease;
            cursor: pointer;
        }

        .action-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(42, 27, 154, 0.1);
        }

        .action-icon {
            font-size: 3.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .action-title {
            font-size: 1.4rem;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .action-description {
            color: var(--text-primary);
            margin-bottom: 20px;
        }

        .action-btn {
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 500;
            text-decoration: none;
            transition: all var(--transition-speed) ease;
            display: inline-block;
        }

        .action-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .welcome-text {
                font-size: 2.2rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .cta-btn {
                width: 100%;
                max-width: 300px;
            }

            .section-title {
                font-size: 2rem;
            }

            .features-grid,
            .steps-container,
            .actions-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                padding: 60px 15px 40px;
            }

            .welcome-text {
                font-size: 1.8rem;
            }

            .features-section,
            .how-it-works-section,
            .quick-actions-section {
                padding: 60px 0;
            }

            .feature-card,
            .step-card,
            .action-card {
                padding: 25px 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .fade-in {
            animation: fadeIn 0.8s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <?php require '../payment/navbar.php'; ?>

    <section class="hero-section">
        <div class="hero-content">
            <h1 class="welcome-text">Welcome to NTI Pay</h1>
            <p class="hero-subtitle">Your trusted partner for seamless bill payments and financial management. Pay all your bills in one secure platform.</p>
            <div class="cta-buttons">
                <a href="../payment/pay2.php" class="cta-btn cta-primary">
                    <i class="fas fa-credit-card"></i>
                    Pay Bills Now
                </a>
                <a href="user.php" class="cta-btn cta-secondary">
                    <i class="fas fa-user"></i>
                    My Account
                </a>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Why Choose NTI Pay?</h2>
                <p class="section-subtitle">Experience the convenience of managing all your bills from one secure platform with advanced features designed for your peace of mind.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Secure Payments</h3>
                    <p class="feature-description">Bank-level security encryption ensures your financial data and transactions are protected with the highest industry standards.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="feature-title">24/7 Availability</h3>
                    <p class="feature-description">Pay your bills anytime, anywhere. Our platform is available round the clock for your convenience.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Mobile Friendly</h3>
                    <p class="feature-description">Optimized for all devices. Access your account and pay bills seamlessly from your smartphone, tablet, or desktop.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3 class="feature-title">Digital Receipts</h3>
                    <p class="feature-description">Get instant digital receipts for all your transactions. Print, share, or download for your records.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3 class="feature-title">Smart Notifications</h3>
                    <p class="feature-description">Never miss a due date with intelligent reminders and notifications for upcoming bill payments.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-description">Our dedicated customer support team is always ready to assist you with any questions or concerns.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="how-it-works-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">How It Works</h2>
                <p class="section-subtitle">Get started in just three simple steps and take control of your bill payments today.</p>
            </div>
            <div class="steps-container">
                <div class="step-card fade-in">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="step-title">Create Account</h3>
                    <p class="step-description">Sign up for your NTI Pay account with your basic information. It takes less than 2 minutes to get started.</p>
                </div>
                <div class="step-card fade-in">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <h3 class="step-title">View Bills</h3>
                    <p class="step-description">Browse all your pending bills in one place. Select which bills you want to pay and review the details.</p>
                </div>
                <div class="step-card fade-in">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3 class="step-title">Pay Securely</h3>
                    <p class="step-description">Complete your payment with our secure payment system and receive instant confirmation and receipts.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">1M+</div>
                    <div class="stat-label">Bills Paid</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">Uptime</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support</div>
                </div>
            </div>
        </div>
    </section>

    <section class="quick-actions-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Quick Actions</h2>
                <p class="section-subtitle">Access your most used features quickly and efficiently.</p>
            </div>
            <div class="actions-grid">
                <div class="action-card" onclick="location.href='../payment/pay2.php'">
                    <div class="action-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h3 class="action-title">Pay Bills</h3>
                    <p class="action-description">View and pay all your pending bills in one convenient location.</p>
                    <a href="../payment/pay2.php" class="action-btn">Pay Now</a>
                </div>
                <div class="action-card" onclick="location.href='user.php'">
                    <div class="action-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3 class="action-title">Account Balance</h3>
                    <p class="action-description">Check your current balance and manage your payment wallet.</p>
                    <a href="user.php" class="action-btn">View Balance</a>
                </div>
                <div class="action-card" onclick="location.href='user.php'">
                    <div class="action-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="action-title">Transaction History</h3>
                    <p class="action-description">Review your payment history and download receipts.</p>
                    <a href="user.php" class="action-btn">View History</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                observer.observe(el);
            });

            const actionCards = document.querySelectorAll('.action-card');
            actionCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            const ctaButtons = document.querySelectorAll('.cta-btn');
            ctaButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.5);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                });
            });

            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>