<?php
//Calcular promedio de notas

function promedio(array $notas): float {
    $cantidad = count($notas);
    if ($cantidad === 0) return 0.0;
    return array_sum($notas) / $cantidad;
}

// Obtener base de datos por defecto
function getDefaultDatabase(): array {
    return [
        'students' => [
            1 => ['name' => 'Angel Cortes', 'age' => 16, 'active' => true],
            2 => ['name' => 'Lisandro Villagra', 'age' => 16, 'active' => true],
        ],
        'grades' => [
            1 => [1 => 8, 2 => 7, 3 => 9],
            2 => [1 => 6, 2 => 9, 3 => 7],
        ],
        'subjects' => [
            1 => 'Matemática',
            2 => 'Lengua',
            3 => 'Historia'
        ],
    ];
}

// Agregar estudiante a la base de datos
function add_student_to_db(string $name, int $age, bool $active, array $grades): bool {
    $file = __DIR__ . '/../date.json';
    
    // Cargar o crear base de datos
    if (!file_exists($file)) {
        $db = getDefaultDatabase();
    } else {
        $db = json_decode(file_get_contents($file), true);
        if (!is_array($db) || !isset($db['students'])) {
            $db = getDefaultDatabase();
        }
    }

    // Generar nuevo ID
    $ids = array_keys($db['students']);
    $newId = empty($ids) ? 1 : max($ids) + 1;

    // Sanitizar y agregar
    $safeName = trim($name);
    $db['students'][$newId] = [
        'name' => $safeName,
        'age' => $age,
        'active' => $active
    ];
    $db['grades'][$newId] = $grades;

    // Guardar con bloqueo
    $json = json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

//Validar datos del formulario POST
// Retorna array con errores
 
function validar_post(array $post, array $expectedSubjects): array {
    $errors = [];

    // Validar nombre
    if (empty(trim($post['name'] ?? ''))) {
        $errors[] = 'El nombre es requerido.';
    } elseif (strlen($post['name']) > 100) {
        $errors[] = 'El nombre es demasiado largo (máximo 100 caracteres).';
    }

    // Validar edad
    $age = isset($post['age']) ? (int)$post['age'] : 0;
    if ($age <= 0) {
        $errors[] = 'La edad debe ser mayor a 0.';
    } elseif ($age > 100) {
        $errors[] = 'La edad debe ser menor a 100.';
    }

    // Validar notas
    $grades = $post['grades'] ?? [];
    foreach ($expectedSubjects as $sid => $sName) {
        if (!isset($grades[$sid]) || $grades[$sid] === '') {
            $errors[] = "Falta la nota de $sName.";
        } else {
            $nota = $grades[$sid];
            if (!is_numeric($nota)) {
                $errors[] = "La nota de $sName debe ser numérica.";
            } elseif ($nota < 0 || $nota > 10) {
                $errors[] = "La nota de $sName debe estar entre 0 y 10.";
            }
        }
    }

    return $errors;
}

// Agregar nota a un estudiante existente
function agregarNota(&$estudiantes, $idEstudiante, $materia, $nota) {
    if (!isset($estudiantes[$idEstudiante])) {
        return "Error: el estudiante no existe.";
    }

    if (!isset($estudiantes[$idEstudiante]['notas'])) {
        $estudiantes[$idEstudiante]['notas'] = [];
    }

    $estudiantes[$idEstudiante]['notas'][] = [
        'materia' => $materia,
        'nota' => $nota
    ];

    return "Nota agregada correctamente.";
}