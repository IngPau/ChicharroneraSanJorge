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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Proveedores</title>
    <link rel="stylesheet" href="../SideBar/sidebar.css">
    <link rel="stylesheet" href="../globales.css">
    <link rel="stylesheet" href="../ventas/ventas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />
</head>
<body>
<div class="container">
    <?php include '../SideBar/sidebar.php'; ?>
    <main class="main">
        <h1>Listado de Proveedores</h1>

        <!-- Filtro -->
        <form method="GET" class="formulario">
            <div style="display:flex; gap:15px;">
                <div>
                    <label>Nombre o Dirección:</label>
                    <input type="text" name="busqueda" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>" placeholder="Ingrese nombre o dirección">
                </div>
                <div style="display:flex; align-items:flex-end; gap:10px;">
                    <button type="submit" class="btn btn-agregar"><i class="fas fa-search"></i> Filtrar</button>
                    <a href="<?= basename($_SERVER['PHP_SELF']) ?>" class="btn btn-cancelar"><i class="fas fa-rotate-left"></i> Limpiar</a>
                </div>
            </div>
        </form>

        <!-- Botones de exportación -->
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <a href="exportar_pdf_proveedores.php?busqueda=<?= urlencode($_GET['busqueda'] ?? '') ?>" class="btn btn-agregar" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar a PDF
            </a>
            <a href="exportar_excel_proveedores.php?busqueda=<?= urlencode($_GET['busqueda'] ?? '') ?>" class="btn btn-agregar">
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
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Dirección</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($proveedores && $proveedores->num_rows > 0): ?>
                    <?php while($p = $proveedores->fetch_assoc()): ?>
                        <tr>
                            <td><?= $p['id_proveedor'] ?></td>
                            <td><?= htmlspecialchars($p['nombre_proveedor']) ?></td>
                            <td><?= htmlspecialchars($p['telefono_proveedor']) ?></td>
                            <td><?= htmlspecialchars($p['correo_proveedor']) ?></td>
                            <td><?= htmlspecialchars($p['direccion_proveedor']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No se encontraron resultados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>
</div>

<script>
    // Imprimir tabla de proveedores
    document.getElementById('btnPrint').addEventListener('click', () => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Listado de Proveedores</title>');
        printWindow.document.write('<link rel="stylesheet" href="../globales.css">');
        printWindow.document.write('</head><body>');
        let encabezado = '<h2>Listado de Proveedores</h2>';
        <?php if(!empty($busqueda_filtro)): ?>
            encabezado += '<p>Búsqueda: <?= htmlspecialchars($busqueda_filtro) ?></p>';
        <?php endif; ?>
        printWindow.document.write(encabezado);
        printWindow.document.write(document.querySelector(".tabla-ventas").outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
</script>
</body>
</html>
