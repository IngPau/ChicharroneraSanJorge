/* const odbc = require('odbc');

class ConexionDW {
  constructor() {
    this.connection = null;
  }

  async conectar() {
    try {
      this.connection = await odbc.connect('DSN=DW');
      console.log("✅ Conexión ODBC establecida correctamente.");
      return this.connection;
    } catch (error) {
      console.error("❌ Error al conectar:", error.message);
    }
  }

  async desconectar() {
    try {
      if (this.connection) {
        await this.connection.close();
        console.log("🔒 Conexión cerrada correctamente.");
      }
    } catch (error) {
      console.error("⚠️ Error al cerrar conexión:", error.message);
    }
  }
}

// Ejemplo de uso:
(async () => {
  const conexion = new ConexionDW();
  const conn = await conexion.conectar();

  // Ejemplo de consulta:
  const result = await conn.query('SELECT * FROM sucursales FETCH FIRST 10 ROWS ONLY');
  console.log(result);

  await conexion.desconectar();
})();
*/