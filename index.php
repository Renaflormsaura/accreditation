<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUP Accreditation System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .header {
            background: #8B0000;
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .logo i {
            margin-right: 10px;
            font-size: 2rem;
        }
        
        .nav {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        
        .nav a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        
        .nav a:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .admin-link {
            background: #FFD700;
            color: #8B0000 !important;
            font-weight: bold;
        }
        
        .admin-link:hover {
            background: #FFC700 !important;
        }
        
        .hero {
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #FFD700;
            color: #8B0000;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #FFC700;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .features {
            padding: 4rem 0;
            background: #f8f9fa;
        }
        
        .features h2 {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2.5rem;
            color: #8B0000;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-card i {
            font-size: 3rem;
            color: #8B0000;
            margin-bottom: 1rem;
        }
        
        .feature-card h3 {
            margin-bottom: 1rem;
            color: #333;
        }
        
        .about {
            padding: 4rem 0;
        }
        
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }
        
        .about-text h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #8B0000;
        }
        
        .about-text p {
            margin-bottom: 1rem;
            font-size: 1.1rem;
            line-height: 1.8;
        }
        
        .about-image {
            text-align: center;
        }
        
        .about-image i {
            font-size: 15rem;
            color: #8B0000;
            opacity: 0.1;
        }
        
        .footer {
            background: #333;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section h3 {
            margin-bottom: 1rem;
            color: #FFD700;
        }
        
        .footer-section p,
        .footer-section a {
            color: #ccc;
            text-decoration: none;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .footer-section a:hover {
            color: #FFD700;
        }
        
        .footer-bottom {
            border-top: 1px solid #555;
            padding-top: 1rem;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .about-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .about-image i {
                font-size: 8rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    PUP Accreditation
                </div>
                <nav>
                    <ul class="nav">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="admin/login.php" class="admin-link">
                            <i class="fas fa-user-shield"></i> Admin Login
                        </a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero" id="home">
        <div class="container">
            <h1>PUP Accreditation System</h1>
            <p>Polytechnic University of the Philippines<br>Quality Assurance & Accreditation Management</p>
            <a href="#about" class="btn">Learn More</a>
        </div>
    </section>

    <section class="features" id="services">
        <div class="container">
            <h2>Our Services</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-certificate"></i>
                    <h3>Accreditation Management</h3>
                    <p>Comprehensive management of accreditation processes, documentation, and compliance tracking for all academic programs.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Quality Assurance</h3>
                    <p>Continuous monitoring and improvement of educational quality standards across all university departments.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-file-alt"></i>
                    <h3>Document Management</h3>
                    <p>Secure storage and organization of accreditation documents, reports, and compliance materials.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-users"></i>
                    <h3>Stakeholder Portal</h3>
                    <p>Dedicated portals for faculty, administrators, and accreditation bodies to access relevant information.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-analytics"></i>
                    <h3>Reporting & Analytics</h3>
                    <p>Comprehensive reporting tools and analytics to track accreditation progress and institutional effectiveness.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Compliance Monitoring</h3>
                    <p>Real-time monitoring of compliance requirements and automated alerts for upcoming deadlines.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about" id="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>About PUP Accreditation</h2>
                    <p>The Polytechnic University of the Philippines (PUP) is committed to maintaining the highest standards of educational excellence through rigorous accreditation processes.</p>
                    <p>Our accreditation management system ensures that all academic programs meet national and international quality standards, providing students with world-class education and employers with highly qualified graduates.</p>
                    <p>We work closely with various accrediting bodies including CHED, AACCUP, and international organizations to maintain our commitment to educational excellence.</p>
                    <a href="#contact" class="btn">Get in Touch</a>
                </div>
                <div class="about-image">
                    <i class="fas fa-university"></i>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Contact Information</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Sta. Mesa, Manila, Philippines</p>
                    <p><i class="fas fa-phone"></i> +63 (2) 8335-1PUP</p>
                    <p><i class="fas fa-envelope"></i> accreditation@pup.edu.ph</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <a href="https://www.pup.edu.ph">PUP Main Website</a>
                    <a href="#about">About Accreditation</a>
                    <a href="#services">Our Services</a>
                    <a href="admin/login.php">Admin Portal</a>
                </div>
                <div class="footer-section">
                    <h3>Accrediting Bodies</h3>
                    <a href="#">CHED</a>
                    <a href="#">AACCUP</a>
                    <a href="#">PACUCOA</a>
                    <a href="#">ISO Certification</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Polytechnic University of the Philippines. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
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

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(139, 0, 0, 0.95)';
            } else {
                header.style.background = '#8B0000';
            }
        });

        // Animate feature cards on scroll
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

        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>