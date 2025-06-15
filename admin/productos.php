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

// Manejar eliminación
if (isset($_GET['eliminar'])) {
    $producto->id = $_GET['eliminar'];
    if ($producto->eliminar()) {
        $mensaje = "Producto eliminado exitosamente";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "Error al eliminar el producto";
        $tipo_mensaje = "danger";
    }
}

$stmt = $producto->leer();
$num = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Sistema Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-3">
            <a class="navbar-brand d-flex align-items-center text-white fw-bold" href="#">
                <i class="fas fa-utensils me-2"></i>
                Restaurante Admin
            </a>
            <hr class="text-white">
            
            <div class="user-info mb-4 text-center">
                <i class="fas fa-user-circle fa-3x text-white mb-2"></i>
                <h6 class="text-white"><?php echo htmlspecialchars($_SESSION['nombre']); ?></h6>
                <small class="text-white-50"><?php echo ucfirst(htmlspecialchars($_SESSION['rol'])); ?></small>
            </div>
            
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="productos.php">
                        <i class="fas fa-box me-2"></i> Productos
                    </a>
                </li>
                <?php if ($_SESSION['rol'] == 'admin'): ?>
            
                </li>
                <?php endif; ?>
                <li class="nav-item mt-3">
                    <a class="nav-link text-danger" href="../auth/logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Gestión de Productos</h1>
                    <p class="text-muted">Administra el inventario del restaurante</p>
                </div>
                <a href="producto_form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </a>
            </div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                    <i class="fas fa-<?php echo $tipo_mensaje == 'success' ? 'check' : 'exclamation-triangle'; ?>"></i>
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <?php if ($num > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td>
                                            <?php if ($row['imagen']): ?>
                                                <img src="../uploads/<?php echo htmlspecialchars($row['imagen']); ?>" 
                                                     class="product-image" 
                                                     alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                                            <?php else: ?>
                                                <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($row['nombre']); ?></td>
                                        <td class="text-success fw-bold">$<?php echo number_format($row['precio'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['cantidad'] > 10 ? 'success' : ($row['cantidad'] > 0 ? 'warning' : 'danger'); ?>">
                                                <?php echo $row['cantidad']; ?> und.
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="producto_form.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmarEliminacion(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nombre']); ?>')" 
                                                        class="btn btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h5>No hay productos registrados</h5>
                            <a href="producto_form.php" class="btn btn-primary">Agregar Producto</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar el producto <strong id="producto-nombre"></strong>?</p>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" id="btn-confirmar-eliminacion" class="btn btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarEliminacion(id, nombre) {
            document.getElementById('producto-nombre').textContent = nombre;
            document.getElementById('btn-confirmar-eliminacion').href = 'productos.php?eliminar=' + id;
            new bootstrap.Modal(document.getElementById('modalConfirmacion')).show();
        }
    </script>
</body>
</html>