<?php
require '../../../config/database/config/config_db.php';

// Consulta segura y validada
$query = "SELECT id, email, rol FROM usuarios LIMIT 10"; // para limitar el resultado a 10 usuarios por cosulta
$result = $mysqli->query($query);

if (!$result) {
    die("Error en la consulta: " . $mysqli->error);
}

$usuarios = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
</head>

<body>
    <!-- <?php include '../sidebaradministrador.php'; ?>-->

    <div>
        <a href="agregar_usuario.php" class="boton-agregar">+ Agregar Usuario</a>

        <?php if (isset($_GET['mensaje'])): ?>
            <div style="padding: 15px; margin: 20px 0; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;">
                <strong>✓ Éxito:</strong> <?= htmlspecialchars($_GET['mensaje']) ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($usuarios) > 0): ?>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id']) ?>
                    </td>
                    <td><?= htmlspecialchars($usuario['email']) ?>
                    </td>
                    <td><?= htmlspecialchars($usuario['rol']) ?>
                    </td>
                    <td class="acciones">
                        <a
                            href="editar_usuario.php?id=<?= urlencode($usuario['id']) ?>">Editar</a>
                        <a
                            href="eliminar_usuario.php?id=<?= urlencode($usuario['id']) ?>">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4">No se encontraron usuarios.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>