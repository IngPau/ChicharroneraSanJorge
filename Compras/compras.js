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
  const newRow = tbody.insertRow(tbody.rows.length);

  const cell1 = newRow.insertCell(0);
  const cell2 = newRow.insertCell(1);
  const cell3 = newRow.insertCell(2);
  const cell4 = newRow.insertCell(3);
  const cell5 = newRow.insertCell(4);

  // Select de insumos (clon del primero)
  const firstSelect = tbody.rows[0].cells[0].querySelector('select');
  const select = firstSelect.cloneNode(true);
  select.selectedIndex = 0;
  cell1.appendChild(select);

  // Cantidad
  const inputCantidad = document.createElement("input");
  inputCantidad.type = "number";
  inputCantidad.name = "cantidad_insumo[]";
  inputCantidad.min = "1";
  inputCantidad.step = "1";
  inputCantidad.required = true;
  inputCantidad.addEventListener('input', calculateSubtotal);
  cell2.appendChild(inputCantidad);

  // Precio unitario
  const inputPrecio = document.createElement("input");
  inputPrecio.type = "number";
  inputPrecio.name = "precio_unitario[]";
  inputPrecio.step = "0.01";
  inputPrecio.min = "0";
  inputPrecio.required = true;
  inputPrecio.addEventListener('input', calculateSubtotal);
  cell3.appendChild(inputPrecio);

  // Subtotal (solo lectura, formateado GTQ)
  const inputSubtotal = document.createElement("input");
  inputSubtotal.type = "text";
  inputSubtotal.name = "subtotal[]";
  inputSubtotal.readOnly = true;
  inputSubtotal.value = formatGTQ(0);
  cell4.appendChild(inputSubtotal);

  // Botón eliminar
  const removeButton = document.createElement("button");
  removeButton.type = "button";
  removeButton.className = "btn btn-remove";
  removeButton.textContent = "Eliminar";
  removeButton.onclick = function(){ removeRow(this); };
  cell5.appendChild(removeButton);
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

// ========== Cálculo por fila (subtotal) ==========
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
  // Asegura listeners en la primera fila existente
  document.querySelectorAll('input[name="cantidad_insumo[]"], input[name="precio_unitario[]"]').forEach(inp => {
    inp.addEventListener('input', calculateSubtotal);
  });

  // Inicializa subtotales/total visuales
  const firstSubtotal = document.querySelector('input[name="subtotal[]"]');
  if (firstSubtotal && !firstSubtotal.value) firstSubtotal.value = formatGTQ(0);
  calculateTotal();
});
