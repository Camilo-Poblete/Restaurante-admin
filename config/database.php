
<?php
class Database {
    private $host = "localhost";
    private $db_name = "restaurante_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Crear base de datos si no existe
            $temp_conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $temp_conn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);
            $temp_conn = null;

            // Conectar a la base de datos
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Crear tablas si no existen
            $this->createTables();

        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }

    private function createTables() {
        try {
            // Crear tabla usuarios
            $sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                nombre VARCHAR(100) NOT NULL,
                rol ENUM('admin', 'empleado') DEFAULT 'empleado',
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->conn->exec($sql_usuarios);

            // Crear tabla productos
            $sql_productos = "CREATE TABLE IF NOT EXISTS productos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                descripcion TEXT,
                precio DECIMAL(10,2) NOT NULL,
                cantidad INT NOT NULL DEFAULT 0,
                imagen VARCHAR(255),
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->conn->exec($sql_productos);

            // Insertar usuarios por defecto si no existen
            $check_admin = $this->conn->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = 'admin'");
            $check_admin->execute();

            if ($check_admin->fetchColumn() == 0) {
                $admin_password = password_hash('password', PASSWORD_DEFAULT);
                $empleado_password = password_hash('password', PASSWORD_DEFAULT);

                $sql_insert = "INSERT INTO usuarios (usuario, password, nombre, rol) VALUES 
                    ('admin', :admin_pass, 'Administrador', 'admin'),
                    ('empleado', :emp_pass, 'Empleado', 'empleado')";

                $stmt = $this->conn->prepare($sql_insert);
                $stmt->bindParam(':admin_pass', $admin_password);
                $stmt->bindParam(':emp_pass', $empleado_password);
                $stmt->execute();
            }

        } catch(PDOException $e) {
            // Las tablas ya existen o hay otro error
        }
    }
}
?>
 
        

