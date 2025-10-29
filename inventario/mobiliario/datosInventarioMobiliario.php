<?php
include_once '../../conexion.php';

$conn = conectar();
header('Content-Type: application/json; charset=UTF-8');

$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$registrosPorPagina = 20;
$offset = ($pagina - 1) * $registrosPorPagina;

$descripcion = isset($_GET['descripcion']) ? trim($_GET['descripcion']) : '';
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$sucursal = isset($_GET['sucursal']) ? trim($_GET['sucursal']) : '';

// ---- Construcción dinámica de condiciones ----
$condiciones = [];
$parametros = [];
$tipos = '';

if (!empty($descripcion)) {
    $condiciones[] = "mb.nombre_mobiliario LIKE ?";
    $parametros[] = "%{$descripcion}%";
    $tipos .= 's';
}

if (!empty($categoria)) {
    $condiciones[] = "cm.nombre_categoria LIKE ?";
    $parametros[] = "%{$categoria}%";
    $tipos .= 's';
}

if (!empty($sucursal)) {
    $condiciones[] = "s.nombre_sucursal = ?";
    $parametros[] = $sucursal;
    $tipos .= 's';
}

$where = count($condiciones) > 0 ? 'WHERE ' . implode(' AND ', $condiciones) : '';

// ---- CONSULTA PARA CONTAR ----
$sqlContar = "SELECT COUNT(*) AS total
              FROM Inventario_Mobiliario imb
              INNER JOIN Mobiliario mb ON imb.id_mobiliario = mb.id_mobiliario
              INNER JOIN Categoria_Mobiliario cm ON mb.id_categoria_mobiliario = cm.id_categoria_mobiliario
              INNER JOIN Almacenes_Sucursal Als ON imb.id_almacen = Als.id_almacen
              INNER JOIN Sucursales s ON Als.id_sucursal = s.id_sucursal
              $where";

$stmt = $conn->prepare($sqlContar);
if (!empty($parametros)) {
    $stmt->bind_param($tipos, ...$parametros);
}
$stmt->execute();
$totalRegistros = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// ---- CONSULTA PRINCIPAL ----
$sql = "SELECT
            imb.id_inventario_mobiliario as idInventario,
            mb.id_mobiliario AS codigoMobiliario,
            mb.nombre_mobiliario AS mobiliario,
            mb.descripcion AS descripcion,
            cm.nombre_categoria AS categoriaMobiliario,
            imb.stock AS cantidad
        FROM Inventario_Mobiliario imb
        INNER JOIN Mobiliario mb ON imb.id_mobiliario = mb.id_mobiliario
        INNER JOIN Categoria_Mobiliario cm ON mb.id_categoria_mobiliario = cm.id_categoria_mobiliario
        INNER JOIN Almacenes_Sucursal Als ON imb.id_almacen = Als.id_almacen
        INNER JOIN Sucursales s ON Als.id_sucursal = s.id_sucursal
        $where
        ORDER BY imb.stock ASC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

// Agregamos paginación a los parámetros
$parametrosConPaginacion = $parametros;
$tiposConPaginacion = $tipos . 'ii';
$parametrosConPaginacion[] = $registrosPorPagina;
$parametrosConPaginacion[] = $offset;

// Enlazamos todos los parámetros
$stmt->bind_param($tiposConPaginacion, ...$parametrosConPaginacion);
$stmt->execute();
$resultado = $stmt->get_result();

$datos = [];
while ($fila = $resultado->fetch_assoc()) {
    $filaSanitizada = array_map(fn($valor) => htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), $fila);
    $datos[] = $filaSanitizada;
}

$stmt->close();
$conn->close();

echo json_encode([
    'datos' => $datos,
    'totalPaginas' => $totalPaginas,
    'paginaActual' => $pagina
]);
?>
