<?php
$title = 'GazExpress - Livraison de Gaz de Cuisine';
$includeMap = false;

ob_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #FF6B35;
            --secondary-color: #004E89;
            --accent-color: #FFB627;
            --success-color: #28A745;
            --warning-color: #FFC107;
            --error-color: #DC3545;
            --dark-color: #2C3E50;
            --light-color: #F8F9FA;
            --border-color: #E9ECEF;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .nav-brand i {
            margin-right: 0.5rem;
            font-size: 1.8rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background: var(--primary-color);
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .btn-login, .btn-register {
            padding: 0.5rem 1.5rem;
            border-radius: var(--radius);
            transition: var(--transition);
        }

        .btn-login {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-login:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-register {
            background: var(--primary-color);
            color: white;
            border: 2px solid var(--primary-color);
        }

        .btn-register:hover {
            background: transparent;
            color: var(--primary-color);
        }

        /* Toggle button */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 1.8rem;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 100vh;
            padding: 120px 2rem 2rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .hero-content {
            flex: 1;
            max-width: 600px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: bold;
            transition: var(--transition);
            border: 2px solid transparent;
            cursor: pointer;
        }

        .btn-primary {
            background: white;
            color: var(--primary-color);
            border-color: white;
        }

        .btn-primary:hover {
            background: transparent;
            color: white;
            border-color: white;
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border-color: white;
        }

        .btn-secondary:hover {
            background: white;
            color: var(--primary-color);
        }

        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-image i {
            font-size: 15rem;
            opacity: 0.3;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Services Section */
        .services {
            padding: 5rem 0;
            background: white;
        }

        .services h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: var(--dark-color);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .service-card i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .service-card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .service-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 2rem 0;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .footer-brand i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1rem;
            border-top: 1px solid #444;
            opacity: 0.7;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .menu-toggle {
                display: block;
            }

            .nav-links {
                display: none;
                flex-direction: column;
                width: 100%;
                background: rgba(255, 255, 255, 0.95);
                position: absolute;
                top: 60px;
                left: 0;
                padding: 1rem;
                box-shadow: var(--shadow);
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links a {
                padding: 0.5rem 0;
                width: 100%;
                text-align: center;
            }

            .hero {
                flex-direction: column;
                text-align: center;
                padding: 120px 1rem 2rem;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-image i {
                font-size: 8rem;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
                padding: 0.8rem 1.5rem;
            }

            .service-card {
                padding: 1.5rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-brand">
                <i class="fas fa-fire"></i>
                <span>GazExpress</span>
            </div>
            <button class="menu-toggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-links">
                <a href="#home">Accueil</a>
                <a href="#services">Services</a>
                <a href="#contact">Contact</a>
                <a href="/login" class="btn-login">Connexion</a>
                <a href="/register" class="btn-register">Inscription</a>
            </div>
        </nav>
    </header>

    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Livraison de Gaz de Cuisine à Domicile</h1>
            <p>Commandez votre gaz de cuisine en ligne et faites-vous livrer rapidement par les stations les plus proches.</p>
            <div class="hero-buttons">
                <a href="/register?type=customer" class="btn btn-primary">
                    <i class="fas fa-user"></i> Client
                </a>
                <a href="/register?type=station" class="btn btn-secondary">
                    <i class="fas fa-gas-pump"></i> Station de Service
                </a>
            </div>
        </div>
        <div class="hero-image">
            <i class="fas fa-truck"></i>
        </div>
    </section>

    <section id="services" class="services">
        <div class="container">
            <h2>Nos Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Géolocalisation</h3>
                    <p>Trouvez automatiquement les stations les plus proches de votre position</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-clock"></i>
                    <h3>Livraison Rapide</h3>
                    <p>Livraison en moins de 30 minutes selon votre localisation</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Paiement Mobile</h3>
                    <p>Payez facilement avec Orange Money ou MTN Money</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Sécurisé</h3>
                    <p>Toutes les stations sont vérifiées et approuvées par notre équipe</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <i class="fas fa-fire"></i>
                    <span>GazExpress</span>
                </div>
                <div class="footer-links">
                    <a href="#about">À propos</a>
                    <a href="#privacy">Confidentialité</a>
                    <a href="#terms">Conditions</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 GazExpress. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.querySelector('.menu-toggle');
            const navLinks = document.querySelector('.nav-links');

            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });

            // Hide menu and toggle button when a nav link is clicked
            navLinks.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.classList.remove('active');
                    menuToggle.style.display = 'none';
                });
            });
        });
    </script>
</body>
</html>

<?php
$content = ob_get_clean();
include '../app/Views/layouts/app.php';
?>