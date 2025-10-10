
<?php
function conectar() {
    $db = new mysqli("localhost", "root", "1234", "RestauranteDB");
    if (!$db)
        die("no hay conexion a la base de datos");
    return $db;
}

function ir($pagina) {
    print "<meta http-equiv='refresh' content='3;url=$pagina'>";
}

?>