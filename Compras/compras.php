	<!--Compras/compras.php -->
  <?php
    include '../conexion.php';    
    include '../SideBar/sidebar.php';
  ?>

	<!DOCTYPE html>
	<html lang="es">
	<head>
	  <meta charset="UTF-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <title>Dashboard Restaurante</title>

  <!-- Archivos Extras -->
	  <link rel="stylesheet" href="compras.css">
    <link rel="stylesheet" href="../SideBar/sidebar.css">
    <link rel="stylesheet" href="../globales.css">
	</head>
	<body>

<div class="main">
  <div class="page-header">
    <div>
      <div class="page-title"> Agregar Compra </div>
    </div>
      <div 
        class="inline-actions">
      </div>
  </div>

  <form action="guardar_compra.php" method="post" class="card" id="form-compra">

    <div class="section-title">Detalle de compra
    </div>

    <!-- Tabla de compras -->
<div class="table-wrap">
  <table class="table" id="detalle_compra">
    <thead>
      <tr>
        <th>Proveedor</th>
        <th>Insumo</th>
        <th style="width:120px;">Cantidad</th>
        <th style="width:160px;">Precio Unitario</th>
        <th style="width:160px;">Subtotal</th>
        <th class="actions">Acciones</th>
      </tr>
    </thead>

    <tbody>
      <tr class="row-item">
        <td>
          <select name="id_proveedor[]" class="sel-proveedor" required>
            <option value="">Seleccione un proveedor</option>
          </select>
        </td>

        <td>
          <select name="id_insumo[]" class="sel-insumo" required>
            <option value="">Seleccione un insumo</option>
          </select>
        </td>

        <td><input type="number" name="cantidad_insumo[]" class="qty" min="1" step="1" required></td>
        <td><input type="number" name="precio_unitario[]" class="price" step="0.01" min="0" required></td>
        <td><input type="text" name="subtotal[]" class="subtotal" readonly></td>

        <td class="actions">
          <button type="button" class="btn btn-edit"  onclick="editRow(this)">Editar</button>
          <button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button>
        </td>
      </tr>
    </tbody>
  </table>
</div>

    <div class="totals">
      <div class="total-box">
        <strong>Total compra</strong>
        <input type="text" name="total_compra" id="total_compra" readonly>
      </div>
      <button type="submit" class="btn btn-primary">Guardar compra</button>
      <button type="button" class="btn btn-save" onclick="addRow()">+</button>
    </div>
  </form>
</div>

<!-- Incluir Scripts -->
<script src="compras.js"></script>
<script src="editarbtn.js"></script>
<script src="cargarProveedores.js"></script>
<script src="cargarInsumos.js"></script>

</body>
</html>