<?php
require '../../../config/database/config/config_db.php';
require '../../../email/Mailer.php';

$error = '';
$exito = '';

// Obtener roles y áreas para los selects
$roles = [];
$areas = [];

$resRoles = $mysqli->query("SELECT id_rol, nombre FROM tbl_rol ORDER BY nombre");
if ($resRoles) {
    $roles = $resRoles->fetch_all(MYSQLI_ASSOC);
}

$resAreas = $mysqli->query("SELECT id_area, nombre FROM tbl_area WHERE activo = TRUE ORDER BY nombre");
if ($resAreas) {
    $areas = $resAreas->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = filter_var(trim($_POST['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');
    $dpi = trim($_POST['dpi'] ?? '');
    $id_rol = intval($_POST['id_rol'] ?? 0);
    $id_area = intval($_POST['id_area'] ?? 0);

    $id_area = $id_area > 0 ? $id_area : null;

    // Validación de campos obligatorios
    if (empty($nombre) || empty($correo) || empty($password) || empty($dpi) || !$id_rol) {
        $error = 'Todos los campos obligatorios deben completarse.';
    }

    // Validar DPI numérico y longitud
    if (!$error && !preg_match('/^\d{13,15}$/', $dpi)) {
        $error = 'El DPI debe contener solo números y tener entre 13 y 15 dígitos.';
    }

    // Validar DPI duplicado
    if (!$error) {
        $stmtDPI = $mysqli->prepare("SELECT id_usuario FROM tbl_usuario WHERE dpi = ?");
        $stmtDPI->bind_param("s", $dpi);
        $stmtDPI->execute();
        $resultDPI = $stmtDPI->get_result();
        if ($resultDPI && $resultDPI->num_rows > 0) {
            $error = "El DPI $dpi ya está registrado.";
        }
        $stmtDPI->close();
    }

    // Validar correo duplicado
    if (!$error) {
        $stmtCorreo = $mysqli->prepare("SELECT id_usuario FROM tbl_usuario WHERE correo = ?");
        $stmtCorreo->bind_param("s", $correo);
        $stmtCorreo->execute();
        $resultCorreo = $stmtCorreo->get_result();
        if ($resultCorreo && $resultCorreo->num_rows > 0) {
            $error = "El correo $correo ya está registrado.";
        }
        $stmtCorreo->close();
    }

    if (!$error) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if ($id_area) {
            $stmt = $mysqli->prepare("
                INSERT INTO tbl_usuario (nombre, apellido, correo, password, dpi, id_area, id_rol)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssssssi", $nombre, $apellido, $correo, $passwordHash, $dpi, $id_area, $id_rol);
        } else {
            $stmt = $mysqli->prepare("
                INSERT INTO tbl_usuario (nombre, apellido, correo, password, dpi, id_rol)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssssi", $nombre, $apellido, $correo, $passwordHash, $dpi, $id_rol);
        }

        if ($stmt->execute()) {
            $stmt->close();

            // Obtener nombre del rol para el correo
            $rolNombre = '';
            foreach ($roles as $r) {
                if ((int)$r['id_rol'] === (int)$id_rol) {
                    $rolNombre = $r['nombre'];
                    break;
                }
            }

            // Enviar correo de confirmación
            $mailer = new Mailer();
            $mailResult = $mailer->enviarCorreo(
                $correo,
                'Registro Exitoso - Sistema de Trámites',
                __DIR__ . '/../../../templates/correo_registro.html',
                [
                    'nombre' => $nombre,
                    'email' => $correo,
                    'password' => $password,
                    'rol' => $rolNombre
                ]
            );

            $mensaje = ($mailResult === true)
                ? 'Usuario agregado correctamente (correo enviado)'
                : 'Usuario agregado. Aviso: no se pudo enviar el correo de notificación.';
            if ($mailResult !== true && is_string($mailResult)) {
                error_log('[MAIL] Error al enviar correo de registro: ' . $mailResult);
            }

            header('Location: usuarios.php?mensaje=' . urlencode($mensaje));
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
    <link rel="stylesheet" href="/style/usuarios.css">
</head>
<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Agregar Usuario</h1>

        <?php if ($error): ?>
            <div style="color:red;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-field">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
            </div>

            <div class="form-field">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>">
            </div>

            <div class="form-field">
                <label for="correo">Email</label>
                <input type="email" id="correo" name="correo" required value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
            </div>

            <div class="form-field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-field">
                <label for="id_rol">Rol</label>
                <select id="id_rol" name="id_rol" required>
                    <option value="">Seleccione un rol</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['id_rol'] ?>" <?= ((int)($_POST['id_rol'] ?? 0) === (int)$rol['id_rol']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rol['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-field">
                <label for="id_area">Área</label>
                <select id="id_area" name="id_area">
                    <option value="0">Sin área</option>
                    <?php foreach ($areas as $area): ?>
                        <option value="<?= $area['id_area'] ?>" <?= ((int)($_POST['id_area'] ?? 0) === (int)$area['id_area']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($area['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-field">
                <label for="dpi">DPI</label>
                <input type="text" id="dpi" name="dpi" maxlength="15" required value="<?= htmlspecialchars($_POST['dpi'] ?? '') ?>">
            </div>

            <div class="form-buttons">
                <a href="usuarios.php" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-guardar">Agregar Usuario</button>
            </div>
        </form>
    </div>
</body>
</html>