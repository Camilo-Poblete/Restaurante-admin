
<?php
class Producto {
    private $conn;
    private $table_name = "productos";

    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $cantidad;
    public $imagen;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function leer() {
        $query = "SELECT id, nombre, descripcion, precio, cantidad, imagen FROM " . $this->table_name . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function leerUno() {
        $query = "SELECT nombre, descripcion, precio, cantidad, imagen FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->precio = $row['precio'];
            $this->cantidad = $row['cantidad'];
            $this->imagen = $row['imagen'];
            return true;
        }
        return false;
    }

    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre=:nombre, descripcion=:descripcion, precio=:precio, cantidad=:cantidad, imagen=:imagen";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":cantidad", $this->cantidad);
        $stmt->bindParam(":imagen", $this->imagen);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function actualizar() {
        $query = "UPDATE " . $this->table_name . " SET nombre=:nombre, descripcion=:descripcion, precio=:precio, cantidad=:cantidad";
        
        if (!empty($this->imagen)) {
            $query .= ", imagen=:imagen";
        }
        
        $query .= " WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':cantidad', $this->cantidad);
        $stmt->bindParam(':id', $this->id);
        
        if (!empty($this->imagen)) {
            $stmt->bindParam(':imagen', $this->imagen);
        }

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function eliminar() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
