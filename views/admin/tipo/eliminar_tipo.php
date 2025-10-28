<?php
require '../../../config/database/config/config_db.php';

$id = filter_var($_GET['id_tipo_tramite'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    die('ID no válido');
}

// Verificar que no haya trámites usando este tipo
$stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM tbl_tramite WHERE id_tipo_tramite = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row['total'] > 0) {
    header("Location: tipos.php?mensaje=No se puede desactivar el tipo porque tiene trámites asociados");
    exit;
}

// Desactivar el tipo
$stmt = $mysqli->prepare("UPDATE tbl_tipo_tramite SET activo = FALSE WHERE id_tipo_tramite = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: tipos.php?mensaje=Tipo de trámite desactivado correctamente");
} else {
    header("Location: tipos.php?mensaje=Error al desactivar el tipo de trámite");
}
$stmt->close();
exit;
?>