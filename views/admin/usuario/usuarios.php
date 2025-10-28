<?php
require '../../../config/database/config/config_db.php';

// Obtener parámetros de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Construir la consulta base
$query = "SELECT 
    u.id_usuario,
    u.nombre,
    u.apellido,
    u.correo,
    u.dpi,
    u.activo,
    r.nombre AS rol,
    a.nombre AS area
FROM tbl_usuario u
LEFT JOIN tbl_rol r ON u.id_rol = r.id_rol
LEFT JOIN tbl_area a ON u.id_area = a.id_area
WHERE 1=1 ";

// Preparar array para los parámetros
$params = [];
$types = '';

// Añadir filtro de búsqueda
if (!empty($busqueda)) {
    $query .= " AND (u.nombre LIKE ? OR u.apellido LIKE ? OR u.correo LIKE ? OR u.dpi LIKE ?)";
    $searchTerm = "%$busqueda%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    $types .= 'ssss';
}

// Añadir filtro de estado
if ($estado !== '') {
    $query .= " AND u.activo = ?";
    $params[] = ($estado === '1') ? 1 : 0;
    $types .= 'i';
}

// Orden y límite
$query .= " ORDER BY u.id_usuario DESC LIMIT 50";

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

$usuarios = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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

    <div class="contenido-principal">
        <div class="contenido-usuario">
        <h1>Usuarios</h1>
        <div class="filtros-container">
            <form method="GET" action="" class="filtros-form">
                <div class="filtro-grupo">
                    <input type="text" name="busqueda" placeholder="Buscar por nombre, correo o DPI" 
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
            <a href="agregar_usuario.php" class="btn-agregar">+ Agregar Usuario</a>
        </div>

        <?php if (isset($_GET['mensaje'])): ?>
        <div class="mensaje-exito" id="flash-message">
            <?= htmlspecialchars($_GET['mensaje']) ?>
        </div>
        <?php endif; ?>

        <div class="tabla-container">    
        <table >
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Área</th>
                    <th>DPI</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($usuarios): ?>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= $usuario['id_usuario'] ?></td>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                    <td><?= htmlspecialchars($usuario['correo']) ?></td>
                    <td><?= htmlspecialchars($usuario['rol'] ?? '') ?></td>
                    <td><?= htmlspecialchars($usuario['area'] ?? '') ?></td>
                    <td><?= htmlspecialchars($usuario['dpi']) ?></td>
                    <td><?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?></td>
                    <td class="acciones">
                        <a href="editar_usuario.php?id_usuario=<?= $usuario['id_usuario'] ?>">Editar</a>
                        <?php if ($usuario['activo']): ?>
                            
                            <a href="eliminar_usuario.php?id_usuario=<?= $usuario['id_usuario'] ?>" >Eliminar</a>
                                
                        <?php else: ?>
                            
                            <a href="activar_usuario.php?id_usuario=<?= $usuario['id_usuario'] ?>" onclick="return confirm('¿Seguro que deseas activar el usuario?')">Activar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8">No se encontraron usuarios.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    </div>
    
    </div>
    <script>
        (function () {
            var el = document.getElementById('flash-message');
            if (!el) return;
            
            // Remove the mensaje parameter from URL without page reload
            if (window.history.replaceState && window.location.search.includes('mensaje=')) {
                var cleanUrl = window.location.pathname + window.location.search.replace(/[&?]mensaje=[^&]+/, '').replace(/^&/, '?');
                window.history.replaceState({}, document.title, cleanUrl);
            }
            
            setTimeout(function () {
                try {
                    el.style.transition = 'opacity 350ms ease';
                    el.style.opacity = '0';
                    setTimeout(function () { if (el && el.parentNode) { el.parentNode.removeChild(el); } }, 400);
                } catch (e) {
                    // Fallback: remove without animation
                    if (el && el.parentNode) { el.parentNode.removeChild(el); }
                }
            }, 4000);
        })();
    </script>
</body>

</html>