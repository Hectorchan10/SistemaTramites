<?php
require '../../../config/database/config/config_db.php';

if (!isset($_GET['id_area'])) {
    die("ID de área no especificado.");
}

$id_area = (int) $_GET['id_area'];

$stmt = $mysqli->prepare("UPDATE tbl_area SET activo = 1 WHERE id_area = ?");
$stmt->bind_param('i', $id_area);

if ($stmt->execute()) {
    header("Location: areas.php?mensaje=Área activada correctamente");
    exit;
} else {
    die("Error al activar el área: " . $mysqli->error);
}

$stmt->close();
?>
