// Compras/compras.js
// ========== Formato de moneda (GTQ) ==========
const formatoQuetzal = new Intl.NumberFormat('es-GT', {
  style: 'currency',
  currency: 'GTQ',
  minimumFractionDigits: 2
});
const formatGTQ = (n) => formatoQuetzal.format(isNaN(n) ? 0 : Number(n));
const parseMoney = (str) => parseFloat(String(str).replace(/[^\d.-]/g, '')) || 0;

// ========== Añadir fila ==========
function addRow() {
  const tbody = document.getElementById("detalle_compra").getElementsByTagName('tbody')[0];

  //  requiere una fila base para clonar selects
  if (!tbody.rows.length) {
    console.error("No hay fila base para clonar selects.");
    return;
  }

  // Fila base (la primera) para clonar los <select>
  const baseRow = tbody.rows[0];

  const newRow = tbody.insertRow(tbody.rows.length);

  // === crea 6 celdas (Proveedor, Insumo, Cantidad, Precio, Subtotal, Acciones)
  const cProv = newRow.insertCell(0);
  const cInsu = newRow.insertCell(1);
  const cCant = newRow.insertCell(2);
  const cPrec = newRow.insertCell(3);
  const cSubt = newRow.insertCell(4);
  const cAct  = newRow.insertCell(5);

  // === Proveedor 
  const provSelectBase = baseRow.cells[0].querySelector('select');
  const provSelect = provSelectBase.cloneNode(true);
  provSelect.selectedIndex = 0;

  provSelect.name = "id_proveedor[]";
  cProv.appendChild(provSelect);

  // === Insumo
  const insumoSelectBase = baseRow.cells[1].querySelector('select');
  const insumoSelect = insumoSelectBase.cloneNode(true);
  insumoSelect.selectedIndex = 0;
  cInsu.appendChild(insumoSelect);

  // === Cantidad 
  const inputCantidad = document.createElement("input");
  inputCantidad.type = "number";
  inputCantidad.name = "cantidad_insumo[]";
  inputCantidad.min = "1";
  inputCantidad.step = "1";
  inputCantidad.required = true;
  inputCantidad.className = "qty";
  inputCantidad.addEventListener('input', calculateSubtotal);
  cCant.appendChild(inputCantidad);

  // === Precio unitario
  const inputPrecio = document.createElement("input");
  inputPrecio.type = "number";
  inputPrecio.name = "precio_unitario[]";
  inputPrecio.step = "0.01";
  inputPrecio.min = "0";
  inputPrecio.required = true;
  inputPrecio.className = "price";
  inputPrecio.addEventListener('input', calculateSubtotal);
  cPrec.appendChild(inputPrecio);

  // === Subtotal
  const inputSubtotal = document.createElement("input");
  inputSubtotal.type = "text";
  inputSubtotal.name = "subtotal[]";
  inputSubtotal.readOnly = true;
  inputSubtotal.className = "subtotal";
  inputSubtotal.value = formatGTQ(0);
  cSubt.appendChild(inputSubtotal);

  // === Acciones 
  const editButton = document.createElement("button");
  editButton.type = "button";
  editButton.className = "btn btn-edit";
  editButton.textContent = "Editar";
  editButton.onclick = function(){ editRow(this); };

  const removeButton = document.createElement("button");
  removeButton.type = "button";
  removeButton.className = "btn btn-danger";
  removeButton.textContent = "Eliminar";
  removeButton.onclick = function(){ removeRow(this); };

  cAct.appendChild(editButton);
  cAct.appendChild(removeButton);
}


// ========== Eliminar fila ==========
function removeRow(button) {
  const row = button.closest('tr');
  const tbody = row.parentNode;
  row.remove();
  // Si no queda ninguna fila, crear una nueva vacía
  if (tbody.rows.length === 0) addRow();
  calculateTotal();
}

// ========== Cálculo subtotal ==========
function calculateSubtotal(event) {
  const row = event.target.closest('tr');
  const cantidad = parseFloat(row.querySelector('input[name="cantidad_insumo[]"]').value) || 0;
  const precio   = parseFloat(row.querySelector('input[name="precio_unitario[]"]').value) || 0;
  const subtotal = cantidad * precio;

  const subtotalInput = row.querySelector('input[name="subtotal[]"]');
  subtotalInput.value = formatGTQ(subtotal);

  calculateTotal();
}

// ========== Cálculo total ==========
function calculateTotal() {
  const tbody = document.getElementById("detalle_compra").getElementsByTagName('tbody')[0];
  const rows = tbody.getElementsByTagName('tr');
  let total = 0;

  for (let i = 0; i < rows.length; i++) {
    const subTxt = rows[i].querySelector('input[name="subtotal[]"]').value;
    total += parseMoney(subTxt);
  }
  document.getElementById('total_compra').value = formatGTQ(total);
}

// ========== Inicializar ==========
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('input[name="cantidad_insumo[]"], input[name="precio_unitario[]"]').forEach(inp => {
    inp.addEventListener('input', calculateSubtotal);
  });

  // Inicializa subtotales  ==========
  const firstSubtotal = document.querySelector('input[name="subtotal[]"]');
  if (firstSubtotal && !firstSubtotal.value) firstSubtotal.value = formatGTQ(0);
  calculateTotal();
});

// ========== Cargar insumos en nuevas filas ==========
window.addRow = async function addRow() {
  const tbody = document.querySelector('#detalle_compra tbody');
  const tpl   = tbody.querySelector('tr'); // 1ra fila como plantilla
  const clone = tpl.cloneNode(true);

  // limpiar inputs/selects
  clone.querySelectorAll('input').forEach(i => i.value = '');
  clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);

  tbody.appendChild(clone);

  //rellena proveedor para ESTA nueva fila
  const provSel = clone.querySelector('select.sel-proveedor');
  if (window.Compras?.fillProveedorSelect && provSel) {
    await window.Compras.fillProveedorSelect(provSel);
  }

  if (typeof hydrateRowInsumos === 'function') {
    await hydrateRowInsumos(clone);
  }
};

