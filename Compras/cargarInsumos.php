<?php
// Compras/cargarInsumos.php 
header('X-Content-Type-Options: nosniff');

$format = $_GET['format'] ?? 'options'; 

try {
    $pdo = new PDO("odbc:DSN=DW");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT id_materia_prima AS id_insumo, nombre_insumos
            FROM MateriaPrima
            ORDER BY id_materia_prima";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    if ($format === 'json') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE);
        exit;
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo '<option value="">Seleccione un insumo</option>';
    foreach ($rows as $r) {
        $id  = htmlspecialchars($r['id_insumo'], ENT_QUOTES, 'UTF-8');
        $nom = htmlspecialchars($r['nombre_insumos'], ENT_QUOTES, 'UTF-8');
        echo "<option value=\"{$id}\">{$nom}</option>";
    }
} catch (Throwable $e) {
    http_response_code(500);
    if ($format === 'json') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    } else {
        header('Content-Type: text/html; charset=UTF-8');
        echo '<option value="">Error cargando insumos</option>';
    }
}
