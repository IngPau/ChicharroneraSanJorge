// Compras/compras.js
// ================== Formato de moneda (GTQ) ==================
const nfGTQ = new Intl.NumberFormat('es-GT', {
  style: 'currency',
  currency: 'GTQ',
  minimumFractionDigits: 2
});
const formatGTQ  = (n) => nfGTQ.format(isNaN(n) ? 0 : Number(n));
const parseMoney = (str) => parseFloat(String(str || '').replace(/[^\d.-]/g, '')) || 0;

// ================== Estado ==================
let compraCount = 0;

// ================== Boot ==================
document.addEventListener('DOMContentLoaded', async () => {
  await addCompra();   // crea la primera compra visible
  renderResumen();     // pinta listado
});

// ================== Helpers de tabla ==================
function getCompraTable(compraEl){
  return compraEl.querySelector('table.detalle_compra');
}

// guarda plantilla de fila por tabla
function cacheRowTemplateForTable(tableEl){
  const firstRow = tableEl.querySelector('tbody tr.row-item');
  if (firstRow) {
    tableEl.dataset.rowTpl = firstRow.outerHTML;
  }
}

// crea una fila limpia desde la plantilla guardada en la tabla
function createRowFromTableTemplate(tableEl){
  const tpl = tableEl?.dataset?.rowTpl;
  if (!tpl) { console.error('Sin plantilla para esta tabla'); return null; }

  const container = document.createElement('tbody');
  container.innerHTML = tpl.trim();
  const row = container.firstElementChild;

  // limpiar/habilitar
  row.querySelectorAll('input').forEach(i => {
    i.value = '';
    i.removeAttribute('readonly');
    i.removeAttribute('disabled');
  });
  row.querySelectorAll('select').forEach(s => {
    s.selectedIndex = 0;
    s.removeAttribute('disabled');
  });

  // subtotal = 0
  const subEl = row.querySelector('input.subtotal, input[name="subtotal[]"]');
  if (subEl) subEl.value = formatGTQ(0);

  // re-engancha handlers por si la plantilla no trae (seguridad)
  const addBtn = row.querySelector('.btn-save');
  const delBtn = row.querySelector('.btn-danger');
  if (addBtn && !addBtn.onclick) addBtn.onclick = () => addRow(addBtn);
  if (delBtn && !delBtn.onclick) delBtn.onclick = () => removeRow(delBtn);

  return row;
}

// asegura que siempre quede 1 fila en esa tabla
async function ensureOneRowForTable(tableEl){
  const tbody = tableEl.querySelector('tbody');
  if (!tbody.querySelector('tr.row-item')) {
    const newRow = createRowFromTableTemplate(tableEl);
    if (!newRow) return;
    tbody.appendChild(newRow);

    // hidrata selects de la nueva fila
    if (typeof hydrateRow === 'function') {
      await hydrateRow(newRow);
    } else if (typeof hydrateRowInsumos === 'function') {
      await hydrateRowInsumos(newRow);
    }
  }
}

// ================== Crear COMPRA ==================
async function addCompra() {
  const tpl = document.getElementById('tpl-compra');
  const container = document.getElementById('compras-container');
  if (!tpl || !container) { console.error('Falta template o contenedor'); return null; }

  const frag = tpl.content.cloneNode(true);
  const compraEl = frag.firstElementChild;
  container.appendChild(compraEl);

  // cachea plantilla de fila para ESTA tabla
  const tableEl = getCompraTable(compraEl);
  cacheRowTemplateForTable(tableEl);

  // Hidratar selects (si tienes loaders)
  const selSuc  = compraEl.querySelector('.sel-sucursal');
  const selProv = compraEl.querySelector('.sel-proveedor');
  if (window.Compras?.fillSucursalSelect && selSuc)  await window.Compras.fillSucursalSelect(selSuc);
  if (window.Compras?.fillProveedorSelect && selProv) await window.Compras.fillProveedorSelect(selProv);

  // Insumos de la primera fila (si tienes loader)
  if (typeof hydrateRowInsumos === 'function') {
    const firstRow = compraEl.querySelector('tr.row-item');
    if (firstRow) await hydrateRowInsumos(firstRow);
  }

  // Delegación de cálculo en ESTA compra
  const tbody = tableEl.querySelector('tbody');
  tbody.addEventListener('input', (e) => {
    if (e.target.matches('.qty, .price, input[name="cantidad_insumo[]"], input[name="precio_unitario[]"]')) {
      const row = e.target.closest('tr');
      recalcRow(row);
      recalcCompraTotal(compraEl);
      renderResumen();
    }
  });

  // Cambios en selects refrescan resumen (y si cambias proveedor, puedes recargar insumos)
  compraEl.addEventListener('change', async (e) => {
    if (e.target.matches('.sel-sucursal, .sel-proveedor, .sel-insumo')) {
      if (e.target.matches('.sel-proveedor') && typeof hydrateRowInsumos === 'function') {
        for (const row of compraEl.querySelectorAll('tr.row-item')) {
          await hydrateRowInsumos(row);
        }
      }
      renderResumen();
    }
  });

  recalcCompraTotal(compraEl);
  compraCount++;
  renderResumen();
  return compraEl;
}

// ================== Filas dentro de UNA compra ==================
async function addRow(btn){
  const compraEl = btn.closest('.compra');
  const tableEl  = getCompraTable(compraEl);
  const tbody    = tableEl.querySelector('tbody');

  const newRow = createRowFromTableTemplate(tableEl);
  if (!newRow) return;

  tbody.appendChild(newRow);

  // hidratar insumos
  if (typeof hydrateRowInsumos === 'function') await hydrateRowInsumos(newRow);

  recalcCompraTotal(compraEl);
  renderResumen();
}

async function removeRow(btn){
  const compraEl = btn.closest('.compra');
  const tableEl  = getCompraTable(compraEl);
  const tbody    = tableEl.querySelector('tbody');
  const row      = btn.closest('tr');

  if (row) row.remove();

  // re-crear una fila limpia
  await ensureOneRowForTable(tableEl);

  recalcCompraTotal(compraEl);
  renderResumen();
}

// ================== Totales y Resumen ==================
function recalcRow(row){
  const qty   = parseFloat(row.querySelector('.qty')?.value || row.querySelector('input[name="cantidad_insumo[]"]')?.value || 0);
  const price = parseFloat(row.querySelector('.price')?.value || row.querySelector('input[name="precio_unitario[]"]')?.value || 0);
  const sub   = qty * price;
  const subEl = row.querySelector('.subtotal') || row.querySelector('input[name="subtotal[]"]');
  if (subEl) subEl.value = formatGTQ(sub);
}

function recalcCompraTotal(compraEl){
  let total = 0;
  compraEl.querySelectorAll('.subtotal, input[name="subtotal[]"]').forEach(i => total += parseMoney(i.value));
  const out = compraEl.querySelector('.total_compra');
  if (out) out.value = formatGTQ(total);
}

function getSelectedText(sel){
  if (!sel) return '';
  const opt = sel.options[sel.selectedIndex];
  return opt ? opt.text.trim() : '';
}

function rowCuenta(row){
  const ins   = row.querySelector('.sel-insumo')?.value?.trim();
  const qty   = parseFloat(row.querySelector('.qty')?.value || row.querySelector('input[name="cantidad_insumo[]"]')?.value || 0);
  const price = parseFloat(row.querySelector('.price')?.value || row.querySelector('input[name="precio_unitario[]"]')?.value || 0);
  return !!ins && (qty > 0 || price > 0);
}

function getRowSubtotal(row){
  const subEl = row.querySelector('.subtotal') || row.querySelector('input[name="subtotal[]"]');
  let sub = subEl ? parseMoney(subEl.value) : 0;
  if (!sub) {
    const qty   = parseFloat(row.querySelector('.qty')?.value || row.querySelector('input[name="cantidad_insumo[]"]')?.value || 0);
    const price = parseFloat(row.querySelector('.price')?.value || row.querySelector('input[name="precio_unitario[]"]')?.value || 0);
    sub = qty * price;
  }
  return sub;
}

function renderResumen(){
  const tbody = document.querySelector('#resumen_compras tbody');
  if (!tbody) return;
  tbody.innerHTML = '';

  const compras = document.querySelectorAll('#compras-container .compra');
  let idx = 1;

  compras.forEach(c => {
    const suc  = getSelectedText(c.querySelector('.sel-sucursal'))  || '—';
    const prv  = getSelectedText(c.querySelector('.sel-proveedor')) || '—';
    const rows = Array.from(c.querySelectorAll('tbody tr.row-item'));
    const items = rows.filter(rowCuenta).length;
    const total = rows.reduce((acc, r) => acc + getRowSubtotal(r), 0);

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td style="text-align:center;">${idx++}</td>
      <td>${suc}</td>
      <td>${prv}</td>
      <td style="text-align:center;">${items}</td>
      <td style="text-align:right;">${formatGTQ(total)}</td>
    `;
    tbody.appendChild(tr);
  });

  if (!compras.length) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td colspan="5" style="text-align:center; color:#6b7280; padding:10px;">
        Sin compras agregadas
      </td>`;
    tbody.appendChild(tr);
  }
}

// ================== Eliminar compra completa ==================
function removeCompra(btn){
  const container = document.getElementById('compras-container');
  const compraEl  = btn.closest('.compra');
  compraEl.remove();

  if (!container.querySelector('.compra')) addCompra(); // deja al menos 1 compra
  renderResumen();
}

// ================== Exponer funciones globales ==================
window.addCompra    = addCompra;
window.addRow       = addRow;
window.removeRow    = removeRow;
window.removeCompra = removeCompra;


//================== Cargar Datos ==================
// Compras/cargarProveedores.js
window.Compras = window.Compras || {};
(function(NS){
  // cache de opciones para no re-fetch en cada fila
  let proveedorOptionsHTML = null;
  const ENDPOINT = 'cargarProveedores.php?format=options'; // <-- verifica la ruta correcta

  async function fetchOptions() {
    const res = await fetch(ENDPOINT, { cache: 'no-store' });
    if (!res.ok) {
      const txt = await res.text().catch(()=> '');
      console.error('Respuesta servidor (proveedores):', txt);
      throw new Error('HTTP ' + res.status);
    }
    return await res.text();
  }

  // Rellena un <select> puntual
  NS.fillProveedorSelect = async function(selectEl){
    try {
      if (!proveedorOptionsHTML) {
        proveedorOptionsHTML = await fetchOptions();
      }
      selectEl.innerHTML = proveedorOptionsHTML;
    } catch (e) {
      console.error('Error cargando proveedores:', e);
      selectEl.innerHTML = '<option value="">Error cargando proveedores</option>';
    }
  };

  // Si existen selects ya pintados en el HTML al cargar
  document.addEventListener('DOMContentLoaded', async () => {
    const selects = document.querySelectorAll('select.sel-proveedor');
    if (selects.length) {
      // carga una vez y aplica a todos
      if (! proveedorOptionsHTML) {
        try { proveedorOptionsHTML = await fetchOptions(); } catch(e){ proveedorOptionsHTML = '<option value="">Error</option>'; }
      }
      selects.forEach(s => s.innerHTML = proveedorOptionsHTML);
    }
  });
})(window.Compras);


// Compras/cargarSucursal.js
window.Compras = window.Compras || {};
(function(NS){
  let sucursalOptionsHTML = null;
  const ENDPOINT = 'cargarSucursal.php?format=options'; // <-- verifica tu archivo PHP

  async function fetchOptions() {
    const res = await fetch(ENDPOINT, { cache: 'no-store' });
    if (!res.ok) {
      const txt = await res.text().catch(()=> '');
      console.error('Respuesta servidor (sucursales):', txt);
      throw new Error('HTTP ' + res.status);
    }
    return await res.text();
  }

  NS.fillSucursalSelect = async function(selectEl){
    try {
      if (!sucursalOptionsHTML) {
        sucursalOptionsHTML = await fetchOptions();
      }
      selectEl.innerHTML = sucursalOptionsHTML;
    } catch (e) {
      console.error('Error cargando sucursales:', e);
      selectEl.innerHTML = '<option value="">Error cargando sucursales</option>';
    }
  };

  document.addEventListener('DOMContentLoaded', async () => {
    const selects = document.querySelectorAll('select.sel-sucursal');
    if (selects.length) {
      if (!sucursalOptionsHTML) {
        try { sucursalOptionsHTML = await fetchOptions(); } catch(e){ sucursalOptionsHTML = '<option value="">Error</option>'; }
      }
      selects.forEach(s => s.innerHTML = sucursalOptionsHTML);
    }
  });
})(window.Compras);


// Compras/cargarInsumos.js
// Expone: window.hydrateRowInsumos(row)

(function(){
  // cachés: uno global y uno por proveedor
  let insumoOptionsGlobal = null;
  const insumoOptionsByProv = new Map();

  async function fetchOptionsGlobal(){
    const res = await fetch('cargarInsumos.php?format=options', { cache: 'no-store' });
    if (!res.ok) {
      const txt = await res.text().catch(()=> '');
      console.error('Respuesta servidor (insumos global):', txt);
      throw new Error('HTTP ' + res.status);
    }
    return await res.text();
  }

  async function fetchOptionsByProveedor(idProveedor){
    const url = 'cargarInsumos.php?format=options&id_proveedor=' + encodeURIComponent(idProveedor);
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) {
      const txt = await res.text().catch(()=> '');
      console.error('Respuesta servidor (insumos por proveedor):', txt);
      throw new Error('HTTP ' + res.status);
    }
    return await res.text();
  }

  // Rellena el <select class="sel-insumo"> de UNA fila (row)
  window.hydrateRowInsumos = async function(row){
    const selInsumo = row.querySelector('select.sel-insumo');
    if (!selInsumo) return;

    // si hay proveedor en ESTA fila, intenta por proveedor
    const selProv = row.querySelector('select.sel-proveedor');
    const provId  = selProv?.value?.trim();

    try {
      if (provId) {
        if (!insumoOptionsByProv.has(provId)) {
          insumoOptionsByProv.set(provId, await fetchOptionsByProveedor(provId));
        }
        selInsumo.innerHTML = insumoOptionsByProv.get(provId);
      } else {
        if (!insumoOptionsGlobal) {
          insumoOptionsGlobal = await fetchOptionsGlobal();
        }
        selInsumo.innerHTML = insumoOptionsGlobal;
      }
    } catch (e) {
      console.error('Error cargando insumos:', e);
      selInsumo.innerHTML = '<option value="">Error cargando insumos</option>';
    }
  };

  // Si hay filas ya pintadas al cargar, hidrátalas
  document.addEventListener('DOMContentLoaded', async () => {
    const rows = document.querySelectorAll('tr.row-item');
    for (const r of rows) {
      await window.hydrateRowInsumos(r);
    }

    // Al cambiar proveedor en cualquier fila, rehacer insumos SOLO de esa fila
    document.body.addEventListener('change', async (e) => {
      if (e.target.matches('select.sel-proveedor')) {
        const row = e.target.closest('tr.row-item');
        if (row) await window.hydrateRowInsumos(row);
      }
    });
  });
})();


// ================== Opcional: hydrateRow para usar con ensureOneRowForTable ==================
// Si ya tienes hydrateRow en otro archivo, ignora esto.
// async function hydrateRow(row) {
//   const provSel = row.querySelector('select.sel-proveedor');
//   if (window.Compras?.fillProveedorSelect && provSel) {
//     await window.Compras.fillProveedorSelect(provSel);
//   }
//   if (typeof hydrateRowInsumos === 'function') {
//     await hydrateRowInsumos(row);
//   }
// }
