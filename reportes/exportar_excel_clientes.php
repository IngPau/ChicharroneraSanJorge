<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
$db = conectar();

// --- FILTRO por dirección ---
$filtro = "1=1";
$direccion_filtro = "";
if (!empty($_GET['direccion'])) {
    $direccion_filtro = $db->real_escape_string($_GET['direccion']);
    $filtro .= " AND direccion_cliente LIKE '%$direccion_filtro%'";
}

// --- Consulta de clientes ---
$clientes = $db->query("
    SELECT id_cliente, nombre_cliente, apellido_cliente, telefono_cliente, correo_cliente, direccion_cliente
    FROM clientes
    WHERE $filtro
    ORDER BY nombre_cliente ASC
");

if(!$clientes){
    die("Error en la consulta: " . $db->error);
}

// --- Preparar exportación Excel ---
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=listado_clientes.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";

// Encabezado dinámico
if(!empty($direccion_filtro)){
    echo "<tr><th colspan='5'>Listado de Clientes - Dirección: $direccion_filtro</th></tr>";
} else {
    echo "<tr><th colspan='5'>Listado de Clientes - Todos los registros</th></tr>";
}

echo "<tr>
        <th>ID</th>
        <th>Nombre Completo</th>
        <th>Teléfono</th>
        <th>Correo</th>
        <th>Dirección</th>
      </tr>";

// Contenido
while($c = $clientes->fetch_assoc()){
    echo "<tr>
            <td>{$c['id_cliente']}</td>
            <td>{$c['nombre_cliente']} {$c['apellido_cliente']}</td>
            <td>{$c['telefono_cliente']}</td>
            <td>{$c['correo_cliente']}</td>
            <td>{$c['direccion_cliente']}</td>
          </tr>";
}

echo "</table>";
?>
