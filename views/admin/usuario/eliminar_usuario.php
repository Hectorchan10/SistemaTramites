<?php
require '../../../config/database/config/config_db.php';

$id = filter_var($_GET['id_usuario'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    die('ID no válido');
}

$stmt = $mysqli->prepare("SELECT id_usuario, nombre, correo, activo FROM tbl_usuario WHERE id_usuario=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$usuario) {
    die('Usuario no encontrado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['confirmar'] === 'si') {
        $stmt = $mysqli->prepare("UPDATE tbl_usuario SET activo = 0 WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header('Location: usuarios.php?mensaje=Usuario eliminado correctamente');
        exit;
    } else {
        header('Location: usuarios.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Eliminar Usuario</title>
    <link rel="stylesheet" href="/style/usuarios.css">
</head>

<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
    <h1>⚠️ Eliminar Usuario</h1>
    <p>¿Seguro que deseas eliminar a
        <?= htmlspecialchars($usuario['nombre']) ?>?
    </p>

    <form method="POST">
        <button type="submit" name="confirmar" value="si">Sí, eliminar</button>
        <button type="submit" name="confirmar" value="no">Cancelar</button>
    </form>

    </div>
</body>

</html>