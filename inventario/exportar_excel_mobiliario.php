<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
$db = conectar();

// --- FILTRO por almacén ---
$filtro = "1=1";
$almacen_nombre = "Todos los registros";

if (!empty($_GET['almacen'])) {
    $almacen = $db->real_escape_string($_GET['almacen']);
    $filtro .= " AND i.id_almacen = '$almacen'";

    // Obtener nombre del almacén
    $res = $db->query("SELECT nombre FROM almacenes_sucursal WHERE id_almacen='$almacen'");
    if($res && $res->num_rows>0){
        $row = $res->fetch_assoc();
        $almacen_nombre = $row['nombre'];
    }
}

// --- Consulta inventario mobiliario ---
$inventario = $db->query("
    SELECT i.id_inventario_mobiliario, m.nombre_mobiliario, i.stock, a.nombre AS nombre_almacen
    FROM inventario_mobiliario i
    INNER JOIN mobiliario m ON i.id_mobiliario = m.id_mobiliario
    INNER JOIN almacenes_sucursal a ON i.id_almacen = a.id_almacen
    WHERE $filtro
    ORDER BY m.nombre_mobiliario ASC
");

if(!$inventario){
    die("Error en la consulta: " . $db->error);
}

// --- Exportar a Excel ---
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=inventario_mobiliario.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr><th colspan='4' style='text-align:center'>Inventario de Mobiliario - Almacén: $almacen_nombre</th></tr>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Mobiliario</th>";
echo "<th>Stock</th>";
echo "<th>Almacén</th>";
echo "</tr>";

if($inventario->num_rows>0){
    while($i = $inventario->fetch_assoc()){
        echo "<tr>";
        echo "<td>{$i['id_inventario_mobiliario']}</td>";
        echo "<td>".htmlspecialchars($i['nombre_mobiliario'])."</td>";
        echo "<td>{$i['stock']}</td>";
        echo "<td>".htmlspecialchars($i['nombre_almacen'])."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4' style='text-align:center'>No se encontraron resultados</td></tr>";
}

echo "</table>";
?>

