<?php
require '../../../config/database/config/config_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $descripcion = trim($_POST['descripcion']);

    if ($nombre === '') {
        $error = "El nombre del área es obligatorio.";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO tbl_area (nombre, correo, descripcion) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $nombre, $correo, $descripcion);

        if ($stmt->execute()) {
            header("Location: areas.php?mensaje=Área agregada correctamente");
            exit;
        } else {
            $error = "Error al agregar el área: " . $mysqli->error;
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
    <title>Agregar Área</title>
    <link rel="stylesheet" href="/style/areas.css">
</head>
<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Agregar Nueva Área</h1>

        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="formulario">
            <label for="nombre">Nombre del área:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="correo">Correo del área:</label>
            <input type="email" name="correo" id="correo">

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="4"></textarea>

            <button type="submit" class="btn-agregar">Guardar</button>
            <a href="areas.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>
