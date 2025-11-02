<?php
include_once 'conexion.php';

$conn = conectar();

$consultaStock = $conn->query("SELECT EXISTS (SELECT 1 FROM Inventario_MateriaPrima 
                                  WHERE stock < cantidad_minima) AS StockBajo");
$resultado = $consultaStock->fetch_assoc();

$estado = (int)$resultado['StockBajo'];
echo json_encode(['estado' => $estado]);
$conn->close();
?>