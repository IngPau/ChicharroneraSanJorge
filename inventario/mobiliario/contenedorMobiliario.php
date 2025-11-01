<div class="contenedorDetalles"></div>
<div class="formularioIngreso">
    <ion-icon name="close-circle-outline" id="iconoCerrarLista"></ion-icon>
    <form method="POST" action="ingresarMobiliario.php" class="formularioMobiliario">
        <h2>Agregar Mobiliario</h2>
        <div class="camposMobiliario">
            <label for="mobiliario">Nombre Mobiliario</label>
            <input type="text" id="mobiliario" name="mobiliario" required />
        </div>
        <div class="camposMobiliario">
            <label for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion" required />
        </div>
        <div class="camposMobiliario">
            <label for="categoria">Categoria Mobiliario</label>
            <input type="text" id="categoria" name="categoria" required />
        </div>
        <div class="camposMobiliario">
            <label for="cantidad">Cantidad</label>
            <input type="number" id="cantidad" name="cantidad" step="1" min="1" required />
        </div>
        <div class="camposMobiliario">
            <label for="sucursalSeleccionada">Sucursal</label>
            <select id="sucursalSeleccionada" name="sucursalSeleccionada" required>
                <?php include '../sucursales.php'; ?>
            </select>
        </div>
        <button type="submit" class="btnRegistrar">Registar en Inventario</button>
    </form>
</div>
<div class="actualizarInventario mobiliario">
    <ion-icon name="close-circle-outline" id="iconoCerrar"></ion-icon>
    <p id="tituloMobiliario"></p>
    <div class="datos">
        <div class="camposActualizar">
            <label for="descripcionMobiliario">Descripcion</label>
            <input type="text" id="descripcionMobiliario" name="descripcionMobiliario" />
        </div>
        <div class="camposActualizar">
            <label for="categoriaMb">Categoría</label>
            <input type="text" id="categoriaMb" name="categoriaMP" />
        </div>
        <div class="camposActualizar">
            <label for="cantidadMP">Cantidad</label>
            <input type="number" id="cantidadMP" name="cantidadMP" />
        </div>
    </div>
    <span id="textoResultado">No hay datos nuevos por actualizar.</span>
    <button id="btnActualizar">Actualizar</button>
</div>