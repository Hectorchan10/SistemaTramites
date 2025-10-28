<?php
require '../../../config/database/config/config_db.php';

// Obtener listas para selects
$estados = $mysqli->query("SELECT id_estado_tramite, nombre FROM tbl_estado_tramite ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$tipos = $mysqli->query("SELECT id_tipo_tramite, nombre FROM tbl_tipo_tramite WHERE activo = TRUE ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$areas = $mysqli->query("SELECT id_area, nombre FROM tbl_area WHERE activo = TRUE ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asunto = trim($_POST['asunto']);
    $mensaje = trim($_POST['mensaje']);
    $base_legal = trim($_POST['base_legal']);
    $tags = trim($_POST['tags']);
    $fecha_caducidad = trim($_POST['fecha_caducidad']);
    $id_estado_tramite = intval($_POST['id_estado_tramite']);
    $id_tipo_tramite = intval($_POST['id_tipo_tramite']);

    // Datos del remitente
    $tipo_persona = $_POST['tipo_persona'];
    $nombre_remitente = trim($_POST['nombre_remitente']);
    $direccion = trim($_POST['direccion']);
    $nit = trim($_POST['nit']);
    $correo_remitente = trim($_POST['correo_remitente']);
    $telefono = trim($_POST['telefono']);
    $razon_social = trim($_POST['razon_social']);

    if (empty($asunto) || !$id_estado_tramite || !$id_tipo_tramite || empty($nombre_remitente)) {
        $error = 'Los campos obligatorios deben completarse.';
    } else {
        // Insertar remitente primero
        $stmtRemitente = $mysqli->prepare("
            INSERT INTO tbl_remitente (tipo_persona, nombre, direccion, nit, correo, telefono, razon_social)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtRemitente->bind_param("sssssss", $tipo_persona, $nombre_remitente, $direccion, $nit, $correo_remitente, $telefono, $razon_social);

        if ($stmtRemitente->execute()) {
            $id_remitente = $stmtRemitente->insert_id;
            $stmtRemitente->close();

            // Insertar trámite
            $fecha_caducidad = $fecha_caducidad ? $fecha_caducidad : null;
            $stmt = $mysqli->prepare("
                INSERT INTO tbl_tramite (asunto, mensaje, base_legal, tags, fecha_caducidad, id_remitente, id_estado_tramite, id_tipo_tramite, id_usuario_creador)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $id_usuario = $_SESSION['usuario_id'] ?? 1; // Usuario actual o por defecto
            $stmt->bind_param("sssssiii", $asunto, $mensaje, $base_legal, $tags, $fecha_caducidad, $id_remitente, $id_estado_tramite, $id_tipo_tramite, $id_usuario);

            if ($stmt->execute()) {
                $id_tramite = $stmt->insert_id;
                $stmt->close();

                // Asignar áreas si se seleccionaron
                if (isset($_POST['areas']) && is_array($_POST['areas'])) {
                    foreach ($_POST['areas'] as $id_area) {
                        $stmtArea = $mysqli->prepare("INSERT INTO tbl_tramite_area (id_tramite, id_area) VALUES (?, ?)");
                        $stmtArea->bind_param("ii", $id_tramite, $id_area);
                        $stmtArea->execute();
                        $stmtArea->close();
                    }
                }

                header("Location: tramites.php?mensaje=Trámite creado correctamente");
                exit;
            } else {
                $error = 'Error al crear el trámite: ' . $stmt->error;
            }
        } else {
            $error = 'Error al registrar el remitente: ' . $stmtRemitente->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Trámite</title>
    <link rel="stylesheet" href="/style/areas.css">
</head>
<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Crear Nuevo Trámite</h1>

        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="formulario">
            <label for="asunto">Asunto *</label>
            <input type="text" name="asunto" id="asunto" required value="<?= htmlspecialchars($_POST['asunto'] ?? '') ?>">

            <label for="mensaje">Mensaje</label>
            <textarea name="mensaje" id="mensaje" rows="4"><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>

            <label for="base_legal">Base Legal</label>
            <textarea name="base_legal" id="base_legal" rows="3"><?= htmlspecialchars($_POST['base_legal'] ?? '') ?></textarea>

            <label for="tags">Tags</label>
            <input type="text" name="tags" id="tags" placeholder="Separados por comas" value="<?= htmlspecialchars($_POST['tags'] ?? '') ?>">

            <label for="fecha_caducidad">Fecha de Caducidad</label>
            <input type="date" name="fecha_caducidad" id="fecha_caducidad" value="<?= htmlspecialchars($_POST['fecha_caducidad'] ?? '') ?>">

            <label for="id_tipo_tramite">Tipo de Trámite *</label>
            <select name="id_tipo_tramite" id="id_tipo_tramite" required>
                <option value="">Seleccione un tipo</option>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?= $tipo['id_tipo_tramite'] ?>" <?= ((int)($_POST['id_tipo_tramite'] ?? 0) === (int)$tipo['id_tipo_tramite']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tipo['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="id_estado_tramite">Estado Inicial *</label>
            <select name="id_estado_tramite" id="id_estado_tramite" required>
                <option value="">Seleccione un estado</option>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?= $estado['id_estado_tramite'] ?>" <?= ((int)($_POST['id_estado_tramite'] ?? 0) === (int)$estado['id_estado_tramite']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($estado['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <h3>Datos del Remitente</h3>

            <label for="tipo_persona">Tipo de Persona *</label>
            <select name="tipo_persona" id="tipo_persona" required>
                <option value="Natural" <?= ($_POST['tipo_persona'] ?? '') === 'Natural' ? 'selected' : '' ?>>Persona Natural</option>
                <option value="Jurídica" <?= ($_POST['tipo_persona'] ?? '') === 'Jurídica' ? 'selected' : '' ?>>Persona Jurídica</option>
            </select>

            <label for="nombre_remitente">Nombre / Razón Social *</label>
            <input type="text" name="nombre_remitente" id="nombre_remitente" required value="<?= htmlspecialchars($_POST['nombre_remitente'] ?? '') ?>">

            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">

            <label for="nit">NIT</label>
            <input type="text" name="nit" id="nit" value="<?= htmlspecialchars($_POST['nit'] ?? '') ?>">

            <label for="correo_remitente">Correo</label>
            <input type="email" name="correo_remitente" id="correo_remitente" value="<?= htmlspecialchars($_POST['correo_remitente'] ?? '') ?>">

            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">

            <label for="razon_social">Razón Social (solo personas jurídicas)</label>
            <input type="text" name="razon_social" id="razon_social" value="<?= htmlspecialchars($_POST['razon_social'] ?? '') ?>">

            <label>Áreas Asignadas</label>
            <div style="border: 1px solid #ddd; padding: 10px; border-radius: 6px; margin-bottom: 1rem;">
                <?php foreach ($areas as $area): ?>
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="checkbox" name="areas[]" value="<?= $area['id_area'] ?>"
                               <?= in_array($area['id_area'], $_POST['areas'] ?? []) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($area['nombre']) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-agregar">Crear Trámite</button>
            <a href="tramites.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>