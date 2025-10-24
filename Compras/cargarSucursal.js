window.Compras = window.Compras || {};
(function(NS){
  if (NS._SucursalLoaded) return;
  NS._SucursalLoaded = true;

  const PROV_URL = 'cargarSucursal.php?format=options';
  let SucursalOptionsHTML = null;

  async function fetchOptions(url) {
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) {
      const txt = await res.text().catch(()=> '');
      console.error('Respuesta servidor (sucursal):', txt);
      throw new Error('HTTP ' + res.status);
    }
    return await res.text();
  }

  NS.ensureSucursalOptions = async function() {
    if (!SucursalOptionsHTML) {
      SucursalOptionsHTML = await fetchOptions(PROV_URL);
    }
    return SucursalOptionsHTML;
  };

  NS.fillSucursalSelect = async function(selectEl) {
    try {
      const html = await NS.ensureSucursalOptions();
      selectEl.innerHTML = html;
    } catch (e) {
      console.error('Error cargando sucursales:', e);
      selectEl.innerHTML = '<option value="">Error cargando Sucursales</option>';
    }
  };

  NS.fillAllSucursalSelects = async function() {
    const html = await NS.ensureSucursalOptions();
    document.querySelectorAll('select.sel-sucursal').forEach(sel => {
      sel.innerHTML = html;
    });
  };

  document.addEventListener('DOMContentLoaded', NS.fillAllSucursalSelects);
})(window.Compras);
