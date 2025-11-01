<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
$db = conectar();

// --- FILTRO por nombre o dirección ---
$filtro = "1=1";
$busqueda_filtro = "";
if (!empty($_GET['busqueda'])) {
    $busqueda_filtro = $db->real_escape_string($_GET['busqueda']);
    $filtro .= " AND (nombre_proveedor LIKE '%$busqueda_filtro%' OR direccion_proveedor LIKE '%$busqueda_filtro%')";
}

// --- Consulta de proveedores ---
$proveedores = $db->query("
    SELECT id_proveedor, nombre_proveedor, telefono_proveedor, correo_proveedor, direccion_proveedor
    FROM proveedores
    WHERE $filtro
    ORDER BY nombre_proveedor ASC
");

// --- Encabezado dinámico ---
$encabezado = "Reporte de Proveedores";
if (!empty($busqueda_filtro)) {
    $encabezado .= " - Búsqueda: $busqueda_filtro";
} else {
    $encabezado .= " - Todos los registros";
}

// --- Exportar Excel ---
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=reporte_proveedores.xls");
echo "<table border='1'>";
echo "<tr><th colspan='5'>$encabezado</th></tr>";
echo "<tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Teléfono</th>
        <th>Correo</th>
        <th>Dirección</th>
      </tr>";

if($proveedores && $proveedores->num_rows > 0){
    while($p = $proveedores->fetch_assoc()){
        echo "<tr>
                <td>{$p['id_proveedor']}</td>
                <td>".htmlspecialchars($p['nombre_proveedor'])."</td>
                <td>".htmlspecialchars($p['telefono_proveedor'])."</td>
                <td>".htmlspecialchars($p['correo_proveedor'])."</td>
                <td>".htmlspecialchars($p['direccion_proveedor'])."</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5' style='text-align:center'>No se encontraron resultados.</td></tr>";
}

echo "</table>";
?>
