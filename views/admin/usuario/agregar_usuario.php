<?php
require '../../../config/database/config/config_db.php';
require '../../../email/Mailer.php';

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre_usuario'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');
    $rol = trim($_POST['rol'] ?? '');
    $DPI = trim($_POST['DPI'] ?? '');
    $foto = $_FILES['foto']['name'] ?? null;

    // Validación de campos obligatorios
    if (empty($nombre) || empty($email) || empty($password) || empty($rol) || empty($DPI)) {
        $error = 'Todos los campos son obligatorios.';
    }

    // Verificar DPI duplicado
    if (!$error) {
        $stmtDPI = $mysqli->prepare("SELECT id_usuario FROM usuarios WHERE DPI=?");
        $stmtDPI->bind_param("s", $DPI);
        $stmtDPI->execute();
        $resultDPI = $stmtDPI->get_result();
        if ($resultDPI->num_rows > 0) {
            $error = "El DPI $DPI ya está registrado.";
        }
        $stmtDPI->close();
    }

    if (!$error) {
        // Encriptar contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Manejar foto opcional
        if ($foto && isset($_FILES['foto']['tmp_name']) && $_FILES['foto']['tmp_name'] !== '') {
            $rutaCarpeta = dirname(__DIR__, 3) . '/uploads/usuarios/';
            if (!is_dir($rutaCarpeta)) {
                $foto = null; // Si la carpeta no existe, no se guarda foto
            } else {
                $nombreUnico = time() . '_' . basename($foto);
                $rutaDestino = $rutaCarpeta . $nombreUnico;

                if (!move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                    $foto = null;
                } else {
                    $foto = $nombreUnico; // Guardar nombre único en DB
                }
            }
        } else {
            $foto = null; // Foto opcional
        }

        // Insertar usuario en DB
        $stmt = $mysqli->prepare("INSERT INTO usuarios (nombre_usuario,email,password,rol,foto,DPI) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $nombre, $email, $passwordHash, $rol, $foto, $DPI);

        if ($stmt->execute()) {
            $stmt->close();

            // Enviar correo de confirmación
            $mailer = new Mailer();
            $mailer->enviarCorreo(
                $email,
                'Registro Exitoso - Sistema de Trámites',
                __DIR__ . '/../../../templates/correo_registro.html',
                ['nombre' => $nombre, 'email' => $email, 'rol' => $rol, 'password' => $password]
            );

            header('Location: usuarios.php?mensaje=Usuario agregado correctamente');
            exit;

        } else {
            $error = 'Error al agregar usuario: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
</head>

<body>
    <h1>Agregar Usuario</h1>

    <?php if ($error): ?>
    <div style="color:red;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nombre:</label><br>
        <input type="text" name="nombre_usuario" required
            value="<?= htmlspecialchars($_POST['nombre_usuario'] ?? '') ?>"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Rol:</label><br>
        <select name="rol" required>
            <option value="admin" <?= (($_POST['rol'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin
            </option>
            <option value="empleado" <?= (($_POST['rol'] ?? '') === 'empleado') ? 'selected' : '' ?>>Empleado
            </option>
        </select><br><br>

        <label>DPI:</label><br>
        <input type="text" name="DPI" maxlength="13" required
            value="<?= htmlspecialchars($_POST['DPI'] ?? '') ?>"><br><br>

        <label>Foto (opcional):</label><br>
        <input type="file" name="foto" accept="image/*"><br><br>

        <button type="submit">Agregar Usuario</button>
    </form>
    <br>
    <a href="usuarios.php">← Volver a la lista de usuarios</a>
</body>

</html>