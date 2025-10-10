<?php
include_once 'conexion.php';
// Conectar a la base de datos
$conn = conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_proveedor = $_POST['id_proveedor'];
    $fecha_compra = $_POST['fecha_compra'];
    $total_compra = $_POST['total_compra'];
    
    // Insertar en la tabla Compras
    $sql_compra = "INSERT INTO Compras (id_proveedor, fecha, total, estado) VALUES (?, ?, ?, 'pendiente')";
    $stmt = $conn->prepare($sql_compra);
    $stmt->bind_param("iss", $id_proveedor, $fecha_compra, $total_compra);
    
    if ($stmt->execute()) {
        $id_compra = $conn->insert_id;
        
        // Insertar detalles de la compra
        $id_insumos = $_POST['id_insumo'];
        $cantidades = $_POST['cantidad_insumo'];
        $precios = $_POST['precio_unitario'];
        
        $sql_detalle = "INSERT INTO DetalleCompra (id_compra, id_insumo, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
        $stmt_detalle = $conn->prepare($sql_detalle);
        
        for ($i = 0; $i < count($id_insumos); $i++) {
            $stmt_detalle->bind_param("iidd", $id_compra, $id_insumos[$i], $cantidades[$i], $precios[$i]);
            $stmt_detalle->execute();
        }
        
        echo "Compra guardada exitosamente. <a href='compras.php'>Volver a Compras</a>";
    } else {
        echo "Error al guardar la compra: " . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo "Método no permitido";
}
?>