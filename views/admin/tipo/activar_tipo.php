<?php
require '../../../config/database/config/config_db.php';

$id = filter_var($_GET['id_tipo_tramite'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    die('ID no válido');
}

// Activar el tipo
$stmt = $mysqli->prepare("UPDATE tbl_tipo_tramite SET activo = TRUE WHERE id_tipo_tramite = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: tipos.php?mensaje=Tipo de trámite activado correctamente");
} else {
    header("Location: tipos.php?mensaje=Error al activar el tipo de trámite");
}
$stmt->close();
exit;
?>