<?php
header('X-Content-Type-Options: nosniff');

$format = $_GET['format'] ?? 'options'; // forzamos options para tu <select>

try {
    $pdo = new PDO("odbc:DSN=DW");                 // <-- DSN
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // OJO: nombres coinciden con tu CREATE TABLE
    $sql = "SELECT id_proveedor, nombre_proveedor AS nombre
            FROM Proveedores
            ORDER BY nombre_proveedor";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/html; charset=UTF-8');
    echo '<option value="">Seleccione un proveedor</option>';
    foreach ($rows as $r) {
        $id  = htmlspecialchars($r['id_proveedor'], ENT_QUOTES, 'UTF-8');
        $nom = htmlspecialchars($r['nombre'],        ENT_QUOTES, 'UTF-8');
        echo "<option value=\"{$id}\">{$nom}</option>";
    }
} catch (PDOException $e) {
    // Muestra el error en la respuesta (temporal para depurar)
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "ERROR: " . $e->getMessage();
}
