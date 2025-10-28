<?php
require '../../../config/database/config/config_db.php';

$id = filter_var($_GET['id_estado_tramite'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    die('ID no válido');
}

// Verificar que no haya trámites usando este estado
$stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM tbl_tramite WHERE id_estado_tramite = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row['total'] > 0) {
    header("Location: estados.php?mensaje=No se puede eliminar el estado porque tiene trámites asociados");
    exit;
}

// Eliminar el estado
$stmt = $mysqli->prepare("DELETE FROM tbl_estado_tramite WHERE id_estado_tramite = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: estados.php?mensaje=Estado eliminado correctamente");
} else {
    header("Location: estados.php?mensaje=Error al eliminar el estado");
}
$stmt->close();
exit;
?>