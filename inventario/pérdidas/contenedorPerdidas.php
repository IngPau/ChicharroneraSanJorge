<div class="contenedorDetalles"></div>
<div class="formularioIngreso">
    <ion-icon name="close-circle-outline" id="iconoCerrarLista"></ion-icon>
    <form method="POST" action="ingresarPerdida.php" class="formularioMobiliario">
        <h2>Pérdida de Inventario</h2>
        <div class="camposMobiliario">
            <label for="materiaPrima">Materia Prima</label>
            <select name="materiaPrima" id="materiaPrima" required>
            </select>
        </div>
        <div class="camposMobiliario">
            <label for="motivo">Motivo</label>
            <input type="text" id="motivo" name="motivo" required />
        </div>
        <div class="camposMobiliario">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" required />
        </div>
        <div class="camposMobiliario">
            <label for="cantidad">Cantidad</label>
            <input type="number" id="cantidad" name="cantidad" step="0.001" min="1" required />
        </div>
        <div class="camposMobiliario">
            <label for="sucursalSeleccionada">Sucursal</label>
            <input type="text" id="sucursalSeleccionada" name="sucursalSeleccionada" >
        </div>
        <button type="submit" class="btnRegistrar">Registar Pérdida</button>
    </form>
</div>
<div class="actualizarInventario perdida">
    <ion-icon name="close-circle-outline" id="iconoCerrar"></ion-icon>
    <p id="tituloPerdida"></p>
    <div class="datos">
        <div class="camposActualizar">
            <label for="motivoPerdida">Motivo</label>
            <input type="text" id="motivoPerdida" name="motivoPerdida" />
        </div>
        <div class="camposActualizar">
            <label for="cantidadMP">Cantidad</label>
            <input type="number" id="cantidadMP" name="cantidadMP" step="0.001" min="1"/>
        </div>
        <div class="camposActualizar">
            <label for="fechaPerdida">Fecha</label>
            <input type="date" id="fechaPerdida" name="fechaPerdida" />
        </div>
    </div>
    <span id="textoResultado">No hay datos nuevos por actualizar.</span>
    <button id="btnActualizar">Actualizar</button>
</div>