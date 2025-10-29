<?php
include_once '../../conexion.php';

$conn = conectar();
header('Content-Type: application/json; charset=UTF-8');

$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$registrosPorPagina = 20;
$offset = ($pagina - 1) * $registrosPorPagina;

$descripcion = isset($_GET['descripcion']) ? trim($_GET['descripcion']) : '';
$sucursal = isset($_GET['sucursal']) ? trim($_GET['sucursal']) : '';

$condiciones = [];
$parametros = [];
$tipos = '';

if (!empty($descripcion)) {
    $condiciones[] = "mp.nombre_insumos LIKE ?";
    $parametros[] = "%{$descripcion}%";
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
              FROM Perdidas p
              INNER JOIN MateriaPrima mp ON p.id_materia_prima = mp.id_materia_prima
              INNER JOIN Almacenes_Sucursal Als ON p.id_almacen = Als.id_almacen
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
            p.id_perdida as idPerdida,
            mp.nombre_insumos AS materiaPrima,
            p.motivo AS motivo,
            p.cantidad AS cantidad,
            p.fecha AS fecha
        FROM Perdidas p
        INNER JOIN MateriaPrima mp ON p.id_materia_prima = mp.id_materia_prima
        INNER JOIN Almacenes_Sucursal Als ON p.id_almacen = Als.id_almacen
        INNER JOIN Sucursales s ON Als.id_sucursal = s.id_sucursal
        $where
        ORDER BY p.fecha ASC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

$parametrosConPaginacion = $parametros;
$tiposConPaginacion = $tipos . 'ii';
$parametrosConPaginacion[] = $registrosPorPagina;
$parametrosConPaginacion[] = $offset;


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
