
<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $usuario;
    public $password;
    public $nombre;
    public $rol;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($usuario, $password) {
        $query = "SELECT id, usuario, password, nombre, rol FROM " . $this->table_name . " WHERE usuario = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $usuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->usuario = $row['usuario'];
                $this->nombre = $row['nombre'];
                $this->rol = $row['rol'];
                return true;
            }
        }
        return false;
    }

    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " SET usuario=:usuario, password=:password, nombre=:nombre, rol=:rol";
        $stmt = $this->conn->prepare($query);

        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":usuario", $this->usuario);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":rol", $this->rol);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
