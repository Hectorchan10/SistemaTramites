<?php
require '../../../config/database/config/config_db.php';

$id = filter_var($_GET['id_estado_tramite'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    die('ID no válido');
}

// Cargar estado
$stmt = $mysqli->prepare("SELECT * FROM tbl_estado_tramite WHERE id_estado_tramite = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$estado = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$estado) {
    die('Estado no encontrado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $color = trim($_POST['color']);
    $descripcion = trim($_POST['descripcion']);

    if (empty($nombre)) {
        $error = 'El nombre es obligatorio.';
    } elseif (empty($color)) {
        $error = 'Debe seleccionar un color.';
    } else {
        $stmt = $mysqli->prepare("UPDATE tbl_estado_tramite SET nombre=?, color=?, descripcion=? WHERE id_estado_tramite=?");
        $stmt->bind_param("sssi", $nombre, $color, $descripcion, $id);

        if ($stmt->execute()) {
            header("Location: estados.php?mensaje=Estado actualizado correctamente");
            exit;
        } else {
            $error = 'Error al actualizar: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Estado de Trámite</title>
    <link rel="stylesheet" href="/style/estados.css">
</head>
<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Editar Estado</h1>

        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="formulario">
            <label for="nombre">Nombre del estado:</label>
            <input type="text" name="nombre" id="nombre" required value="<?= htmlspecialchars($estado['nombre']) ?>">

            <label for="color">Color del estado:</label>
            <input type="color" name="color" id="color" required value="<?= htmlspecialchars($estado['color'] ?? '#3b82f6') ?>">

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="4"><?= htmlspecialchars($estado['descripcion']) ?></textarea>

            <button type="submit" class="btn-agregar">Actualizar</button>
            <a href="estados.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>