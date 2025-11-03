<?php
session_start();
// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../conexion.php';
$db = conectar();
date_default_timezone_set('America/Guatemala');

// --- FUNCION PARA FECHAS EN ESPAÑOL ---
function fecha_es($fecha) {
    $meses = [
        1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril",
        5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto",
        9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
    ];

    $partes = explode('-', $fecha); // YYYY-MM-DD
    if(count($partes) == 3){
        $anio = $partes[0];
        $mes = (int)$partes[1];
        $dia = $partes[2];
        return $dia . " de " . $meses[$mes] . " de " . $anio;
    } else {
        return $fecha; // por si no tiene formato esperado
    }
}

// --- FILTROS ---
$filtro = "1=1"; // condición base
if (!empty($_GET['fecha'])) {
    $fecha = $db->real_escape_string($_GET['fecha']);
    $filtro .= " AND v.fecha_venta = '$fecha'";
}
if (!empty($_GET['mes'])) {
    $mes = $db->real_escape_string($_GET['mes']);
    $filtro .= " AND MONTH(v.fecha_venta) = '$mes'";
}
if (!empty($_GET['sucursal'])) {
    $sucursal = $db->real_escape_string($_GET['sucursal']);
    $filtro .= " AND v.id_sucursal = '$sucursal'";
}

// --- CONSULTA PRINCIPAL ---
$ventas = $db->query("
    SELECT v.*, 
           s.nombre_sucursal, 
           u.nombre_usuario,
           c.nombre_cliente
    FROM ventas v
    INNER JOIN sucursales s ON v.id_sucursal = s.id_sucursal
    INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
    LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
    WHERE $filtro
    ORDER BY v.fecha_venta DESC
");

// --- OBTENER SUCURSALES PARA EL SELECT ---
$sucursales = $db->query("SELECT id_sucursal, nombre_sucursal FROM sucursales");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <link rel="stylesheet" href="../SideBar/sidebar.css">
    <link rel="stylesheet" href="../globales.css">
    <link rel="stylesheet" href="../ventas/ventas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
<div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
        <h1>Reporte de Ventas</h1>
        <h3>Consulta de Ventas con Filtros</h3>

        <!-- 🔍 Filtros -->
        <form method="GET" class="formulario">
            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div>
                    <label>Fecha específica:</label>
                    <input type="date" name="fecha" value="<?= $_GET['fecha'] ?? '' ?>">
                </div>
                <div>
                    <label>Mes:</label>
                    <select name="mes">
                        <option value="">-- Todos --</option>
                        <?php
                        // Meses en español
                        $meses = [
                            1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril",
                            5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto",
                            9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
                        ];

                        for ($i = 1; $i <= 12; $i++) {
                            $selected = (isset($_GET['mes']) && $_GET['mes'] == $i) ? 'selected' : '';
                            echo "<option value='$i' $selected>{$meses[$i]}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label>Sucursal:</label>
                    <select name="sucursal">
                        <option value="">-- Todas --</option>
                        <?php while ($s = $sucursales->fetch_assoc()): ?>
                            <?php $sel = (isset($_GET['sucursal']) && $_GET['sucursal'] == $s['id_sucursal']) ? 'selected' : ''; ?>
                            <option value="<?= $s['id_sucursal'] ?>" <?= $sel ?>><?= $s['nombre_sucursal'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div style="display:flex; align-items:flex-end; gap:10px;">
                    <button type="submit" class="btn btn-agregar"><i class="fas fa-search"></i> Buscar</button>
                    <a href="reporte_ventas.php" class="btn btn-cancelar"><i class="fas fa-rotate-left"></i> Limpiar</a>
                </div>
            </div>
        </form>

        <!-- 📤 Botones de exportación -->
        <div style="margin-bottom:20px; margin-top: 20px; display: flex; flex-wrap: wrap; gap: 10px;">
            <a href="exportar_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-agregar" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar a PDF
            </a>
            <a href="exportar_excel.php?<?= http_build_query($_GET) ?>" class="btn btn-agregar">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
            <button type="button" id="btnPrint" class="btn btn-agregar"><i class="fas fa-print"></i> Imprimir</button>
        </div>

        <!-- 🧾 Tabla -->
        <section class="tabla-ventas">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Total (Q)</th>
                    <th>Mesa</th>
                    <th>Usuario</th>
                    <th>Sucursal</th>
                    <th>Cliente</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($ventas->num_rows > 0): ?>
                    <?php while ($v = $ventas->fetch_assoc()): ?>
                        <tr>
                            <td><?= $v['id_venta'] ?></td>
                            <td><?= fecha_es($v['fecha_venta']) ?></td>
                            <td>Q<?= number_format($v['total_venta'], 2) ?></td>
                            <td><?= $v['id_mesa'] ?: '—' ?></td>
                            <td><?= htmlspecialchars($v['nombre_usuario']) ?></td>
                            <td><?= htmlspecialchars($v['nombre_sucursal']) ?></td>
                            <td><?= $v['nombre_cliente'] ?: '—' ?></td>
                            <td class="acciones">
                                <a href="../ventas/ventas.php?editar=<?= $v['id_venta'] ?>" class="btn btn-editar" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="../ventas/ventas_crud.php?eliminar=<?= $v['id_venta'] ?>" class="btn btn-eliminar" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>

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

        <!-- 📊 Totales -->
        <?php
        $totalGeneral = 0;
        $ventas->data_seek(0);
        while ($fila = $ventas->fetch_assoc()) {
            $totalGeneral += $fila['total_venta'];
        }
        ?>
        <div style="margin-top: 20px;">
            <h3>Total general: <strong>Q<?= number_format($totalGeneral, 2) ?></strong></h3>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 🖨️ Imprimir tabla
    document.getElementById('btnPrint').addEventListener('click', () => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Reporte de Ventas</title>');
        printWindow.document.write('<link rel="stylesheet" href="../globales.css">');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h2>Reporte de Ventas</h2>');
        printWindow.document.write(document.querySelector(".tabla-ventas").outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
</script>
</body>
</html>
