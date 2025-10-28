<?php
require '../../../config/database/config/config_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    if ($nombre === '') {
        $error = "El nombre del estado es obligatorio.";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO tbl_estado_tramite (nombre, descripcion) VALUES (?, ?)");
        $stmt->bind_param('ss', $nombre, $descripcion);

        if ($stmt->execute()) {
            header("Location: estados.php?mensaje=Estado agregado correctamente");
            exit;
        } else {
            $error = "Error al agregar el estado: " . $mysqli->error;
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
    <title>Agregar Estado de Trámite</title>
    <link rel="stylesheet" href="/style/areas.css">
</head>
<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Agregar Nuevo Estado</h1>

        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="formulario">
            <label for="nombre">Nombre del estado:</label>
            <input type="text" name="nombre" id="nombre" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="4"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>

            <button type="submit" class="btn-agregar">Guardar</button>
            <a href="estados.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>