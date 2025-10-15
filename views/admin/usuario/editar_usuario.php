<?php

require '../../../config/database/config/config_db.php';

require_once __DIR__ . '/../../../email/Mailer.php';

$error = '';
$exito = '';
$usuario = null;

// Obtener el ID del usuario a editar
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);

if (!$id) {
    die('ID de usuario no válido.');
}

// Cargar datos del usuario
$stmt = $mysqli->prepare("SELECT id, email, rol FROM usuarios WHERE id = ?");
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
    die('Error al cargar el usuario: ' . $mysqli->error);
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');
    $rol = trim($_POST['rol'] ?? '');

    if (empty($email) || empty($rol)) {
        $error = 'Por favor, completa todos los campos obligatorios.';
    } else {
        // Si se proporciona una nueva contraseña, actualizarla
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE usuarios SET email = ?, password = ?, rol = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("sssi", $email, $passwordHash, $rol, $id);
            }
        } else {
            // Solo actualizar email y rol
            $stmt = $mysqli->prepare("UPDATE usuarios SET email = ?, rol = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("ssi", $email, $rol, $id);
            }
        }

        if ($stmt) {
            if ($stmt->execute()) {
                $stmt->close();
                $exito = 'Usuario actualizado correctamente.';

                // Enviar correo de notificación
                $mailer = new Mailer();
                $resultado = $mailer->enviarCorreo(
                    $email,
                    'Actualización de Cuenta - Sistema de Trámites',
                    __DIR__ . '/../../../templates/correo_actualizacion.html',
                    ['email' => $email, 'rol' => $rol , 'password' => $password]
                );

                if ($resultado !== true) {
                    $error = "Usuario actualizado, pero error al enviar correo: $resultado";
                }

                // Actualizar datos del usuario en la vista
                $usuario['email'] = $email;
                $usuario['rol'] = $rol;
            } else {
                $error = 'Error al actualizar el usuario: ' . $stmt->error;
                $stmt->close();
            }
        } else {
            $error = 'Error al preparar la consulta: ' . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
</head>

<body>
    <h1>Editar Usuario</h1>
    <form method="POST">
        <?php if ($error): ?>
        <div class="alert alert-error">
            <strong>⚠️ Error:</strong>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        <?php if ($exito): ?>
        <div class="alert alert-success">
            <strong>✓ Éxito:</strong> <?= htmlspecialchars($exito) ?>
        </div>
        <?php endif; ?>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email"
            value="<?= htmlspecialchars($usuario['email']) ?>"
            required><br><br>

        <label for="password">Nueva Contraseña (dejar en blanco para no cambiar):</label>
        <input type="password" id="password" name="password"><br><br>

        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Admin
            </option>
            <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>Usuario
            </option>
        </select><br><br>

        <button type="submit">Actualizar Usuario</button>
        <a href="usuarios.php">Cancelar</a>
    </form>
</body>

</html>