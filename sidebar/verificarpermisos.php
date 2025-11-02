<?php
include_once "C:/xampp/htdocs/ChicharroneraSanJorge/conexion.php";

function tienePermiso($permiso_requerido) {
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_rol'])) {
        return false;
    }
    
    // Si es Administrador (rol 1), acceso total
    if ($_SESSION['id_rol'] == 1) {
        return true;
    }
    
    $conn = conectar();
    
    // CORRECCIÓN: Usar id_rol en lugar de id_usuario
    $sql = "SELECT 1 
            FROM rol_permisos rp 
            INNER JOIN permisos p ON rp.id_permiso = p.id_permiso 
            WHERE rp.id_rol = ? AND p.nombre_permiso = ?";
    
    $stmt = $conn->prepare($sql);
    // CORRECCIÓN: Pasar el rol del usuario, no su ID
    $stmt->bind_param("is", $_SESSION['id_rol'], $permiso_requerido);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tiene_permiso = $result->num_rows > 0;
    
    $stmt->close();
    $conn->close();
    
    return $tiene_permiso;
}

function puedeVerModulo($modulo) {
    return tienePermiso($modulo);
}

function verificarAcceso($permiso_requerido, $pagina_error = '/ChicharroneraSanJorge/acceso_denegado.php') {
    if (!tienePermiso($permiso_requerido)) {
        header("Location: $pagina_error");
        exit();
    }
}
?>