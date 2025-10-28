<?php
require '../../../config/database/config/config_db.php';

$id = filter_var($_GET['id_tramite'] ?? 0, FILTER_VALIDATE_INT);
if (!$id) {
    die('ID no válido');
}

// Cargar trámite con todas las relaciones
$query = "
    SELECT
        t.*,
        e.nombre as estado,
        tp.nombre as tipo,
        r.*,
        u.nombre as creador_nombre,
        u.apellido as creador_apellido
    FROM tbl_tramite t
    LEFT JOIN tbl_estado_tramite e ON t.id_estado_tramite = e.id_estado_tramite
    LEFT JOIN tbl_tipo_tramite tp ON t.id_tipo_tramite = tp.id_tipo_tramite
    LEFT JOIN tbl_remitente r ON t.id_remitente = r.id_remitente
    LEFT JOIN tbl_usuario u ON t.id_usuario_creador = u.id_usuario
    WHERE t.id_tramite = ?
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$tramite = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tramite) {
    die('Trámite no encontrado');
}

// Cargar áreas asignadas
$areas = $mysqli->prepare("
    SELECT a.nombre
    FROM tbl_tramite_area ta
    JOIN tbl_area a ON ta.id_area = a.id_area
    WHERE ta.id_tramite = ?
");
$areas->bind_param("i", $id);
$areas->execute();
$areas_asignadas = $areas->get_result()->fetch_all(MYSQLI_ASSOC);
$areas->close();

// Cargar seguimiento
$seguimiento = $mysqli->prepare("
    SELECT s.*, u.nombre, u.apellido
    FROM tbl_seguimiento s
    LEFT JOIN tbl_usuario u ON s.id_usuario_seguimiento = u.id_usuario
    WHERE s.id_tramite = ?
    ORDER BY s.fecha DESC
");
$seguimiento->bind_param("i", $id);
$seguimiento->execute();
$historial = $seguimiento->get_result()->fetch_all(MYSQLI_ASSOC);
$seguimiento->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Trámite #<?= $tramite['id_tramite'] ?></title>
    <link rel="stylesheet" href="/style/tramites.css">
</head>
<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Trámite #<?= $tramite['id_tramite'] ?> - <?= htmlspecialchars($tramite['asunto']) ?></h1>

        <div class="acciones-superiores">
            <a href="editar_tramite.php?id_tramite=<?= $tramite['id_tramite'] ?>" class="btn-agregar">Editar</a>
            <a href="seguimiento.php?id_tramite=<?= $tramite['id_tramite'] ?>" class="btn-agregar">Agregar Seguimiento</a>
            <a href="tramites.php" class="btn-cancelar">Volver</a>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <!-- Información del Trámite -->
            <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
                <h2>Información del Trámite</h2>

                <div style="margin-top: 1rem;">
                    <strong>Asunto:</strong><br>
                    <?= htmlspecialchars($tramite['asunto']) ?><br><br>

                    <strong>Mensaje:</strong><br>
                    <?= nl2br(htmlspecialchars($tramite['mensaje'])) ?><br><br>

                    <strong>Base Legal:</strong><br>
                    <?= nl2br(htmlspecialchars($tramite['base_legal'])) ?><br><br>

                    <strong>Tags:</strong><br>
                    <?= htmlspecialchars($tramite['tags']) ?><br><br>

                    <strong>Tipo:</strong> <?= htmlspecialchars($tramite['tipo']) ?><br>
                    <strong>Estado:</strong> <?= htmlspecialchars($tramite['estado']) ?><br>
                    <strong>Fecha Creación:</strong> <?= htmlspecialchars($tramite['fecha_creacion']) ?><br>
                    <strong>Fecha Caducidad:</strong> <?= htmlspecialchars($tramite['fecha_caducidad'] ?? 'N/A') ?><br>
                    <strong>Creador:</strong> <?= htmlspecialchars(($tramite['creador_nombre'] ?? '') . ' ' . ($tramite['creador_apellido'] ?? '')) ?>
                </div>
            </div>

            <!-- Información del Remitente -->
            <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
                <h2>Información del Remitente</h2>

                <div style="margin-top: 1rem;">
                    <strong>Tipo:</strong> <?= $tramite['tipo_persona'] === 'Natural' ? 'Persona Natural' : 'Persona Jurídica' ?><br>
                    <strong>Nombre:</strong> <?= htmlspecialchars($tramite['nombre']) ?><br>

                    <?php if ($tramite['tipo_persona'] === 'Jurídica'): ?>
                        <strong>Razón Social:</strong> <?= htmlspecialchars($tramite['razon_social']) ?><br>
                    <?php endif; ?>

                    <strong>Dirección:</strong> <?= htmlspecialchars($tramite['direccion'] ?? 'N/A') ?><br>
                    <strong>NIT:</strong> <?= htmlspecialchars($tramite['nit'] ?? 'N/A') ?><br>
                    <strong>Correo:</strong> <?= htmlspecialchars($tramite['correo'] ?? 'N/A') ?><br>
                    <strong>Teléfono:</strong> <?= htmlspecialchars($tramite['telefono'] ?? 'N/A') ?>
                </div>
            </div>
        </div>

        <!-- Áreas Asignadas -->
        <?php if ($areas_asignadas): ?>
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); margin-top: 2rem;">
            <h2>Áreas Asignadas</h2>
            <div style="margin-top: 1rem;">
                <?php foreach ($areas_asignadas as $area): ?>
                    <span style="display: inline-block; background: #e2e8f0; padding: 0.25rem 0.75rem; border-radius: 4px; margin: 0.25rem; font-size: 0.9rem;">
                        <?= htmlspecialchars($area['nombre']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Historial de Seguimiento -->
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); margin-top: 2rem;">
            <h2>Historial de Seguimiento</h2>

            <?php if ($historial): ?>
                <div style="margin-top: 1rem;">
                    <?php foreach ($historial as $item): ?>
                        <div style="border-left: 4px solid #3b82f6; padding-left: 1rem; margin-bottom: 1rem; background: #f8fafc; padding: 1rem; border-radius: 6px;">
                            <div style="font-weight: bold; color: #3b82f6;">
                                <?= htmlspecialchars($item['fecha']) ?>
                                <?php if ($item['nombre']): ?>
                                    - <?= htmlspecialchars($item['nombre'] . ' ' . $item['apellido']) ?>
                                <?php endif; ?>
                            </div>
                            <div style="margin-top: 0.5rem;">
                                <?= nl2br(htmlspecialchars($item['detalle'])) ?>
                            </div>
                            <?php if ($item['documento_adjunto']): ?>
                                <div style="margin-top: 0.5rem;">
                                    <a href="/uploads/<?= htmlspecialchars($item['documento_adjunto']) ?>" target="_blank" style="color: #3b82f6; text-decoration: underline;">
                                        Ver documento adjunto
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #666; font-style: italic;">No hay seguimiento registrado para este trámite.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>