<?php
// process.php - Procesar formulario de agregar estudiante
require_once __DIR__ . '/includes/functions.php';

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Sanitizar y recoger datos
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
$active = isset($_POST['active']) && $_POST['active'] == '1';
$grades = $_POST['grades'] ?? [];

// Cargar archivo de datos
$dataFile = __DIR__ . '/date.json';
if (!file_exists($dataFile)) {
    $db = getDefaultDatabase();
} else {
    $db = json_decode(file_get_contents($dataFile), true);
    if (!$db) {
        $db = getDefaultDatabase();
    }
}

// Validar datos
$errors = validar_post([
    'name' => $name,
    'age' => $age,
    'grades' => $grades
], $db['subjects']);

if (!empty($errors)) {
    $msg = implode(' | ', $errors);
    header('Location: index.php?msg=' . urlencode($msg) . '&type=danger');
    exit;
}

// Sanitizar notas
$gradesClean = [];
foreach ($db['subjects'] as $sid => $sName) {
    $val = isset($grades[$sid]) ? (float)$grades[$sid] : 0;
    // Validar rango
    if ($val < 0 || $val > 10) {
        header('Location: index.php?msg=' . urlencode('Las notas deben estar entre 0 y 10') . '&type=danger');
        exit;
    }
    $gradesClean[$sid] = $val;
}

// Agregar estudiante
$result = add_student_to_db($name, (int)$age, $active, $gradesClean);

if ($result) {
    header('Location: index.php?msg=' . urlencode('¡Alumno agregado correctamente!') . '&type=success');
} else {
    header('Location: index.php?msg=' . urlencode('Error al guardar el alumno') . '&type=danger');
}
exit;