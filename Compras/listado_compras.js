let compraCount = 0;

// === Agrega una compra ===
async function addCompra() {
  // Toma el template correctamente
  const tpl = document.getElementById('tpl-compra');
  if (!tpl) {
    console.error('No encuentro #tpl-compra');
    return null;
  }
  const fragment = tpl.content.cloneNode(true); // Clona el template
  const compraEl = fragment.firstElementChild; // Toma el elemento raíz del template

  // 2) Inserta en el contenedor correcto
  const container = document.getElementById('compras-container'); // usa el ID real
  if (!container) {
    console.error('No encuentro #compras-container');
    return null;
  }
  container.appendChild(compraEl);

  // 3) Hidrata selectores de ESTA compra
  if (typeof hydrateCompra === 'function') {
    await hydrateCompra(compraEl);
  } else {

    // solo proveedores/sucursales si están
    if (window.Compras?.fillProveedorSelect) {
      const provSel = compraEl.querySelector('.sel-proveedor');
      provSel && await window.Compras.fillProveedorSelect(provSel);
    }
    if (window.Compras?.fillSucursalSelect) {
      const sucSel = compraEl.querySelector('.sel-sucursal');
      sucSel && await window.Compras.fillSucursalSelect(sucSel);
    }
    if (typeof hydrateRowInsumos === 'function') {
      const firstRow = compraEl.querySelector('tr.row-item');
      firstRow && await hydrateRowInsumos(firstRow);
    }
  }

  // 4) Listeners de cálculo por compra 
  if (typeof wireCompraCalculations === 'function') {
    wireCompraCalculations(compraEl);
  }

  // 5) Inicializa total de ESTA compra
  if (typeof recalcCompraTotal === 'function') {
    recalcCompraTotal(compraEl);
  } else {
    // si no tienes recalcCompraTotal, al menos deja 0
    const totalInput = compraEl.querySelector('.total_compra') 
                    || compraEl.querySelector('input[name="total_compra"]');
    if (totalInput) totalInput.value = formatGTQ(0);
  }

  // 6) contador y resumen (si existe)
  compraCount++;
  if (typeof renderResumen === 'function') renderResumen();

  return compraEl;
}
