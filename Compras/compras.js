// ================== Formato de moneda (GTQ) ==================
const nfGTQ = new Intl.NumberFormat('es-GT', { style: 'currency', currency: 'GTQ', minimumFractionDigits: 2 });
const formatGTQ  = (n) => nfGTQ.format(isNaN(n) ? 0 : Number(n));
const parseMoney = (str) => parseFloat(String(str || '').replace(/[^\d.-]/g, '')) || 0;

// ================== Estado ==================
let compraCount = 0;

// ================== Boot ==================
document.addEventListener('DOMContentLoaded', async () => {
  await addCompra();        // crea la primera compra visible
  renderResumen();          // pinta el resumen
  // Si tienes un botón global de guardar (por ejemplo con id="btn-guardar-todo"):
  const btnGuardarGlobal = document.getElementById('btn-guardar-todo');
  if (btnGuardarGlobal) btnGuardarGlobal.addEventListener('click', submitCompras);
});

// ================== Helpers ==================
function getCompraTable(compraEl){ return compraEl.querySelector('table.detalle_compra'); }

function cacheRowTemplateForTable(tableEl){
  const firstRow = tableEl.querySelector('tbody tr.row-item');
  if (firstRow) tableEl.dataset.rowTpl = firstRow.outerHTML;
}

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

  const subEl = row.querySelector('input.subtotal, input[name="subtotal[]"]');
  if (subEl) subEl.value = formatGTQ(0);

  // reenganchar handlers defensivamente
  const addBtn = row.querySelector('.btn-save');
  const delBtn = row.querySelector('.btn-danger');
  if (addBtn && !addBtn.onclick) addBtn.onclick = () => addRow(addBtn);
  if (delBtn && !delBtn.onclick) delBtn.onclick = () => removeRow(delBtn);

  return row;
}

async function ensureOneRowForTable(tableEl){
  const tbody = tableEl.querySelector('tbody');
  if (!tbody.querySelector('tr.row-item')) {
    const newRow = createRowFromTableTemplate(tableEl);
    if (!newRow) return;
    tbody.appendChild(newRow);
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

  const tableEl = getCompraTable(compraEl);
  cacheRowTemplateForTable(tableEl);

  // Hidratar selects de cabecera
  const selSuc  = compraEl.querySelector('.sel-sucursal');
  const selProv = compraEl.querySelector('.sel-proveedor');
  if (window.Compras?.fillSucursalSelect && selSuc)  await window.Compras.fillSucursalSelect(selSuc);
  if (window.Compras?.fillProveedorSelect && selProv) await window.Compras.fillProveedorSelect(selProv);

  // Hidratar primera fila de insumos
  if (typeof hydrateRowInsumos === 'function') {
    const firstRow = compraEl.querySelector('tr.row-item');
    if (firstRow) await hydrateRowInsumos(firstRow);
  }

  // Delegación de cálculo
  const tbody = tableEl.querySelector('tbody');
  tbody.addEventListener('input', (e) => {
    if (e.target.matches('.qty, .price, input[name="cantidad_insumo[]"], input[name="precio_unitario[]"]')) {
      const row = e.target.closest('tr');
      recalcRow(row);
      recalcCompraTotal(compraEl);
      renderResumenDebounced();
    }
  });

  // Cambios en selects de cabecera o fila
  compraEl.addEventListener('change', async (e) => {
    if (e.target.matches('.sel-sucursal, .sel-proveedor')) {
      // Si cambias proveedor de la compra, rehidrata insumos de TODAS las filas de esa compra
      if (e.target.matches('.sel-proveedor') && typeof hydrateRowInsumos === 'function') {
        for (const row of compraEl.querySelectorAll('tr.row-item')) {
          await hydrateRowInsumos(row);
        }
      }
      renderResumenDebounced();
    }
    if (e.target.matches('.sel-insumo')) {
      renderResumenDebounced();
    }
  });

  // Botón "Guardar todo" DENTRO de la compra
  const btnGuardar = compraEl.querySelector('button[type="submit"]');
  if (btnGuardar) {
    btnGuardar.addEventListener('click', async (ev) => {
      ev.preventDefault();
      await submitCompras(); // guarda todas las compras del contenedor
    });
  }

  recalcCompraTotal(compraEl);
  compraCount++;
  renderResumenDebounced();
  return compraEl;
}

// ================== Filas en UNA compra ==================
async function addRow(btn){
  const compraEl = btn.closest('.compra');
  const tableEl  = getCompraTable(compraEl);
  const tbody    = tableEl.querySelector('tbody');

  const newRow = createRowFromTableTemplate(tableEl);
  if (!newRow) return;

  tbody.appendChild(newRow);

  // ✅ Solo una hidratación
  if (typeof hydrateRowInsumos === 'function') await hydrateRowInsumos(newRow);

  recalcCompraTotal(compraEl);
  renderResumenDebounced();
}

async function removeRow(btn){
  const compraEl = btn.closest('.compra');
  const tableEl  = getCompraTable(compraEl);
  const row      = btn.closest('tr');

  if (row) row.remove();
  await ensureOneRowForTable(tableEl);

  recalcCompraTotal(compraEl);
  renderResumenDebounced();
}

// ================== Totales y Resumen ==================
function recalcRow(row){
  const qtyIn  = row.querySelector('.qty') || row.querySelector('input[name="cantidad_insumo[]"]');
  const prcIn  = row.querySelector('.price') || row.querySelector('input[name="precio_unitario[]"]');
  const subOut = row.querySelector('.subtotal') || row.querySelector('input[name="subtotal[]"]');
  const qty   = parseFloat(qtyIn?.value || 0);
  const price = parseFloat(prcIn?.value || 0);
  const sub   = qty * price;
  if (subOut) subOut.value = formatGTQ(sub);
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
  const insSel = row.querySelector('.sel-insumo');
  const ins   = insSel ? insSel.value : '';
  const qtyIn = row.querySelector('.qty') || row.querySelector('input[name="cantidad_insumo[]"]');
  const prcIn = row.querySelector('.price') || row.querySelector('input[name="precio_unitario[]"]');
  const qty   = parseFloat(qtyIn?.value || 0);
  const price = parseFloat(prcIn?.value || 0);
  return !!ins && (qty > 0 || price > 0);
}

function getRowSubtotal(row){
  const subEl = row.querySelector('.subtotal') || row.querySelector('input[name="subtotal[]"]');
  let sub = subEl ? parseMoney(subEl.value) : 0;
  if (!sub) {
    const qtyIn = row.querySelector('.qty') || row.querySelector('input[name="cantidad_insumo[]"]');
    const prcIn = row.querySelector('.price') || row.querySelector('input[name="precio_unitario[]"]');
    sub = (parseFloat(qtyIn?.value || 0) * parseFloat(prcIn?.value || 0)) || 0;
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

let rafId = null;
function renderResumenDebounced(){
  if (rafId) cancelAnimationFrame(rafId);
  rafId = requestAnimationFrame(() => {
    renderResumen();
    rafId = null;
  });
}

// ================== Eliminar compra completa ==================
function removeCompra(btn){
  const container = document.getElementById('compras-container');
  const compraEl  = btn.closest('.compra');
  compraEl.remove();
  if (!container.querySelector('.compra')) addCompra(); // deja al menos 1
  renderResumenDebounced();
}

// ================== Payload y envío ==================
function buildComprasPayload(){
  const comprasEls = [...document.querySelectorAll('#compras-container .compra')];

  const compras = comprasEls.map(compraEl => {
    const id_sucursal  = parseInt(compraEl.querySelector('.sel-sucursal')?.value || 0);
    const id_proveedor = parseInt(compraEl.querySelector('.sel-proveedor')?.value || 0);
    const fechaInput   = compraEl.querySelector('.fecha')?.value;
    const fecha_compra = fechaInput || new Date().toISOString().slice(0,10);

    const detalles = [...compraEl.querySelectorAll('tbody tr.row-item')].map(row => {
      const id_insumo = parseInt(row.querySelector('.sel-insumo')?.value || 0);
      const qtyIn = row.querySelector('.qty') || row.querySelector('input[name="cantidad_insumo[]"]');
      const prcIn = row.querySelector('.price') || row.querySelector('input[name="precio_unitario[]"]');
      const cantidad_insumo = parseFloat(qtyIn?.value || 0);
      const precio_unitario = parseFloat(prcIn?.value || 0);
      return { id_insumo, cantidad_insumo, precio_unitario };
    }).filter(d => d.id_insumo && d.cantidad_insumo > 0);

    return { id_sucursal, id_proveedor, fecha_compra, detalles };
  }).filter(c => c.id_sucursal && c.id_proveedor && c.detalles.length);

  return compras;
}

async function submitCompras(){
  const compras = buildComprasPayload();
  if (!compras.length) {
    alert('No hay compras válidas para guardar.');
    return;
  }
  try {
    const res = await fetch('../api/compras/batch_create.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ compras })
    });
    const json = await res.json();
    if (!res.ok) throw new Error(json?.error || ('HTTP ' + res.status));
    alert('Compras guardadas: ' + (json.ids?.join(', ') || 'OK'));
    // reset UI
    document.getElementById('compras-container').innerHTML = '';
    await addCompra();
  } catch (e) {
    console.error(e);
    alert('Error guardando compras: ' + e.message);
  }
}

// ================== Exponer funciones globales ==================
window.addCompra    = addCompra;
window.addRow       = addRow;
window.removeRow    = removeRow;
window.removeCompra = removeCompra;

// ================== Cargar catálogos ==================
window.Compras = window.Compras || {};
(function(NS){
  let proveedorOptionsHTML = null;
  const ENDPOINT = 'cargarProveedores.php?format=options';

  async function fetchOptions() {
    const res = await fetch(ENDPOINT, { cache: 'no-store' });
    if (!res.ok) throw new Error('HTTP ' + res.status + ' proveedores');
    return await res.text();
  }

  NS.fillProveedorSelect = async function(selectEl){
    try {
      if (!proveedorOptionsHTML) proveedorOptionsHTML = await fetchOptions();
      selectEl.innerHTML = proveedorOptionsHTML;
    } catch (e) {
      console.error('Error cargando proveedores:', e);
      selectEl.innerHTML = '<option value="">Error cargando proveedores</option>';
    }
  };

  document.addEventListener('DOMContentLoaded', async () => {
    const selects = document.querySelectorAll('select.sel-proveedor');
    if (selects.length) {
      if (! proveedorOptionsHTML) {
        try { proveedorOptionsHTML = await fetchOptions(); }
        catch(e){ proveedorOptionsHTML = '<option value="">Error</option>'; }
      }
      selects.forEach(s => s.innerHTML = proveedorOptionsHTML);
    }
  });
})(window.Compras);

window.Compras = window.Compras || {};
(function(NS){
  let sucursalOptionsHTML = null;
  const ENDPOINT = 'cargarSucursal.php?format=options';

  async function fetchOptions() {
    const res = await fetch(ENDPOINT, { cache: 'no-store' });
    if (!res.ok) throw new Error('HTTP ' + res.status + ' sucursales');
    return await res.text();
  }

  NS.fillSucursalSelect = async function(selectEl){
    try {
      if (!sucursalOptionsHTML) sucursalOptionsHTML = await fetchOptions();
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
        try { sucursalOptionsHTML = await fetchOptions(); }
        catch(e){ sucursalOptionsHTML = '<option value="">Error</option>'; }
      }
      selects.forEach(s => s.innerHTML = sucursalOptionsHTML);
    }
  });
})(window.Compras);

// ================== Insumos (con proveedor por COMPRA) ==================
(function(){
  let insumoOptionsGlobal = null;
  const insumoOptionsByProv = new Map();

  async function fetchOptionsGlobal(){
    const res = await fetch('cargarInsumos.php?format=options', { cache: 'no-store' });
    if (!res.ok) throw new Error('HTTP ' + res.status + ' insumos global');
    return await res.text();
  }

  async function fetchOptionsByProveedor(idProveedor){
    const url = 'cargarInsumos.php?format=options&id_proveedor=' + encodeURIComponent(idProveedor);
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) throw new Error('HTTP ' + res.status + ' insumos por proveedor');
    return await res.text();
  }

  // Rellena el <select.sel-insumo> de UNA fila según el proveedor de la COMPRA
  window.hydrateRowInsumos = async function(row){
    const selInsumo = row.querySelector('select.sel-insumo');
    if (!selInsumo) return;

    // ⬇️ proveedor está en la CABECERA de la compra
    const compraEl = row.closest('.compra');
    const selProvCompra = compraEl?.querySelector('select.sel-proveedor');
    const provId  = selProvCompra?.value?.trim();

    try {
      if (provId) {
        if (!insumoOptionsByProv.has(provId)) {
          insumoOptionsByProv.set(provId, await fetchOptionsByProveedor(provId));
        }
        selInsumo.innerHTML = insumoOptionsByProv.get(provId);
      } else {
        if (!insumoOptionsGlobal) insumoOptionsGlobal = await fetchOptionsGlobal();
        selInsumo.innerHTML = insumoOptionsGlobal;
      }
    } catch (e) {
      console.error('Error cargando insumos:', e);
      selInsumo.innerHTML = '<option value="">Error cargando insumos</option>';
    }
  };

  // Hidratar filas ya pintadas
  document.addEventListener('DOMContentLoaded', async () => {
    const rows = document.querySelectorAll('tr.row-item');
    for (const r of rows) await window.hydrateRowInsumos(r);

    // Si cambias el proveedor de una COMPRA, rehidrata todas sus filas (esto también se hace en addCompra)
    document.body.addEventListener('change', async (e) => {
      if (e.target.matches('select.sel-proveedor')) {
        const compra = e.target.closest('.compra');
        if (compra) {
          const rows = compra.querySelectorAll('tr.row-item');
          for (const r of rows) await window.hydrateRowInsumos(r);
        }
      }
    });
  });
})();

// ================== Hidratar fila completa (si la plantilla trae combos de cabecera en fila) ==================
async function hydrateRow(row) {
  const sucSel = row.querySelector('select.sel-sucursal');
  if (window.Compras?.fillSucursalSelect && sucSel) {
    try { await window.Compras.fillSucursalSelect(sucSel); } catch (e) { console.error(e); }
  }
  const provSel = row.querySelector('select.sel-proveedor');
  if (window.Compras?.fillProveedorSelect && provSel) {
    try { await window.Compras.fillProveedorSelect(provSel); } catch (e) { console.error(e); }
  }
  if (typeof hydrateRowInsumos === 'function') {
    try { await hydrateRowInsumos(row); } catch (e) { console.error(e); }
  }
  return row;
}
