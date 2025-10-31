<?php

function conectar() {
    $db = new mysqli("localhost","root", "123456", "CSJ_db");
    //$db = new mysqli("localhost","root", "", "CSJ_db",3307); - Conexion KEVIN
  if (!$db)
        die("no hay conexion a la base de datos");

    return $db;
}

function ir($pagina) {
    print "<meta http-equiv='refresh' content='3;url=$pagina'>";
}

/* 
conexion a DW via ODBC

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
*/
?>