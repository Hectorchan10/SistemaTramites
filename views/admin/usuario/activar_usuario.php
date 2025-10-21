<?php


require '../../../config/database/config/config_db.php';


// Evita esperas largas
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);
ini_set('max_execution_time', 5);

// Validar ID
$id_usuario = filter_input(INPUT_GET, 'id_usuario', FILTER_VALIDATE_INT);
if (!$id_usuario) {
    header('Location: usuarios.php?error=' . urlencode('ID de usuario no válido'));
    exit();
}

// Activar usuario
$stmt = $mysqli->prepare("UPDATE tbl_usuario SET activo = 1 WHERE id_usuario = ? AND activo = 0 LIMIT 1");
$stmt->bind_param('i', $id_usuario);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $mensaje = 'Usuario activado correctamente';
    $param = 'mensaje';
} else {
    $mensaje = 'No se pudo activar el usuario (¿ya estaba activo?)';
    $param = 'error';
}

// Cerrar
$stmt->close();
$mysqli->close();

// Redirigir rápido
header("Location: usuarios.php?$param=" . urlencode($mensaje));
exit();
