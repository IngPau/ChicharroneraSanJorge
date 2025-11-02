<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
$db = conectar();

// --- FILTRO por dirección (opcional) ---
$filtro = "1=1";
$direccion_filtro = "";
if (!empty($_GET['direccion'])) {
    $direccion_filtro = $db->real_escape_string($_GET['direccion']);
    $filtro .= " AND direccion_cliente LIKE '%$direccion_filtro%'";
}

// --- Consulta de clientes ---
$clientes = $db->query("
    SELECT id_cliente, nombre_cliente, apellido_cliente, dpi_cliente, telefono_cliente, direccion_cliente, correo_cliente
    FROM clientes
    WHERE $filtro
    ORDER BY nombre_cliente ASC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Clientes</title>
    <link rel="stylesheet" href="../SideBar/sidebar.css">
    <link rel="stylesheet" href="../globales.css">
    <link rel="stylesheet" href="../ventas/ventas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />
</head>
<body>
<div class="container">
    <?php include '../SideBar/sidebar.php'; ?>
    <main class="main">
        <h1>Listado de Clientes</h1>

        <!-- Filtro -->
        <form method="GET" class="formulario">
            <div style="display:flex; gap:15px;">
                <div>
                    <label>Dirección:</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($_GET['direccion'] ?? '') ?>" placeholder="Ingrese dirección">
                </div>
                <div style="display:flex; align-items:flex-end; gap:10px;">
                    <button type="submit" class="btn btn-agregar"><i class="fas fa-search"></i> Filtrar</button>
                    <a href="<?= basename($_SERVER['PHP_SELF']) ?>" class="btn btn-cancelar"><i class="fas fa-rotate-left"></i> Limpiar</a>
                </div>
            </div>
        </form>

        <!-- Botones de exportación -->
        <div style="margin-bottom: 20px; margin-top: 20px; display: flex; gap: 10px;">
            <a href="exportar_pdf_clientes.php?direccion=<?= urlencode($_GET['direccion'] ?? '') ?>" class="btn btn-agregar" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar a PDF
            </a>
            <a href="exportar_excel_clientes.php?direccion=<?= urlencode($_GET['direccion'] ?? '') ?>" class="btn btn-agregar">
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
                        <th>Apellido</th>
                        <th>DPI</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($clientes && $clientes->num_rows > 0): ?>
                    <?php while($c = $clientes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $c['id_cliente'] ?></td>
                            <td><?= htmlspecialchars($c['nombre_cliente']) ?></td>
                            <td><?= htmlspecialchars($c['apellido_cliente']) ?></td>
                            <td><?= htmlspecialchars($c['dpi_cliente']) ?></td>
                            <td><?= htmlspecialchars($c['telefono_cliente']) ?></td>
                            <td><?= htmlspecialchars($c['direccion_cliente']) ?></td>
                            <td><?= htmlspecialchars($c['correo_cliente']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No se encontraron resultados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>
</div>

<script>
    // Imprimir tabla de clientes
    document.getElementById('btnPrint').addEventListener('click', () => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Listado de Clientes</title>');
        printWindow.document.write('<link rel="stylesheet" href="../globales.css">');
        printWindow.document.write('</head><body>');
        let encabezado = '<h2>Listado de Clientes</h2>';
        <?php if(!empty($direccion_filtro)): ?>
            encabezado += '<p>Dirección: <?= htmlspecialchars($direccion_filtro) ?></p>';
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



