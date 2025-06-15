
-- Base de datos para el sistema de restaurante
CREATE DATABASE IF NOT EXISTS restaurante_db;
USE restaurante_db;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'empleado') DEFAULT 'empleado',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar usuario admin por defecto
INSERT INTO usuarios (usuario, password, nombre, rol) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin'),
('empleado', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Empleado', 'empleado');
-- Contraseña por defecto: password

-- Insertar algunos productos de ejemplo
INSERT INTO productos (nombre, descripcion, precio, cantidad, imagen) VALUES 
('Pizza Margherita', 'Pizza tradicional con tomate, mozzarella y albahaca fresca', 12.99, 50, 'pizza_margherita.jpg'),
('Hamburguesa Clásica', 'Hamburguesa de carne con lechuga, tomate, cebolla y queso', 8.99, 30, 'hamburguesa_clasica.jpg'),
('Ensalada César', 'Ensalada fresca con pollo, crutones y aderezo césar', 7.50, 25, 'ensalada_cesar.jpg'),
('Pasta Carbonara', 'Pasta cremosa con panceta, huevo y queso parmesano', 11.50, 40, 'pasta_carbonara.jpg');
