<?php
// Eliminar estudiante
require_once __DIR__ . '/includes/functions.php';

// Verificar que se recibió un ID
if (!isset($_GET['id'])) {
    header('Location: index.php?msg=' . urlencode('ID no proporcionado') . '&type=danger');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php?msg=' . urlencode('ID inválido') . '&type=danger');
    exit;
}

// Cargar datos
$dataFile = __DIR__ . '/date.json';
if (!file_exists($dataFile)) {
    header('Location: index.php?msg=' . urlencode('Base de datos no encontrada') . '&type=danger');
    exit;
}

$db = json_decode(file_get_contents($dataFile), true);

// Verificar que el estudiante
if (!isset($db['students'][$id])) {
    header('Location: index.php?msg=' . urlencode('Estudiante no encontrado') . '&type=danger');
    exit;
}

        // Guardar nombre 
$studentName = $db['students'][$id]['name'];

        // Eliminar estudiante y sus notas
unset($db['students'][$id]);
unset($db['grades'][$id]);

        // Guardar cambios
$json = json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$saved = file_put_contents($dataFile, $json, LOCK_EX);

if ($saved !== false) {
    header('Location: index.php?msg=' . urlencode("Estudiante '$studentName' eliminado correctamente") . '&type=success');
} else {
    header('Location: index.php?msg=' . urlencode('Error al eliminar el estudiante') . '&type=danger');
}
exit;