<?php
  include '../conexion.php';
  include '../SideBar/sidebar.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Compras</title>
  <link rel="stylesheet" href="compras.css">
  <link rel="stylesheet" href="../SideBar/sidebar.css">
  <link rel="stylesheet" href="../globales.css">
</head>
<body>

<div class="main">
  <div class="page-header">
    <div><div class="page-title">Agregar Compras</div></div>
  </div>

  <form action="guardar_compra.php" method="post" class="card" id="form-compra">
  <!-- Donde se insertan las compras -->
    <div id="compras-container">
    </div>
  </form>
</div>

<!-- ====== TEMPLATE LIMPIO ====== -->
<template id="tpl-compra">
  <div class="compra card" style="margin:18px 0; padding:12px;">
    <div class="section-title">Nueva compra</div>

    <div class="table-wrap">
      <table class="table detalle_compra">
        <thead>
          <tr>
            <th>Sucursal</th>
            <th>Proveedor</th>
            <th>Insumo</th>
            <th style="width:120px;">Cantidad</th>
            <th style="width:160px;">Precio Unitario</th>
            <th style="width:160px;">Subtotal</th>
            <th class="actions"></th>
          </tr>
        </thead>
        <tbody>
          <tr class="row-item">
            <td>
              <select name="id_sucursal[]" class="sel-sucursal" required>
                <option value="">Seleccione una sucursal</option>
              </select>
            </td>
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
              <button type="button" class="btn btn-save" onclick="addRow(this)">Agregar</button>
              <button type="button" class="btn btn-danger" onclick="removeRow(this)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
      
      <div class="totals">
        <strong>Total compra</strong>
        <input type="text" class="total_compra" readonly>

        <button type="button" class="btn btn-danger" onclick="removeCompra(this)">Eliminar todo</button>
        <button type="submit" class="btn btn-primary">Guardar todo</button>
      </div>
    </div>
  </div>
</template>

<!-- Tjavascript-->
<script src="cargarProveedores.js"></script>
<script src="cargarInsumos.js"></script>
<script src="cargarSucursal.js"></script>
<script src="compras.js"></script>

</body>
</html>
