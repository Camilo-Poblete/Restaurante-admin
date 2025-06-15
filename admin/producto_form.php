
<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../auth/login.php");
    exit;
}

include_once '../config/database.php';
include_once '../classes/Producto.php';

$database = new Database();
$db = $database->getConnection();
$producto = new Producto($db);

$editar = false;
$titulo = "Nuevo Producto";

// Si hay ID, cargar datos para editar
if (isset($_GET['id'])) {
    $editar = true;
    $titulo = "Editar Producto";
    $producto->id = $_GET['id'];
    $producto->leerUno();
}

// Procesar formulario
if ($_POST) {
    $producto->nombre = $_POST['nombre'];
    $producto->descripcion = $_POST['descripcion'];
    $producto->precio = $_POST['precio'];
    $producto->cantidad = $_POST['cantidad'];
    
    // Manejar subida de imagen
    $imagen_nombre = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $permitidos = array("jpg", "jpeg", "png", "gif");
        $temp = explode(".", $_FILES["imagen"]["name"]);
        $extension = end($temp);
        
        if (in_array(strtolower($extension), $permitidos) && $_FILES["imagen"]["size"] < 5000000) {
            $imagen_nombre = uniqid() . "." . $extension;
            $ruta_destino = "../uploads/" . $imagen_nombre;
            
            // Crear directorio si no existe
            if (!file_exists("../uploads/")) {
                mkdir("../uploads/", 0777, true);
            }
            
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_destino)) {
                $producto->imagen = $imagen_nombre;
            } else {
                $error = "Error al subir la imagen";
            }
        } else {
            $error = "Formato de imagen no válido o archivo muy grande";
        }
    } elseif ($editar && !isset($_FILES['imagen'])) {
        // Si es edición y no se subió nueva imagen, mantener la actual
        $producto->imagen = "";
    }
    
    if (!isset($error)) {
        if ($editar) {
            $producto->id = $_POST['id'];
            if ($producto->actualizar()) {
                $mensaje = "Producto actualizado exitosamente";
                $tipo_mensaje = "success";
            } else {
                $error = "Error al actualizar el producto";
            }
        } else {
            if ($producto->crear()) {
                $mensaje = "Producto creado exitosamente";
                $tipo_mensaje = "success";
                // Limpiar formulario
                $producto = new Producto($db);
            } else {
                $error = "Error al crear el producto";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Sistema Restaurante</title>
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
        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
            border-radius: 5px;
        }
        .imagen-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .upload-area.drag-over {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-3">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-utensils me-2"></i>
                Restaurante Admin
            </a>
            <hr class="text-white">
            
            <div class="user-info mb-4 text-center">
                <i class="fas fa-user-circle fa-3x text-white mb-2"></i>
                <h6 class="text-white"><?php echo $_SESSION['nombre']; ?></h6>
                <small class="text-white-50"><?php echo ucfirst($_SESSION['rol']); ?></small>
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
            <div class="row mb-4">
                <div class="col">
                    <h1 class="h3 mb-0"><?php echo $titulo; ?></h1>
                    <p class="text-muted">Complete el formulario para <?php echo $editar ? 'actualizar' : 'crear'; ?> un producto</p>
                </div>
                <div class="col-auto">
                    <a href="productos.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                    <i class="fas fa-check"></i> <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <?php if ($editar): ?>
                                    <input type="hidden" name="id" value="<?php echo $producto->id; ?>">
                                <?php endif; ?>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label">Nombre del Producto *</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" 
                                               value="<?php echo htmlspecialchars($producto->nombre ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="precio" class="form-label">Precio *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control" id="precio" name="precio" 
                                                   value="<?php echo $producto->precio ?? ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="cantidad" class="form-label">Cantidad *</label>
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                               value="<?php echo $producto->cantidad ?? ''; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($producto->descripcion ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Imagen del Producto</label>
                                    <div class="upload-area" onclick="document.getElementById('imagen').click()">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="mb-0">Haz clic aquí o arrastra una imagen</p>
                                        <small class="text-muted">JPG, PNG, GIF hasta 5MB</small>
                                    </div>
                                    <input type="file" class="form-control d-none" id="imagen" name="imagen" accept="image/*">
                                    <div id="imagen-preview" class="mt-3"></div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> <?php echo $editar ? 'Actualizar' : 'Crear'; ?> Producto
                                    </button>
                                    <a href="productos.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <?php if ($editar && $producto->imagen): ?>
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Imagen Actual</h6>
                        </div>
                        <div class="card-body text-center">
                            <img src="../uploads/<?php echo htmlspecialchars($producto->imagen); ?>" 
                                 class="imagen-preview" alt="Imagen actual">
                            <p class="text-muted mt-2">Sube una nueva imagen para reemplazar esta</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Información</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-info-circle text-primary"></i> Los campos marcados con * son obligatorios</li>
                                <li><i class="fas fa-image text-primary"></i> Formatos de imagen permitidos: JPG, PNG, GIF</li>
                                <li><i class="fas fa-weight text-primary"></i> Tamaño máximo de imagen: 5MB</li>
                                <li><i class="fas fa-dollar-sign text-primary"></i> El precio debe incluir decimales (ej: 12.99)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview de imagen
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagen-preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="text-center">
                            <img src="${e.target.result}" class="imagen-preview" alt="Preview">
                            <p class="text-muted mt-2">Preview de la nueva imagen</p>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop
        const uploadArea = document.querySelector('.upload-area');
        const fileInput = document.getElementById('imagen');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('drag-over');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
