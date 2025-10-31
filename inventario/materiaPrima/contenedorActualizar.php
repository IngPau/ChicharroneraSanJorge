<div class="contenedorDetalles"></div>
<div class="formularioIngreso">
    <ion-icon name="close-circle-outline" id="iconoCerrar"></ion-icon>
    <form method="POST" action="ingresarInventarioMP.php" class="formularioMobiliario">
        <h2>Añadir Materia Prima</h2>
        <div class="camposMobiliario">
            <label for="MateriaP">Materia Prima</label>
            <input type="text" id="MateriaP" name="MateriaP" required />
        </div>
        <div class="camposMobiliario">
            <label for="stock">Cantidad</label>
            <input type="number" id="stock" name="stock" step="0.001" min="1" required />
        </div>
        <div class="camposMobiliario">
            <label for="uMedida">Unidad de Medida</label>
            <input type="text" id="uMedida" name="uMedida" required />
        </div>
        <div class="camposMobiliario">
            <label for="stockMinimo">Stock Minimo</label>
            <input type="number" id="stockMinimo" name="stockMinimo" step="1" min="1" required />
        </div>
        <div class="camposMobiliario">
            <label for="sucursalSeleccionada">Sucursal</label>
            <select id="sucursalSeleccionada" name="sucursalSeleccionada" required>
                <option value="Perisur">Perisur</option>
                <option value="Zona 8 Ciudad">Zona 8 Ciudad</option>
                <option value="San Cristobal">San Cristobal</option>
                <option value="Monserrat">Monserrat</option>
            </select>
        </div>
        <button type="submit" class="btnRegistrar">Registar en Inventario</button>
    </form>
</div>
<div class="actualizarInventario">
    <ion-icon name="close-circle-outline" id="iconoCerrarLista"></ion-icon>
    <p id="materiaPrima"></p>
    <div class="datos">
        <div class="camposActualizar">
            <label for="cantidadMP">Cantidad</label>
            <input type="number" id="cantidadMP" name="cantidadMP" step="0.001" min="0" />
        </div>
        <div class="camposActualizar">
            <label for="unidadMedida">Unidad de Medida</label>
            <input type="text" id="unidadMedida" name="unidadMedida" />
        </div>
        <div class="camposActualizar">
            <label for="cantidadMinima">Stock Minimo</label>
            <input type="number" id="cantidadMinima" name="cantidadMinima" step="1" min="1" />
        </div>
    </div>
    <span id="textoResultado">No hay datos nuevos por actualizar.</span>
    <button id="btnActualizar">Actualizar</button>
</div>
