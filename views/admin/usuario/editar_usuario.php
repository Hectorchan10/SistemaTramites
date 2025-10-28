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
$stmt = $mysqli->prepare("SELECT * FROM tbl_usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$usuario) {
    die('Usuario no encontrado');
}

// Cargar roles y áreas
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

    // Área opcional
    $id_area = $id_area > 0 ? $id_area : null;

    if (empty($nombre) || empty($correo) || empty($dpi) || !$id_rol) {
        $error = 'Todos los campos obligatorios deben completarse.';
    } else {
        if ($password) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("
                UPDATE tbl_usuario 
                SET nombre=?, apellido=?, correo=?, password=?, dpi=?, id_rol=?, id_area=? 
                WHERE id_usuario=?
            ");
            // tipos: s(nombre), s(apellido), s(correo), s(password), s(dpi), i(id_rol), i(id_area), i(id)
            $stmt->bind_param("sssssiii", $nombre, $apellido, $correo, $passwordHash, $dpi, $id_rol, $id_area, $id);
        } else {
            $stmt = $mysqli->prepare("
                UPDATE tbl_usuario 
                SET nombre=?, apellido=?, correo=?, dpi=?, id_rol=?, id_area=? 
                WHERE id_usuario=?
            ");
            // tipos: s(nombre), s(apellido), s(correo), s(dpi), i(id_rol), i(id_area), i(id)
            $stmt->bind_param("ssssiii", $nombre, $apellido, $correo, $dpi, $id_rol, $id_area, $id);
        }

        if ($stmt->execute()) {
            $stmt->close();

            // Obtener nombre del rol
            $rolNombre = '';
            foreach ($roles as $rolItem) {
                if ((int)$rolItem['id_rol'] === (int)$id_rol) {
                    $rolNombre = $rolItem['nombre'];
                    break;
                }
            }

            $datosCorreo = [
                'email' => $correo,
                'rol' => $rolNombre,
                // Enviar el texto de la contraseña solo si fue cambiada; de lo contrario indicar "Sin cambios"
                'password' => ($password !== '' ? $password : 'Sin cambios')
            ];

            // Enviar correo
            $mailer = new Mailer();
            $mailResult = $mailer->enviarCorreo(
                $correo,
                'Actualización de cuenta - Sistema de Trámites',
                __DIR__ . '/../../../templates/correo_actualizacion.html',
                $datosCorreo
            );

            // Mensaje según resultado del correo
            if ($mailResult !== true) {
                // Registrar detalle del fallo de correo sin interrumpir el flujo
                if (is_string($mailResult)) {
                    error_log('[MAIL] Error al enviar correo de actualización: ' . $mailResult);
                }
                $mensaje = 'Usuario actualizado. Aviso: no se pudo enviar el correo de notificación.';
            } else {
                $mensaje = 'Usuario actualizado correctamente (correo enviado)';
            }

            header("Location: usuarios.php?mensaje=" . urlencode($mensaje));
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
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="formulario">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>">

            <label for="correo">Email</label>
            <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>

            <label for="password">Nueva Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar">

            <label for="id_rol">Rol</label>
            <select id="id_rol" name="id_rol" required>
                <option value="">Seleccione un rol</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= $rol['id_rol'] ?>" <?= ((int)$usuario['id_rol'] === (int)$rol['id_rol']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($rol['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="id_area">Área</label>
            <select id="id_area" name="id_area">
                <option value="0" <?= is_null($usuario['id_area']) ? 'selected' : '' ?>>Sin área</option>
                <?php foreach ($areas as $area): ?>
                    <option value="<?= $area['id_area'] ?>" <?= ((int)$usuario['id_area'] === (int)$area['id_area']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($area['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="dpi">DPI</label>
            <input type="text" id="dpi" name="dpi" value="<?= htmlspecialchars($usuario['dpi']) ?>" maxlength="15" required>

            <button type="submit" class="btn-agregar">Actualizar Usuario</button>
            <a href="usuarios.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>