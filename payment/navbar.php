<style>
:root {
    --primary-color: #2a1b9a;
    --primary-light: #001195;
    --primary-dark: #2f5e96;
    --accent-color: #758acd;
    --text-on-primary: #ffffff;
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
    padding-top: 80px;
}

.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    background-color: var(--primary-color);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
    flex-wrap: wrap;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: bold;
    font-size: 18px;
    color: var(--text-on-primary);
}

.logo-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #FF6B35, #F7931E);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
}

.nav-links {
    display: flex;
    gap: 25px;
    align-items: center;
}

.nav-links a {
    text-decoration: none;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    transition: all var(--transition-speed) ease;
    white-space: nowrap;
}

.nav-links a:hover {
    color: var(--text-on-primary);
}

.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    color: var(--text-on-primary);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 5px;
}

.right-section {
    display: flex;
    gap: 10px;
    align-items: center;
}

.btn {
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 14px;
    cursor: pointer;
    transition: all var(--transition-speed) ease;
    border: none;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-profile {
    background-color: var(--accent-color);
    color: white;
}

.btn-logout {
    background-color: transparent;
    border: 1.5px solid var(--accent-color);
    color: white;
}

@media (max-width: 768px) {
    body {
        padding-top: 70px;
    }

    .navbar {
        padding: 10px 15px;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        order: 1;
    }

    .mobile-menu-toggle {
        display: block;
        order: 3;
    }

    .nav-links {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: var(--primary-color);
        flex-direction: column;
        gap: 0;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        order: 4;
        width: 100%;
    }

    .nav-links.active {
        display: flex;
    }

    .nav-links a {
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 16px;
        text-align: center;
    }

    .nav-links a:last-child {
        border-bottom: none;
    }

    .right-section {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: var(--primary-color);
        flex-direction: column;
        gap: 15px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        order: 5;
    }

    .right-section.active {
        display: flex;
    }

    .btn {
        width: 100%;
        justify-content: center;
        padding: 12px 20px;
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .navbar {
        padding: 8px 12px;
    }

    .logo {
        font-size: 16px;
    }

    .logo-icon {
        width: 35px;
        height: 35px;
        font-size: 12px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .navbar {
        padding: 12px 25px;
    }
}
</style>

<div class="navbar">
    <div class="logo">
        <div class="logo-icon">NTI</div>
        <span>NTIpay</span>
    </div>

    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="nav-links" id="navLinks">
        <a href="../Login/welcome.php">Home</a>
        <a href="../Login/user.php">My Account</a>
    </div>

    <div class="right-section" id="rightSection">
        <a class="btn btn-profile" href="../Login/user.php">
            <i class="fas fa-user"></i> My Account
        </a>
        <a class="btn btn-logout" href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupMobileMenu();
});

function setupMobileMenu() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navLinks = document.getElementById('navLinks');
    const rightSection = document.getElementById('rightSection');

    if (!mobileMenuToggle || !navLinks || !rightSection) return;

    mobileMenuToggle.addEventListener('click', function() {
        navLinks.classList.toggle('active');
        rightSection.classList.toggle('active');
        
        const icon = mobileMenuToggle.querySelector('i');
        if (icon) {
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    });

    const navLinksElements = navLinks.querySelectorAll('a');
    navLinksElements.forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('active');
            rightSection.classList.remove('active');
            const icon = mobileMenuToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    });

    document.addEventListener('click', function(event) {
        const navbar = document.querySelector('.navbar');
        if (navbar && !navbar.contains(event.target)) {
            navLinks.classList.remove('active');
            rightSection.classList.remove('active');
            const icon = mobileMenuToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            navLinks.classList.remove('active');
            rightSection.classList.remove('active');
            const icon = mobileMenuToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    });
}
</script>