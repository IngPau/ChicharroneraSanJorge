<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo Ventas</title>

  <!-- Estilos globales, del sidebar y ventas -->
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../ventas/ventas.css">
  <link rel="stylesheet" href="../globales.css">

  <!-- Iconos Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-papNM2Y2...==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <div class="container">
    <?php include '../SideBar/sidebar.php'; ?>

    <main class="main">
      <h1>Módulo Ventas</h1>
      <h3>Panel de administración</h3>

      <section class="resumen-ventas">
        <div class="div3"><p>Ventas Hoy</p></div>
        <div class="div4"><p>Órdenes Hoy</p></div>
        <div class="div5"><p>Método de pago más usado</p></div>
        <div class="div6">6</div>
      </section>

      <section class="tabla-ventas">
        <table>
          <thead>
            <tr>
              <th>ID VENTA</th>
              <th>CLIENTE</th>
              <th>TOTAL</th>
              <th>ITEMS</th>
              <th>FECHA</th>
              <th>ESTADO</th>
              <th>MÉTODO PAGO</th>
              <th>ACCIONES</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>V001</td>
              <td>Juan Pérez</td>
              <td>$45.50</td>
              <td>3</td>
              <td>2025-01-25</td>
              <td><span class="estado-completada">Completada</span></td>
              <td>Efectivo</td>
              <td class="acciones">
                <i class="fas fa-eye" title="Ver"></i>
                <i class="fas fa-edit" title="Editar"></i>
                <i class="fas fa-trash" title="Eliminar"></i>
              </td>
            </tr>
            <tr>
              <td>V002</td>
              <td>María García</td>
              <td>$67.25</td>
              <td>5</td>
              <td>2025-01-25</td>
              <td><span class="estado-completada">Completada</span></td>
              <td>Tarjeta</td>
              <td class="acciones">
                <i class="fas fa-eye" title="Ver"></i>
                <i class="fas fa-edit" title="Editar"></i>
                <i class="fas fa-trash" title="Eliminar"></i>
              </td>
            </tr>
            <tr>
              <td>V003</td>
              <td>Carlos López</td>
              <td>$32.00</td>
              <td>2</td>
              <td>2025-01-25</td>
              <td><span class="estado-pendiente">Pendiente</span></td>
              <td>Transferencia</td>
              <td class="acciones">
                <i class="fas fa-eye" title="Ver"></i>
                <i class="fas fa-edit" title="Editar"></i>
                <i class="fas fa-trash" title="Eliminar"></i>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>
