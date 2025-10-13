<?php
require '../../../config/database/config/config_db.php';

$error = '';
$exito = '';

// Obtener el ID del usuario a eliminar
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);

if (!$id) {
    die('ID de usuario no válido.');
}

// Verificar si el usuario existe antes de eliminar
$stmt = $mysqli->prepare("SELECT id, email FROM usuarios WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();

    if (!$usuario) {
        die('Usuario no encontrado.');
    }
} else {
    die('Error al verificar el usuario: ' . $mysqli->error);
}

// Procesar la confirmación de eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'si') {
        // Eliminar el usuario
        $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $stmt->close();
                // Redirigir a la lista de usuarios con mensaje de éxito
                header('Location: usuarios.php?mensaje=Usuario eliminado correctamente');
                exit;
            } else {
                $error = 'Error al eliminar el usuario: ' . $stmt->error;
                $stmt->close();
            }
        } else {
            $error = 'Error al preparar la consulta: ' . $mysqli->error;
        }
    } else {
        // Usuario canceló la eliminación
        header('Location: usuarios.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Usuario</title>
</head>

<body>
    <h1>⚠️ Eliminar Usuario</h1>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="alert alert-warning">
        <strong>¡Atención!</strong> Esta acción no se puede deshacer.
    </div>

    <div class="usuario-info">
        <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
    </div>

    <p>¿Estás seguro de que deseas eliminar este usuario?</p>

    <form method="POST">
        <input type="hidden" name="confirmar" value="si">
        <button type="submit" class="btn btn-danger">Sí, eliminar usuario</button>
        <button type="submit" name="confirmar" value="no" class="btn btn-secondary">Cancelar</button>
    </form>

    <br>
    <a href="usuarios.php">← Volver a la lista de usuarios</a>
</body>

</html>
