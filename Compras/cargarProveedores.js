window.Compras = window.Compras || {};
(function(NS){
  if (NS._proveedoresLoaded) return;
  NS._proveedoresLoaded = true;

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

  NS.ensureProveedorOptions = async function ensureProveedorOptions() {
    if (!proveedorOptionsHTML) {
      proveedorOptionsHTML = await fetchOptions('cargarProveedores.php?format=options');
    }
    return proveedorOptionsHTML;
  };

  document.addEventListener('DOMContentLoaded', async () => {
    const selProv = document.getElementById('proveedor'); // <-- este sí existe en tu HTML
    if (!selProv) return;
    try {
      selProv.innerHTML = await NS.ensureProveedorOptions();
    } catch (e) {
      console.error('Error cargando proveedores:', e);
      selProv.innerHTML = '<option value="">Error cargando proveedores</option>';
    }
  });
})(window.Compras);
