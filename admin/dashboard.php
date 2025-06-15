<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../auth/login.php");
    exit;
}

require_once '../config/database.php';
require_once '../classes/Producto.php';

$database = new Database();
$db = $database->getConnection();
$producto = new Producto($db);

// Obtener productos y calcular totales
$productos = $producto->leer()->fetchAll(PDO::FETCH_ASSOC);
$numProductos = count($productos);

// Calcular valor total del inventario
$totalInventario = array_reduce($productos, function($carry, $item) {
    return $carry + ($item['precio'] * $item['cantidad']);
}, 0);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            width: 250px;
            position: fixed;
        }
        .main-content {
            margin-left: 250px;
        }
        .stats-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar p-3">
        <a class="d-flex align-items-center text-white text-decoration-none mb-4">
            <i class="fas fa-utensils me-2"></i>
            <span class="fs-5 fw-bold">Restaurante Admin</span>
        </a>
        <hr class="text-white">
        
        <div class="text-center mb-4">
            <i class="fas fa-user-circle fa-3x text-white mb-2"></i>
            <h6 class="text-white"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?></h6>
            <small class="text-white-50"><?= ucfirst(htmlspecialchars($_SESSION['rol'] ?? 'usuario')) ?></small>
        </div>
        
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="productos.php">
                    <i class="fas fa-box me-2"></i> Productos
                </a>
            </li>
            <?php if (($_SESSION['rol'] ?? '') == 'admin'): ?>
           
            <?php endif; ?>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="../auth/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Dashboard</h1>
                <p class="text-muted">Resumen del sistema</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card stats-card text-center p-3">
                    <i class="fas fa-box fa-2x mb-2"></i>
                    <h4><?= $numProductos ?></h4>
                    <p class="mb-0">Total Productos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card text-center p-3">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <h4>$<?= number_format($totalInventario, 2) ?></h4>
                    <p class="mb-0">Valor Inventario</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card text-center p-3">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h4>2</h4>
                    <p class="mb-0">Usuarios Activos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card text-center p-3">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h4 id="current-time"></h4>
                    <p class="mb-0">Hora Actual</p>
                </div>
            </div>
        </div>

        <!-- Products Overview -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Productos Recientes</h5>
                <a href="productos.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Gestionar
                </a>
            </div>
            <div class="card-body">
                <?php if ($numProductos > 0): ?>
                    <div class="row g-3">
                        <?php foreach (array_slice($productos, 0, 4) as $producto): ?>
                        <div class="col-md-3">
                            <div class="card h-100">
                                <?php if (!empty($producto['imagen'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($producto['imagen']) ?>" 
                                         class="card-img-top product-image" 
                                         alt="<?= htmlspecialchars($producto['nombre']) ?>">
                                <?php else: ?>
                                    <div class="card-img-top product-image bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h6>
                                    <p class="card-text text-muted small">
                                        <?= substr(htmlspecialchars($producto['descripcion']), 0, 50) . '...' ?>
                                    </p>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-success fw-bold">$<?= number_format($producto['precio'], 2) ?></span>
                                        <span class="badge bg-info"><?= $producto['cantidad'] ?> und.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                        <h5>No hay productos registrados</h5>
                        <a href="productos.php" class="btn btn-primary">Agregar Producto</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateTime() {
            document.getElementById('current-time').textContent = 
                new Date().toLocaleTimeString('es-ES');
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>