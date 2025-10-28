<?php
require '../../../config/database/config/config_db.php';

// Obtener todos los estados (no tienen filtro de activo/inactivo)
$query = "SELECT
    id_estado_tramite,
    nombre,
    descripcion
FROM tbl_estado_tramite
ORDER BY id_estado_tramite ASC";

$result = $mysqli->query($query);
if (!$result) {
    die("Error en la consulta: " . $mysqli->error);
}

$estados = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estados de Trámites</title>
    <link rel="stylesheet" href="/style/areas.css">
</head>

<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Estados de Trámites</h1>

        <div class="acciones-superiores">
            <a href="agregar_estado.php" class="btn-agregar">+ Agregar Estado</a>
        </div>

        <?php if (isset($_GET['mensaje'])): ?>
        <div class="mensaje-exito" id="flash-message">
            <?= htmlspecialchars($_GET['mensaje']) ?>
        </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($estados): ?>
                <?php foreach ($estados as $estado): ?>
                <tr>
                    <td><?= $estado['id_estado_tramite'] ?></td>
                    <td><?= htmlspecialchars($estado['nombre']) ?></td>
                    <td><?= htmlspecialchars($estado['descripcion']) ?></td>
                    <td class="acciones">
                        <a href="editar_estado.php?id_estado_tramite=<?= $estado['id_estado_tramite'] ?>">Editar</a>
                        <a href="eliminar_estado.php?id_estado_tramite=<?= $estado['id_estado_tramite'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este estado?')">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4">No se encontraron estados de trámite.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
        (function () {
            var el = document.getElementById('flash-message');
            if (!el) return;
            if (window.history.replaceState && window.location.search.includes('mensaje=')) {
                var cleanUrl = window.location.pathname + window.location.search.replace(/[&?]mensaje=[^&]+/, '').replace(/^&/, '?');
                window.history.replaceState({}, document.title, cleanUrl);
            }
            setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 4000);
        })();
    </script>
</body>

</html>