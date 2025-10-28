<?php
require '../../../config/database/config/config_db.php';

$id_tramite = filter_var($_GET['id_tramite'] ?? 0, FILTER_VALIDATE_INT);
if (!$id_tramite) {
    die('ID de trámite no válido');
}

// Verificar que el trámite existe
$stmt = $mysqli->prepare("SELECT asunto FROM tbl_tramite WHERE id_tramite = ?");
$stmt->bind_param("i", $id_tramite);
$stmt->execute();
$tramite = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tramite) {
    die('Trámite no encontrado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $detalle = trim($_POST['detalle']);
    $documento_adjunto = '';

    // Manejar subida de archivo
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../../uploads/tramites/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = uniqid() . '_' . basename($_FILES['documento']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['documento']['tmp_name'], $file_path)) {
            $documento_adjunto = 'tramites/' . $file_name;
        }
    }

    if (empty($detalle)) {
        $error = 'El detalle del seguimiento es obligatorio.';
    } else {
        $stmt = $mysqli->prepare("
            INSERT INTO tbl_seguimiento (id_tramite, detalle, documento_adjunto, id_usuario_seguimiento)
            VALUES (?, ?, ?, ?)
        ");
        $id_usuario = $_SESSION['usuario_id'] ?? 1;
        $stmt->bind_param("issi", $id_tramite, $detalle, $documento_adjunto, $id_usuario);

        if ($stmt->execute()) {
            header("Location: ver_tramite.php?id_tramite=$id_tramite&mensaje=Seguimiento agregado correctamente");
            exit;
        } else {
            $error = 'Error al agregar seguimiento: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Seguimiento - Trámite #<?= $id_tramite ?></title>
    <link rel="stylesheet" href="/style/areas.css">
</head>
<body>
    <?php include '../../sidebaradministrador.php'; ?>

    <div class="contenido-principal">
        <h1>Agregar Seguimiento</h1>
        <p><strong>Trámite:</strong> #<?= $id_tramite ?> - <?= htmlspecialchars($tramite['asunto']) ?></p>

        <?php if (isset($error)): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="formulario">
            <label for="detalle">Detalle del Seguimiento *</label>
            <textarea name="detalle" id="detalle" rows="6" required placeholder="Describa las acciones realizadas, observaciones, cambios de estado, etc."><?= htmlspecialchars($_POST['detalle'] ?? '') ?></textarea>

            <label for="documento">Documento Adjunto (opcional)</label>
            <input type="file" name="documento" id="documento" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">

            <button type="submit" class="btn-agregar">Agregar Seguimiento</button>
            <a href="ver_tramite.php?id_tramite=<?= $id_tramite ?>" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>