<?php
require '../../../config/database/config/config_db.php';

$query = "SELECT id_usuario, nombre_usuario, email, rol, foto, DPI FROM usuarios LIMIT 10";
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
    <link rel="stylesheet" href="/style/usuarios.css">
</head>
<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <!-- ✅ Envuelve TODO el contenido principal aquí -->
    <div class="contenido-principal">
        <h1>Usuarios</h1>
        <a href="agregar_usuario.php" class="btn-agregar">+ Agregar Usuario</a>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje-exito">
                <?= htmlspecialchars($_GET['mensaje']) ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Foto</th>
                    <th>DPI</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($usuarios): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= $usuario['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($usuario['nombre_usuario']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= htmlspecialchars($usuario['rol']) ?></td>
                        <td>
                            <?php if ($usuario['foto']): ?>
                                <img src="/uploads/usuarios/<?= htmlspecialchars($usuario['foto']) ?>" width="50" alt="Foto">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($usuario['DPI']) ?></td>
                        <td class="acciones">
                            <a href="editar_usuario.php?id_usuario=<?= $usuario['id_usuario'] ?>">Editar</a> |
                            <a href="eliminar_usuario.php?id_usuario=<?= $usuario['id_usuario'] ?>">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No se encontraron usuarios.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>