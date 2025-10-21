// Compras/compras.js

// ========== Formato de moneda (GTQ) ==========
const formatoQuetzal = new Intl.NumberFormat('es-GT', {
  style: 'currency',
  currency: 'GTQ',
  minimumFractionDigits: 2
});
const formatGTQ = (n) => formatoQuetzal.format(isNaN(n) ? 0 : Number(n));
const parseMoney = (str) => parseFloat(String(str).replace(/[^\d.-]/g, '')) || 0;

// ========== Plantilla de fila ==========
let ROW_TEMPLATE_HTML = '';   // plantilla HTML de una fila

// ========== Utilidades botones ==========
function setActionLabels(scope = document) {
  scope.querySelectorAll('.btn-edit').forEach(b => { b.textContent = 'Editar';  b.title = 'Editar'; });
  scope.querySelectorAll('.btn-save').forEach(b => { b.textContent = '+'; b.title = '+'; });
  scope.querySelectorAll('.btn-danger').forEach(b => { b.textContent = 'Eliminar'; b.title = 'Eliminar'; });
}

// ========== Cálculo subtotal / total ==========
function calculateSubtotalFromRow(row) {
  const cantidad = parseFloat(row.querySelector('input[name="cantidad_insumo[]"]').value) || 0;
  const precio   = parseFloat(row.querySelector('input[name="precio_unitario[]"]').value) || 0;
  const subtotal = cantidad * precio;
  const subtotalInput = row.querySelector('input[name="subtotal[]"]');
  if (subtotalInput) subtotalInput.value = formatGTQ(subtotal);
}

function calculateSubtotal(event) {
  const row = event.target.closest('tr');
  calculateSubtotalFromRow(row);
  calculateTotal();
}

function calculateTotal() {
  const tbody = document.querySelector('#detalle_compra tbody');
  let total = 0;
  tbody.querySelectorAll('input[name="subtotal[]"]').forEach(i => {
    total += parseMoney(i.value);
  });
  document.getElementById('total_compra').value = formatGTQ(total);
}

// ========== Crear fila desde plantilla ==========
function createRowFromTemplate() {
  const container = document.createElement('tbody');
  container.innerHTML = ROW_TEMPLATE_HTML.trim();
  const row = container.firstElementChild;

  // limpiar/habilitar campos
  row.querySelectorAll('input').forEach(i => {
    i.value = '';
    i.removeAttribute('readonly');
    i.removeAttribute('disabled');
  });
  row.querySelectorAll('select').forEach(s => {
    s.selectedIndex = 0;
    s.removeAttribute('disabled');
  });

  // subtotal a 0
  const subEl = row.querySelector('input[name="subtotal[]"]');
  if (subEl) subEl.value = formatGTQ(0);

  // asegurar textos de botones
  setActionLabels(row);

  // asegurar handlers si la fila plantilla no trae onclick por alguna razón
  const editBtn = row.querySelector('.btn-edit');
  if (editBtn && !editBtn.onclick) editBtn.onclick = function(){ editRow(this); };
  const removeBtn = row.querySelector('.btn-danger');
  if (removeBtn && !removeBtn.onclick) removeBtn.onclick = function(){ removeRow(this); };

  return row;
}

// ========== Hidratar combos de la fila ==========
async function hydrateRow(row) {
  // Proveedores (si usas el caché)
  const provSel = row.querySelector('select.sel-proveedor');
  if (window.Compras?.fillProveedorSelect && provSel) {
    await window.Compras.fillProveedorSelect(provSel);
  }
  // Insumos por fila (si aplica)
  if (typeof hydrateRowInsumos === 'function') {
    await hydrateRowInsumos(row);
  }
}

// ========== Añadir fila  ==========
window.addRow = async function addRow() {
  const tbody = document.querySelector('#detalle_compra tbody');

  // Si no hay filas, crea desde plantilla guardada
  if (!tbody.rows.length) {
    if (!ROW_TEMPLATE_HTML) {
      console.error('No hay plantilla de fila disponible.');
      return;
    }
    const row = createRowFromTemplate();
    tbody.appendChild(row);
    await hydrateRow(row);
    calculateTotal();
    return;
  }

  // Si hay filas, clona la primera
  const tpl = tbody.querySelector('tr');
  const clone = tpl.cloneNode(true);

  // limpiar/habilitar
  clone.querySelectorAll('input').forEach(i => {
    i.value = '';
    i.removeAttribute('readonly');
    i.removeAttribute('disabled');
  });
  clone.querySelectorAll('select').forEach(s => {
    s.selectedIndex = 0;
    s.removeAttribute('disabled');
  });

  // subtotal 0
  const subEl = clone.querySelector('input[name="subtotal[]"]');
  if (subEl) subEl.value = formatGTQ(0);

  // textos de botones y handlers
  setActionLabels(clone);
  const editBtn = clone.querySelector('.btn-edit');
  if (editBtn) editBtn.onclick = function(){ editRow(this); };
  const removeBtn = clone.querySelector('.btn-danger');
  if (removeBtn) removeBtn.onclick = function(){ removeRow(this); };
  const saveBtn = clone.querySelector('.btn-save');
  if (saveBtn) saveBtn.onclick = function(){ saveRow(this); };

  tbody.appendChild(clone);
  await hydrateRow(clone);
  calculateTotal();
};


// --- Detecta si la fila está completa ---
function isRowComplete(row){
  const prov  = row.querySelector('.sel-proveedor')?.value?.trim();
  const ins   = row.querySelector('.sel-insumo')?.value?.trim();
  const qty   = parseFloat(row.querySelector('input[name="cantidad_insumo[]"]')?.value || 0);
  const price = parseFloat(row.querySelector('input[name="precio_unitario[]"]')?.value || 0);

  // exige precio > 0; cambia a >=0 si aceptas gratuito
  return !!prov && !!ins && qty > 0 && price > 0;
}

// --- Crea SIEMPRE desde la plantilla guardada ---
function createRowFromTemplate() {
  if (!ROW_TEMPLATE_HTML) {
    console.error('No hay plantilla de fila disponible.');
    return null;
  }
  const container = document.createElement('tbody');
  container.innerHTML = ROW_TEMPLATE_HTML.trim();
  const row = container.firstElementChild;

  // Limpieza total
  row.querySelectorAll('input').forEach(i => {
    i.value = '';
    i.removeAttribute('readonly');
    i.removeAttribute('disabled');
  });
  row.querySelectorAll('select').forEach(s => {
    s.selectedIndex = 0;
    s.removeAttribute('disabled');
  });

  // Subtotal = 0
  const subEl = row.querySelector('input[name="subtotal[]"]');
  if (subEl) subEl.value = formatGTQ(0);

  // 🔑 MUY IMPORTANTE: no heredar marcas de autospawn
  row.removeAttribute('data-spawned-next');
  delete row.dataset.spawnedNext;

  // Etiquetas/handlers por si la plantilla no trae
  setActionLabels(row);
  const editBtn   = row.querySelector('.btn-edit');
  const removeBtn = row.querySelector('.btn-danger');
  const saveBtn   = row.querySelector('.btn-save');
  if (editBtn   && !editBtn.onclick)   editBtn.onclick   = function(){ editRow(this); };
  if (removeBtn && !removeBtn.onclick) removeBtn.onclick = function(){ removeRow(this); };
  if (saveBtn   && !saveBtn.onclick)   saveBtn.onclick   = function(){ saveRow(this); };

  return row;
}

// --- Añade una fila (SIEMPRE desde plantilla) y la hidrata ---
window.addRow = async function addRow() {
  const tbody = document.querySelector('#detalle_compra tbody');
  const row = createRowFromTemplate();
  if (!row) return null;
  tbody.appendChild(row);

  // Hidratar selects (proveedor/insumo) si tus loaders existen
  if (typeof hydrateRow === 'function') {
    await hydrateRow(row);
  } else {
    // fallback mínimo: solo proveedores si está disponible
    const provSel = row.querySelector('select.sel-proveedor');
    if (window.Compras?.fillProveedorSelect && provSel) {
      await window.Compras.fillProveedorSelect(provSel);
    }
    if (typeof hydrateRowInsumos === 'function') {
      await hydrateRowInsumos(row);
    }
  }

  calculateTotal();
  return row;
};

// --- Auto-crear la siguiente cuando la última queda completa ---
async function maybeAutoAddRow(row){
  const tbody = document.querySelector('#detalle_compra tbody');
  if (tbody.lastElementChild !== row) return;        // solo la última
  if (!isRowComplete(row)) return;                   // solo si completa
  if (row.dataset.spawnedNext === '1') return;       // ya lo hizo

  row.dataset.spawnedNext = '1';                     // marcar SOLO esta
  const nueva = await addRow();                      // crear siguiente
  // Enfocar primer control útil
  (nueva?.querySelector('.sel-proveedor')
   || nueva?.querySelector('.sel-insumo')
   || nueva?.querySelector('input, select'))?.focus();
}





// ========== Eliminar fila==========
function removeRow(button) {
  const tbody = document.querySelector('#detalle_compra tbody');
  const row = button.closest('tr');
  if (row) row.remove();

  // Si quedó vacío, crea una fila nueva desde plantilla
  if (tbody.rows.length === 0) {
    const newRow = createRowFromTemplate();
    tbody.appendChild(newRow);
    hydrateRow(newRow); // no bloqueante
  }

  calculateTotal();
}

// ========== Inicializar ==========
document.addEventListener('DOMContentLoaded', function () {
  const tbody = document.querySelector('#detalle_compra tbody');

  // Guardar plantilla al inicio
  const firstRow = tbody.querySelector('tr');
  if (firstRow) ROW_TEMPLATE_HTML = firstRow.outerHTML;

// sigue calculando en vivo
tbody.addEventListener('input', function (e) {
  if (e.target.matches('input[name="cantidad_insumo[]"], input[name="precio_unitario[]"], .qty, .price')) {
    calculateSubtotal(e);
  }
});

// 🔑 auto-agregar SOLO al confirmar precio (blur/change)
tbody.addEventListener('change', function (e){
  if (e.target.matches('input[name="precio_unitario[]"]')) {
    const row = e.target.closest('tr');
    maybeAutoAddRow(row);
  }
});

// 🔑 y también si presionas Enter dentro de precio
tbody.addEventListener('keydown', function (e){
  if (e.key === 'Enter' && e.target.matches('input[name="precio_unitario[]"]')) {
    e.preventDefault(); // evita submit
    const row = e.target.closest('tr');
    maybeAutoAddRow(row);
  }
});

  // Inicializa subtotal/total
  const firstSubtotal = document.querySelector('input[name="subtotal[]"]');
  if (firstSubtotal && !firstSubtotal.value) firstSubtotal.value = formatGTQ(0);
  calculateTotal();
});
