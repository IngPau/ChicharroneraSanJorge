<?php
class ConexionDW {
  private $conn;

  public function conectar() {
    try {
      $this->conn = new PDO("odbc:DSN=DW");
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      echo "✅ Conexión ODBC establecida correctamente.";
      return $this->conn;
    } catch (PDOException $e) {
      echo "❌ Error al conectar: " . $e->getMessage();
      return null;
    }
  }

  public function desconectar() {
    $this->conn = null;
    echo "Conexión cerrada correctamente.";
  }
}
?>
