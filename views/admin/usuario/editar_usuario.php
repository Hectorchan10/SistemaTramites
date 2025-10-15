<?php
require '../../../config/database/config/config_db.php';
require '../../../email/Mailer.php';

$error = '';
$exito = '';

$id = filter_var($_GET['id_usuario'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    die('ID no válido');
}

// Cargar usuario
$stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$usuario) {
    die('Usuario no encontrado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_usuario'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');
    $rol = trim($_POST['rol'] ?? '');
    $DPI = trim($_POST['DPI'] ?? '');
    $foto = $_FILES['foto']['name'] ?? $usuario['foto'];

    if (empty($nombre) || empty($email) || empty($rol) || empty($DPI)) {
        $error = 'Todos los campos obligatorios deben completarse.';
    } else {
        // Subir nueva foto si existe
        if ($_FILES['foto']['tmp_name'] ?? false) {
            $rutaDestino = __DIR__ . '/../../../uploads/usuarios/' . basename($foto);
            move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino);
        }

        if ($password) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("UPDATE usuarios SET nombre_usuario=?, email=?, password=?, rol=?, foto=?, DPI=? WHERE id_usuario=?");
            $stmt->bind_param("ssssssi", $nombre, $email, $passwordHash, $rol, $foto, $DPI, $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE usuarios SET nombre_usuario=?, email=?, rol=?, foto=?, DPI=? WHERE id_usuario=?");
            $stmt->bind_param("sssssi", $nombre, $email, $rol, $foto, $DPI, $id);
        }

        if ($stmt->execute()) {
            $stmt->close();
            $exito = 'Usuario actualizado correctamente';

            // Enviar correo
            $mailer = new Mailer();
            $mailer->enviarCorreo(
                $email,
                'Actualización de cuenta - Sistema de Trámites',
                __DIR__ . '/../../../templates/correo_actualizacion.html',
                ['nombre' => $nombre, 'email' => $email, 'rol' => $rol, 'password' => $password]
            );

            header("Location: usuarios.php?mensaje=Usuario actualizado correctamente");
            exit;
        } else {
            $error = 'Error al actualizar usuario: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="/style/usuarios.css">
</head>

<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Editar Usuario</h1>

        <?php if ($error): ?>
        <div style="color:red;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Nombre:</label><br>
            <input type="text" name="nombre_usuario"
                value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>"
                required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email"
                value="<?= htmlspecialchars($usuario['email']) ?>"
                required><br><br>

            <label>Nueva Contraseña (dejar en blanco para no cambiar):</label><br>
            <input type="password" name="password"><br><br>

            <label>Rol:</label><br>
            <select name="rol" required>
                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Admin
                </option>
                <option value="empleado" <?= $usuario['rol'] === 'empleado' ? 'selected' : '' ?>>Empleado
                </option>
            </select><br><br>

            <label>DPI:</label><br>
            <input type="text" name="DPI"
                value="<?= htmlspecialchars($usuario['DPI']) ?>"
                maxlength="13" required><br><br>

            <label>Foto:</label><br>
            <input type="file" name="foto" accept="image/*"><br><br>
            <?php if ($usuario['foto']): ?>
            <img src="/uploads/usuarios/<?= htmlspecialchars($usuario['foto']) ?>"
                width="50">
            <?php endif; ?><br><br>

            <button type="submit">Actualizar Usuario</button>
        </form>
    </div>
</body>

</html>