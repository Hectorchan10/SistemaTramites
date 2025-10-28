<?php
require '../../../config/database/config/config_db.php';

// Obtener parámetros de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

// Construir la consulta base
$query = "SELECT
    t.id_tramite,
    t.asunto,
    t.fecha_creacion,
    t.fecha_caducidad,
    e.nombre as estado,
    tp.nombre as tipo,
    r.nombre as remitente,
    u.nombre as creador
FROM tbl_tramite t
LEFT JOIN tbl_estado_tramite e ON t.id_estado_tramite = e.id_estado_tramite
LEFT JOIN tbl_tipo_tramite tp ON t.id_tipo_tramite = tp.id_tipo_tramite
LEFT JOIN tbl_remitente r ON t.id_remitente = r.id_remitente
LEFT JOIN tbl_usuario u ON t.id_usuario_creador = u.id_usuario
WHERE 1=1 ";

// Preparar array para los parámetros
$params = [];
$types = '';

// Añadir filtro de búsqueda
if (!empty($busqueda)) {
    $query .= " AND (t.asunto LIKE ? OR r.nombre LIKE ? OR t.tags LIKE ?)";
    $searchTerm = "%$busqueda%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
    $types .= 'sss';
}

// Añadir filtro de estado
if ($estado !== '') {
    $query .= " AND t.id_estado_tramite = ?";
    $params[] = $estado;
    $types .= 'i';
}

// Añadir filtro de tipo
if ($tipo !== '') {
    $query .= " AND t.id_tipo_tramite = ?";
    $params[] = $tipo;
    $types .= 'i';
}

// Orden y límite
$query .= " ORDER BY t.fecha_creacion DESC LIMIT 50";

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

$tramites = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Obtener listas para filtros
$estados = $mysqli->query("SELECT id_estado_tramite, nombre FROM tbl_estado_tramite ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$tipos = $mysqli->query("SELECT id_tipo_tramite, nombre FROM tbl_tipo_tramite WHERE activo = TRUE ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Trámites</title>
    <link rel="stylesheet" href="/style/areas.css">
</head>

<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Trámites</h1>
        <div class="filtros-container">
            <form method="GET" action="" class="filtros-form">
                <div class="filtro-grupo">
                    <input type="text" name="busqueda" placeholder="Buscar por asunto, remitente o tags"
                        value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
                </div>
                <div class="filtro-grupo">
                    <select name="estado">
                        <option value="">Todos los estados</option>
                        <?php foreach ($estados as $est): ?>
                            <option value="<?= $est['id_estado_tramite'] ?>" <?= (isset($_GET['estado']) && $_GET['estado'] === $est['id_estado_tramite']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($est['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filtro-grupo">
                    <select name="tipo">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($tipos as $tip): ?>
                            <option value="<?= $tip['id_tipo_tramite'] ?>" <?= (isset($_GET['tipo']) && $_GET['tipo'] === $tip['id_tipo_tramite']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tip['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-buscar">Buscar</button>
                <a href="?" class="btn-limpiar">Limpiar filtros</a>
            </form>
        </div>
        <div class="acciones-superiores">
            <a href="agregar_tramite.php" class="btn-agregar">+ Nuevo Trámite</a>
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
                    <th>Asunto</th>
                    <th>Remitente</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                    <th>Creador</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tramites): ?>
                <?php foreach ($tramites as $tramite): ?>
                <tr>
                    <td><?= $tramite['id_tramite'] ?></td>
                    <td><?= htmlspecialchars($tramite['asunto']) ?></td>
                    <td><?= htmlspecialchars($tramite['remitente'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($tramite['tipo'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($tramite['estado'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($tramite['fecha_creacion']) ?></td>
                    <td><?= htmlspecialchars($tramite['creador'] ?? 'N/A') ?></td>
                    <td class="acciones">
                        <a href="ver_tramite.php?id_tramite=<?= $tramite['id_tramite'] ?>">Ver</a>
                        <a href="editar_tramite.php?id_tramite=<?= $tramite['id_tramite'] ?>">Editar</a>
                        <a href="seguimiento.php?id_tramite=<?= $tramite['id_tramite'] ?>">Seguimiento</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8">No se encontraron trámites.</td>
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