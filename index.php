<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante Delicioso - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-utensils me-2"></i>
                Restaurante Delicioso
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#menu">Menú</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#acerca">Acerca</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light ms-2 px-3" href="auth/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="hero-section">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-3 fw-bold mb-4 animate-on-scroll">
                        Sabores Auténticos
                    </h1>
                    <p class="lead mb-4 animate-on-scroll">
                        Descubre una experiencia culinaria única con ingredientes frescos y recetas tradicionales preparadas con amor.
                    </p>
                    <div class="animate-on-scroll">
                        <a href="#menu" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-book-open me-2"></i>Ver Menú
                        </a>
                        <a href="#contacto" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-map-marker-alt me-2"></i>Ubicación
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4 animate-on-scroll">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3 class="fw-bold">500+</h3>
                    <p>Clientes Satisfechos</p>
                </div>
                <div class="col-md-3 mb-4 animate-on-scroll">
                    <i class="fas fa-utensils fa-3x mb-3"></i>
                    <h3 class="fw-bold">50+</h3>
                    <p>Platos Especiales</p>
                </div>
                <div class="col-md-3 mb-4 animate-on-scroll">
                    <i class="fas fa-award fa-3x mb-3"></i>
                    <h3 class="fw-bold">5</h3>
                    <p>Años de Experiencia</p>
                </div>
                <div class="col-md-3 mb-4 animate-on-scroll">
                    <i class="fas fa-star fa-3x mb-3"></i>
                    <h3 class="fw-bold">4.9</h3>
                    <p>Calificación Promedio</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title display-5 fw-bold">Nuestro Menú</h2>
                <p class="lead">Descubre nuestros platos más populares</p>
            </div>
            
            <div class="row">
                <?php
                try {
                    include_once 'config/database.php';
                    include_once 'classes/Producto.php';
                    
                    $database = new Database();
                    $db = $database->getConnection();
                    $producto = new Producto($db);
                    
                    $stmt = $producto->leer();
                    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($productos) > 0) {
                        foreach ($productos as $item) {
                            $imagenSrc = $item['imagen'] ? 'uploads/' . $item['imagen'] : 'https://via.placeholder.com/300x250?text=Sin+Imagen';
                            echo '<div class="col-lg-3 col-md-6 mb-4 animate-on-scroll">';
                            echo '    <div class="card product-card card-hover h-100">';
                            echo '        <img src="' . htmlspecialchars($imagenSrc) . '" class="card-img-top product-image" alt="' . htmlspecialchars($item['nombre']) . '">';
                            echo '        <div class="card-body d-flex flex-column">';
                            echo '            <h5 class="card-title">' . htmlspecialchars($item['nombre']) . '</h5>';
                            echo '            <p class="card-text text-muted flex-grow-1">' . htmlspecialchars($item['descripcion']) . '</p>';
                            echo '            <div class="d-flex justify-content-between align-items-center">';
                            echo '                <span class="h5 text-primary mb-0">$' . number_format($item['precio'], 2) . '</span>';
                            echo '                <span class="badge bg-success">' . $item['cantidad'] . ' disponibles</span>';
                            echo '            </div>';
                            echo '        </div>';
                            echo '    </div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="col-12 text-center">';
                        echo '    <div class="alert alert-info">';
                        echo '        <i class="fas fa-info-circle me-2"></i>';
                        echo '        No hay productos disponibles en este momento.';
                        echo '    </div>';
                        echo '</div>';
                    }
                } catch (Exception $e) {
                    echo '<div class="col-12 text-center">';
                    echo '    <div class="alert alert-warning">';
                    echo '        <i class="fas fa-exclamation-triangle me-2"></i>';
                    echo '        Error al cargar los productos. Por favor, intenta más tarde.';
                    echo '    </div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="acerca" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 animate-on-scroll">
                    <h2 class="display-5 fw-bold mb-4">Nuestra Historia</h2>
                    <p class="lead mb-4">
                        Desde 2019, hemos estado sirviendo los sabores más auténticos de la cocina tradicional, 
                        combinando recetas familiares con técnicas modernas para crear una experiencia única.
                    </p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <i class="fas fa-leaf text-success me-2"></i>
                            <strong>Ingredientes Frescos</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <i class="fas fa-heart text-danger me-2"></i>
                            <strong>Cocinado con Amor</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <i class="fas fa-clock text-primary me-2"></i>
                            <strong>Servicio Rápido</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <i class="fas fa-smile text-warning me-2"></i>
                            <strong>Ambiente Familiar</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center animate-on-scroll">
                    <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                         alt="Nuestro restaurante" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contacto" class="contact-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title display-5 fw-bold text-white">Contáctanos</h2>
                <p class="lead text-white-50">Estamos aquí para servirte</p>
            </div>
            
            <div class="row">
                <div class="col-lg-4 mb-4 animate-on-scroll">
                    <div class="text-center">
                        <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                        <h5>Dirección</h5>
                        <p class="text-white-50">Calle Principal 123<br>Ciudad, País 12345</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 animate-on-scroll">
                    <div class="text-center">
                        <i class="fas fa-phone fa-3x text-primary mb-3"></i>
                        <h5>Teléfono</h5>
                        <p class="text-white-50">+1 234 567 8900<br>+1 234 567 8901</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 animate-on-scroll">
                    <div class="text-center">
                        <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                        <h5>Email</h5>
                        <p class="text-white-50">info@restaurante.com<br>reservas@restaurante.com</p>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-lg-6 mx-auto">
                    <div class="text-center">
                        <h4 class="mb-3">Horarios de Atención</h4>
                        <div class="row">
                            <div class="col-6">
                                <p><strong>Lunes - Viernes</strong><br>11:00 AM - 10:00 PM</p>
                            </div>
                            <div class="col-6">
                                <p><strong>Sábado - Domingo</strong><br>10:00 AM - 11:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-utensils fa-2x text-primary me-3"></i>
                        <div>
                            <h5 class="mb-0">Restaurante Delicioso</h5>
                            <small class="text-muted">Sabores auténticos desde 2019</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-whatsapp"></i></a>
                    </div>
                    <p class="mt-2 mb-0 text-muted">© 2024 Restaurante Delicioso. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling
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

        // Animate on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>