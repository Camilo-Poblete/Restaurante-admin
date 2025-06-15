<?php
include_once 'config/database.php';
include_once 'classes/Producto.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $producto = new Producto($db);

    // Verificar si ya hay productos
    $stmt = $producto->leer();
    $productos_existentes = $stmt->fetchAll();

    if (count($productos_existentes) == 0) {
        // Productos de ejemplo
        $productos_ejemplo = [
            [
                'nombre' => 'Pizza Margherita',
                'descripcion' => 'Pizza clásica con tomate, mozzarella fresca y albahaca',
                'precio' => 15.99,
                'cantidad' => 20,
                'imagen' => 'pizza_margherita.jpg'
            ],
            [
                'nombre' => 'Hamburguesa Deluxe',
                'descripcion' => 'Hamburguesa de carne angus con queso, lechuga, tomate y papas fritas',
                'precio' => 12.50,
                'cantidad' => 15,
                'imagen' => 'hamburguesa_deluxe.jpg'
            ],
            [
                'nombre' => 'Ensalada César',
                'descripcion' => 'Lechuga romana, pollo a la parrilla, crutones y aderezo césar',
                'precio' => 9.75,
                'cantidad' => 25,
                'imagen' => 'ensalada_cesar.jpg'
            ],
            [
                'nombre' => 'Pasta Carbonara',
                'descripcion' => 'Espaguetis con salsa carbonara, panceta y parmesano',
                'precio' => 13.25,
                'cantidad' => 18,
                'imagen' => 'pasta_carbonara.jpg'
            ],
            [
                'nombre' => 'Salmón a la Plancha',
                'descripcion' => 'Filete de salmón fresco con verduras al vapor y salsa de limón',
                'precio' => 18.50,
                'cantidad' => 12,
                'imagen' => 'salmon_plancha.jpg'
            ],
            [
                'nombre' => 'Paella Valenciana',
                'descripcion' => 'Auténtica paella valenciana con pollo, conejo, verduras y azafrán',
                'precio' => 24.99,
                'cantidad' => 15,
                'imagen' => 'paella.jpg'
            ],
            [
                'nombre' => 'Ceviche Peruano',
                'descripcion' => 'Pescado fresco marinado en limón con cebolla morada y ají',
                'precio' => 16.50,
                'cantidad' => 20,
                'imagen' => 'ceviche.jpg'
            ],
            [
                'nombre' => 'Tiramisú',
                'descripcion' => 'Postre italiano tradicional con café, mascarpone y cacao',
                'precio' => 6.99,
                'cantidad' => 30,
                'imagen' => 'tiramisu.jpg'
            ]
        ];

        foreach ($productos_ejemplo as $item) {
            $producto->nombre = $item['nombre'];
            $producto->descripcion = $item['descripcion'];
            $producto->precio = $item['precio'];
            $producto->cantidad = $item['cantidad'];
            $producto->imagen = $item['imagen'];
            $producto->crear();
        }

        echo "Productos de ejemplo agregados exitosamente!";
    } else {
        echo "Ya existen productos en la base de datos.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>