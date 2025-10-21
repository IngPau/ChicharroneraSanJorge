<?php
// Compras/cargarInsumos.php 
header('X-Content-Type-Options: nosniff');
header('X-Content-Type-Options: nosniff');
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require_once __DIR__ . '/../conexion.php'; 
try {
    $conn = conectar();

    $sql = "SELECT id_materia_prima, nombre_insumos AS nombre
            FROM MateriaPrima
            ORDER BY nombre_insumos";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    echo '<option value="">Seleccione un Insumo</option>';
    while ($r = $result->fetch_assoc()) {
        $id  = htmlspecialchars($r['id_materia_prima'], ENT_QUOTES, 'UTF-8');
        $nom = htmlspecialchars($r['nombre'],        ENT_QUOTES, 'UTF-8');
        echo "<option value=\"{$id}\">{$nom}</option>";
    }
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "❌ ERROR: " . $e->getMessage();
}


