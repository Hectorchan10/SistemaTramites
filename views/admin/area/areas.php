<?php
require '../../../config/database/config/config_db.php';

// Obtener parámetros de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Construir la consulta base
$query = "SELECT 
    id_area,
    nombre,
    correo,
    descripcion,
    fecha_creacion,
    activo
FROM tbl_area
WHERE 1=1 ";

// Preparar array para los parámetros
$params = [];
$types = '';

// Añadir filtro de búsqueda
if (!empty($busqueda)) {
    $query .= " AND (nombre LIKE ? OR correo LIKE ? OR descripcion LIKE ?)";
    $searchTerm = "%$busqueda%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
    $types .= 'sss';
}

// Añadir filtro de estado
if ($estado !== '') {
    $query .= " AND activo = ?";
    $params[] = ($estado === '1') ? 1 : 0;
    $types .= 'i';
}

// Orden y límite
$query .= " ORDER BY id_area DESC LIMIT 50";

// Preparar y ejecutar la consulta
$stmt = $mysqli->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error en la consulta: " . $mysqli->error);
}

$areas = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Áreas</title>
    <link rel="stylesheet" href="/style/areas.css">
</head>

<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Gestión de Áreas</h1>

        <div class="filtros-container">
            <form method="GET" action="" class="filtros-form">
                <div class="filtro-grupo">
                    <input type="text" name="busqueda" placeholder="Buscar por nombre, correo o descripción" 
                        value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
                </div>
                <div class="filtro-grupo">
                    <select name="estado">
                        <option value="">Todos los estados</option>
                        <option value="1" <?= (isset($_GET['estado']) && $_GET['estado'] === '1') ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= (isset($_GET['estado']) && $_GET['estado'] === '0') ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="btn-buscar">Buscar</button>
                <a href="?" class="btn-limpiar">Limpiar filtros</a>
            </form>
        </div>

        <div class="acciones-superiores">
            <a href="agregar_area.php" class="btn-agregar">+ Agregar Área</a>
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
                    <th>Correo</th>
                    <th>Descripción</th>
                    <th>Fecha de creación</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($areas): ?>
                <?php foreach ($areas as $area): ?>
                <tr>
                    <td><?= $area['id_area'] ?></td>
                    <td><?= htmlspecialchars($area['nombre']) ?></td>
                    <td><?= htmlspecialchars($area['correo']) ?></td>
                    <td><?= htmlspecialchars($area['descripcion']) ?></td>
                    <td><?= htmlspecialchars($area['fecha_creacion']) ?></td>
                    <td><?= $area['activo'] ? 'Activo' : 'Inactivo' ?></td>
                    <td class="acciones">
                        <a href="editar_area.php?id_area=<?= $area['id_area'] ?>">Editar</a>
                        <?php if ($area['activo']): ?>
                            <a href="eliminar_area.php?id_area=<?= $area['id_area'] ?>" onclick="return confirm('¿Seguro que deseas desactivar esta área?')">Desactivar</a>
                        <?php else: ?>
                            <a href="activar_area.php?id_area=<?= $area['id_area'] ?>" onclick="return confirm('¿Seguro que deseas activar esta área?')">Activar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7">No se encontraron áreas.</td>
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
