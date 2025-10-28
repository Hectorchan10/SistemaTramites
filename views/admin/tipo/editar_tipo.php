<?php
require '../../../config/database/config/config_db.php';

$id = filter_var($_GET['id_tipo_tramite'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    die('ID no válido');
}

// Cargar tipo
$stmt = $mysqli->prepare("SELECT * FROM tbl_tipo_tramite WHERE id_tipo_tramite = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tipo = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$tipo) {
    die('Tipo de trámite no encontrado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    if (empty($nombre)) {
        $error = 'El nombre es obligatorio.';
    } else {
        $stmt = $mysqli->prepare("UPDATE tbl_tipo_tramite SET nombre=?, descripcion=? WHERE id_tipo_tramite=?");
        $stmt->bind_param("ssi", $nombre, $descripcion, $id);

        if ($stmt->execute()) {
            header("Location: tipos.php?mensaje=Tipo de trámite actualizado correctamente");
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
    <title>Editar Tipo de Trámite</title>
    <link rel="stylesheet" href="/style/tipos.css">
</head>
<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Editar Tipo de Trámite</h1>

        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="formulario">
            <label for="nombre">Nombre del tipo:</label>
            <input type="text" name="nombre" id="nombre" required value="<?= htmlspecialchars($tipo['nombre']) ?>">

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="4"><?= htmlspecialchars($tipo['descripcion']) ?></textarea>

            <button type="submit" class="btn-agregar">Actualizar</button>
            <a href="tipos.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>