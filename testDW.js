const odbc = require('odbc');

(async () => {
  console.log("Node:", process.version, "Arch:", process.arch);
  try {
    // Lista drivers y DSNs
    if (odbc.drivers) {
      const drivers = await odbc.drivers();
      console.log("Drivers ODBC disponibles:", drivers);
    }
    if (odbc.dataSources) {
      const dsnList = await odbc.dataSources();
      console.log("DSNs visibles para este proceso:", dsnList);
    } else {
      console.log("Nota: odbc.dataSources() no está disponible en esta plataforma/build.");
    }

    console.log("Intentando conectar al DSN 'DW'...");
    const conn = await odbc.connect('DSN=DW');
    console.log("✅ Conexión ODBC establecida correctamente.");

    // Consulta neutra que funciona en la mayoría de motores
    const rs = await conn.query('SELECT 1 AS prueba');
    console.log("✅ Consulta de prueba:", rs);

    await conn.close();
    console.log("🔒 Conexión cerrada correctamente.");
  } catch (error) {
    console.error("❌ Error al conectar o ejecutar consulta:");
    console.error(error);              
    if (error.odbcErrors) {             
      console.error("Detalles ODBC:", error.odbcErrors);
    }
  }
})();
