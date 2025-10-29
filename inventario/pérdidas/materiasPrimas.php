<?php
include_once '../../conexion.php';

$conn = conectar();
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

// Preferir valor enviado en JSON; si no existe, fallback a $_GET
$sucursal = isset($data['sucursal']) ? trim($data['sucursal'])
          : (isset($_GET['sucursal']) ? trim($_GET['sucursal']) : '');
// Sanitizar
$sucursal = filter_var($sucursal, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


$sql = $conn->prepare("SELECT m.id_materia_prima AS codigoMateria, m.nombre_insumos as materiaPrima
                       FROM MateriaPrima m
                       INNER JOIN Inventario_MateriaPrima imp ON m.id_materia_prima = imp.id_insumo
                       INNER JOIN Almacenes_Sucursal Als ON imp.id_almacen = Als.id_almacen
                       INNER JOIN Sucursales s ON Als.id_sucursal = s.id_sucursal
                       WHERE s.nombre_sucursal = ?");
$sql->bind_param("s", $sucursal);
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<option value='" . htmlspecialchars($row["codigoMateria"]) . "'>" 
           . htmlspecialchars($row["materiaPrima"]) . 
           "</option>";
    }
} else {
    echo "<option value=''>No hay materia prima ingresado en el inventario</option>";
}

?>

