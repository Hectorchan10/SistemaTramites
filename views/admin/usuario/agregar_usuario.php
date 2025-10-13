<?php
require '../../../config/database/config/config_db.php';
require '../../../email/Mailer.php';
$error = '';
$exito = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? ''); // contraseña sin encriptar     
    //$password = password_hash(trim($_POST['password'] ?? ''), PASSWORD_DEFAULT); si se desea encriptar la contraseña 
    $rol = trim($_POST['rol'] ?? ''); 
    if (empty($email) || empty($password) || empty($rol)) {
        die('Por favor, completa todos los campos.');
    }

    // Insertar usuario en la base de datos
    $stmt = $mysqli->prepare("INSERT INTO usuarios (email, password, rol) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $email, $password, $rol);
        $stmt->execute();
        $stmt->close();

        // Enviar correo de confirmación
        $mailer = new Mailer();
        $resultado = $mailer->enviarCorreo(
            $email,                                  // destinatario
            'Registro Exitoso - Sistema de Trámites', // asunto
            __DIR__ . '/../../../templates/correo_registro.html', // plantilla HTML
            ['email' => $email, 'rol' => $rol , 'password' => $password] // variables dinámicas
        );

        if ($resultado !== true) {
            $error = " Error al enviar correo: $resultado";
        }

        // Redirigir luego de unos segundos
        header('Location: /views/admin/usuario/usuarios.php');
        exit;
    } else {
        $error = 'Error al agregar el usuario: ' . $mysqli->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
</head>

<body>
    <h1>Agregar Usuario</h1>
    <form method="POST">
        <?php if ($error): ?>
            <div class="alert alert-error">
                <strong>⚠️ Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($exito): ?>
            <div class="alert alert-success">
                <strong>✓ Éxito:</strong> <?= htmlspecialchars($exito) ?>
            </div>
        <?php endif; ?>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="admin">Admin</option>
            <option value="usuario">Usuario</option>
        </select><br><br>

        <button type="submit">Agregar Usuario</button>
    </form>
</body>

</html>