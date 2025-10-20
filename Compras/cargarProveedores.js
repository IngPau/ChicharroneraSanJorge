// Compras/cargarProveedores.js
window.Compras = window.Compras || {};
(function(NS){
  if (NS._proveedoresLoaded) return;
  NS._proveedoresLoaded = true;

  const PROV_URL = 'cargarProveedores.php?format=options';
  let proveedorOptionsHTML = null;

  async function fetchOptions(url) {
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) {
      const txt = await res.text().catch(()=> '');
      console.error('Respuesta servidor (proveedores):', txt);
      throw new Error('HTTP ' + res.status);
    }
    return await res.text();
  }

  NS.ensureProveedorOptions = async function() {
    if (!proveedorOptionsHTML) {
      proveedorOptionsHTML = await fetchOptions(PROV_URL);
    }
    return proveedorOptionsHTML;
  };

  NS.fillProveedorSelect = async function(selectEl) {
    try {
      const html = await NS.ensureProveedorOptions();
      selectEl.innerHTML = html;
    } catch (e) {
      console.error('Error cargando proveedores:', e);
      selectEl.innerHTML = '<option value="">Error cargando proveedores</option>';
    }
  };

  NS.fillAllProveedorSelects = async function() {
    const html = await NS.ensureProveedorOptions();
    document.querySelectorAll('select.sel-proveedor').forEach(sel => {
      sel.innerHTML = html;
    });
  };

  document.addEventListener('DOMContentLoaded', NS.fillAllProveedorSelects);
})(window.Compras);
