
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../config/database.php';
include_once '../classes/Producto.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    $producto = new Producto($db);
    $stmt = $producto->leer();
    $productos = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $productos[] = array(
            'id' => $row['id'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'precio' => $row['precio'],
            'cantidad' => $row['cantidad'],
            'imagen' => $row['imagen']
        );
    }

    echo json_encode($productos);
} else {
    echo json_encode(array('error' => 'Error de conexión a la base de datos'));
}
?>
