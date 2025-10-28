<?php
require '../../../config/database/config/config_db.php';

if (!isset($_GET['id_area'])) {
    die("ID de área no especificado.");
}

$id_area = (int) $_GET['id_area'];

// Obtener datos del área
$stmt = $mysqli->prepare("SELECT * FROM tbl_area WHERE id_area = ?");
$stmt->bind_param('i', $id_area);
$stmt->execute();
$result = $stmt->get_result();
$area = $result->fetch_assoc();
$stmt->close();

if (!$area) {
    die("Área no encontrada.");
}

// Si se envía el formulario, actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $descripcion = trim($_POST['descripcion']);
    $activo = isset($_POST['activo']) ? 1 : 0;

    if ($nombre === '') {
        $error = "El nombre del área es obligatorio.";
    } else {
        $stmt = $mysqli->prepare("UPDATE tbl_area SET nombre = ?, correo = ?, descripcion = ?, activo = ? WHERE id_area = ?");
        $stmt->bind_param('sssii', $nombre, $correo, $descripcion, $activo, $id_area);

        if ($stmt->execute()) {
            header("Location: areas.php?mensaje=Área actualizada correctamente");
            exit;
        } else {
            $error = "Error al actualizar el área: " . $mysqli->error;
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
    <title>Editar Área</title>
    <link rel="stylesheet" href="/style/areas.css">
</head>
<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Editar Área</h1>

        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="formulario">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($area['nombre']) ?>" required>

            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" value="<?= htmlspecialchars($area['correo']) ?>">

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="4"><?= htmlspecialchars($area['descripcion']) ?></textarea>

            <label><input type="checkbox" name="activo" <?= $area['activo'] ? 'checked' : '' ?>> Activa</label>

            <button type="submit" class="btn-guardar">Guardar cambios</button>
            <a href="areas.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>
