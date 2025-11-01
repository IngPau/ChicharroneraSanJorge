<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
$db = conectar();

// --- FILTROS ---
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

// Consulta inventario mobiliario
$inventario = $db->query("
    SELECT i.id_inventario_mobiliario, m.nombre_mobiliario, i.stock, a.nombre AS nombre_almacen
    FROM inventario_mobiliario i
    INNER JOIN mobiliario m ON i.id_mobiliario = m.id_mobiliario
    INNER JOIN almacenes_sucursal a ON i.id_almacen = a.id_almacen
    WHERE $filtro
    ORDER BY m.nombre_mobiliario ASC
");

// Almacenes para filtro
$almacenes = $db->query("SELECT id_almacen, nombre FROM almacenes_sucursal");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inventario Mobiliario</title>
    <link rel="stylesheet" href="../SideBar/sidebar.css">
    <link rel="stylesheet" href="../globales.css">
    <link rel="stylesheet" href="../ventas/ventas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />
</head>
<body>
<div class="container">
    <?php include '../SideBar/sidebar.php'; ?>
    <main class="main">
        <h1>Reporte de Inventario de Mobiliario</h1>

        <!-- Filtro por almacén -->
        <form method="GET" class="formulario">
            <div style="display:flex; gap:15px;">
                <div>
                    <label>Almacén:</label>
                    <select name="almacen">
                        <option value="">-- Todos --</option>
                        <?php while($a = $almacenes->fetch_assoc()): ?>
                            <?php $sel = (isset($_GET['almacen']) && $_GET['almacen']==$a['id_almacen'])?'selected':''; ?>
                            <option value="<?= $a['id_almacen'] ?>" <?= $sel ?>><?= htmlspecialchars($a['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div style="display:flex; align-items:flex-end; gap:10px;">
                    <button type="submit" class="btn btn-agregar"><i class="fas fa-search"></i> Filtrar</button>
                    <a href="<?= basename($_SERVER['PHP_SELF']) ?>" class="btn btn-cancelar"><i class="fas fa-rotate-left"></i> Limpiar</a>
                </div>
            </div>
        </form>

        <!-- Botones de exportación -->
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <a href="exportar_pdf_mobiliario.php?almacen=<?= urlencode($_GET['almacen'] ?? '') ?>" class="btn btn-agregar" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar a PDF
            </a>
            <a href="exportar_excel_mobiliario.php?almacen=<?= urlencode($_GET['almacen'] ?? '') ?>" class="btn btn-agregar">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
            <button type="button" id="btnPrint" class="btn btn-agregar"><i class="fas fa-print"></i> Imprimir</button>
        </div>

        <!-- Tabla -->
        <section class="tabla-ventas">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mobiliario</th>
                        <th>Stock</th>
                        <th>Almacén</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($inventario && $inventario->num_rows>0): ?>
                    <?php while($i = $inventario->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i['id_inventario_mobiliario'] ?></td>
                            <td><?= htmlspecialchars($i['nombre_mobiliario']) ?></td>
                            <td><?= $i['stock'] ?></td>
                            <td><?= htmlspecialchars($i['nombre_almacen']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">No se encontraron resultados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>
</div>

<script>
    // Imprimir tabla de inventario
    document.getElementById('btnPrint').addEventListener('click', () => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Reporte de Inventario Mobiliario</title>');
        printWindow.document.write('<link rel="stylesheet" href="../globales.css">');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h2>Reporte de Inventario de Mobiliario</h2>');
        <?php if($almacen_nombre !== "Todos los registros"): ?>
            printWindow.document.write('<p>Almacén: <?= htmlspecialchars($almacen_nombre) ?></p>');
        <?php else: ?>
            printWindow.document.write('<p>Todos los registros</p>');
        <?php endif; ?>
        printWindow.document.write(document.querySelector(".tabla-ventas").outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
</script>
</body>
</html>



