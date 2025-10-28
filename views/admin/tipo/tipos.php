<?php
require '../../../config/database/config/config_db.php';

// Obtener parámetros de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Construir la consulta base
$query = "SELECT
    id_tipo_tramite,
    nombre,
    descripcion,
    activo
FROM tbl_tipo_tramite
WHERE 1=1 ";

// Preparar array para los parámetros
$params = [];
$types = '';

// Añadir filtro de búsqueda
if (!empty($busqueda)) {
    $query .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
    $searchTerm = "%$busqueda%";
    $params = array_merge($params, [$searchTerm, $searchTerm]);
    $types .= 'ss';
}

// Añadir filtro de estado
if ($estado !== '') {
    $query .= " AND activo = ?";
    $params[] = ($estado === '1') ? 1 : 0;
    $types .= 'i';
}

// Orden y límite
$query .= " ORDER BY id_tipo_tramite DESC LIMIT 50";

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

$tipos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tipos de Trámites</title>
    <link rel="stylesheet" href="/style/tipos.css">
</head>

<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Tipos de Trámites</h1>
        <div class="filtros-container">
            <form method="GET" action="" class="filtros-form">
                <div class="filtro-grupo">
                    <input type="text" name="busqueda" placeholder="Buscar por nombre o descripción"
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
            <a href="agregar_tipo.php" class="btn-agregar">+ Agregar Tipo</a>
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
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tipos): ?>
                <?php foreach ($tipos as $tipo): ?>
                <tr>
                    <td><?= $tipo['id_tipo_tramite'] ?></td>
                    <td><?= htmlspecialchars($tipo['nombre']) ?></td>
                    <td><?= htmlspecialchars($tipo['descripcion']) ?></td>
                    <td><?= $tipo['activo'] ? 'Activo' : 'Inactivo' ?></td>
                    <td class="acciones">
                        <a href="editar_tipo.php?id_tipo_tramite=<?= $tipo['id_tipo_tramite'] ?>">Editar</a>
                        <?php if ($tipo['activo']): ?>
                            <a href="eliminar_tipo.php?id_tipo_tramite=<?= $tipo['id_tipo_tramite'] ?>" onclick="return confirm('¿Seguro que deseas desactivar este tipo de trámite?')">Desactivar</a>
                        <?php else: ?>
                            <a href="activar_tipo.php?id_tipo_tramite=<?= $tipo['id_tipo_tramite'] ?>" onclick="return confirm('¿Seguro que deseas activar este tipo de trámite?')">Activar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5">No se encontraron tipos de trámite.</td>
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