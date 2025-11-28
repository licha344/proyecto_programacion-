<?php
// index.php - Página principal
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/Student.php';

// Cargar base de datos desde date.json
$dataFile = __DIR__ . '/date.json';
if (file_exists($dataFile)) {
    $db = json_decode(file_get_contents($dataFile), true);
    // Validar estructura
    if (!isset($db['students']) || !isset($db['grades']) || !isset($db['subjects'])) {
        $db = getDefaultDatabase();
        file_put_contents($dataFile, json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
} else {
    $db = getDefaultDatabase();
    file_put_contents($dataFile, json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

include __DIR__ . '/includes/header.php';
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Panel de Notas y Calificaciones</h1>
        <div>
            <button id="toggleFormBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar Alumno / Notas
            </button>
        </div>
    </div>
    
    <!-- Mensajes -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?= isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'info' ?> alert-dismissible fade show mt-3">
            <?= htmlspecialchars($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Formulario (inicialmente oculto) -->
    <div id="formContainer" class="card card-body mt-3" style="display:none;">
        <form action="process.php" method="post" id="addForm">
            <h5>Agregar alumno y sus notas</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Edad *</label>
                    <input type="number" name="age" class="form-control" min="1" max="100" required>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="active" value="1" class="form-check-input" id="activeCheck" checked>
                        <label class="form-check-label" for="activeCheck">Activo</label>
                    </div>
                </div>
            </div>

            <hr>
            <h6>Notas por materia *</h6>
            <div class="row">
                <?php foreach ($db['subjects'] as $subId => $subName): ?>
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><?= htmlspecialchars($subName) ?> *</label>
                        <input type="number" name="grades[<?= $subId ?>]" class="form-control" 
                               min="0" max="10" step="0.1" required>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar
                </button>
                <button type="button" id="cancelBtn" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de resultados -->
    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Alumno</th>
                    <?php foreach ($db['subjects'] as $sub): ?>
                        <th><?= htmlspecialchars($sub) ?></th>
                    <?php endforeach; ?>
                    <th>Promedio</th>
                    <th>Edad</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($db['students'])): ?>
                    <tr>
                        <td colspan="<?= count($db['subjects']) + 4 ?>" class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                            <p>No hay estudiantes registrados. ¡Agrega el primero!</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($db['students'] as $stuId => $stu): ?>
                        <?php
                        $grades = isset($db['grades'][$stuId]) ? $db['grades'][$stuId] : [];
                        $avg = count($grades) ? promedio($grades) : 0;
                        $colorClass = $avg >= 7 ? 'text-success' : ($avg >= 6 ? 'text-warning' : 'text-danger');
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($stu['name']) ?></strong></td>
                            <?php foreach ($db['subjects'] as $subId => $subName): ?>
                                <td><?= isset($grades[$subId]) ? number_format($grades[$subId], 1) : '-' ?></td>
                            <?php endforeach; ?>
                            <td><strong class="<?= $colorClass ?>"><?= number_format($avg, 2) ?></strong></td>
                            <td><?= (int)$stu['age'] ?></td>
                            <td>
                                <span class="badge <?= $stu['active'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $stu['active'] ? 'Sí' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <a href="delete.php?id=<?= $stuId ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('¿Estás seguro de eliminar este estudiante?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Estadísticas -->
    <div class="row mt-4">
        <?php
        $totalEstudiantes = count($db['students']);
        $estudiantesActivos = count(array_filter($db['students'], fn($s) => $s['active']));
        $promedioGeneral = 0;
        if ($totalEstudiantes > 0) {
            $sumaPromedios = 0;
            foreach ($db['students'] as $id => $stu) {
                $grades = isset($db['grades'][$id]) ? $db['grades'][$id] : [];
                $sumaPromedios += count($grades) ? promedio($grades) : 0;
            }
            $promedioGeneral = $sumaPromedios / $totalEstudiantes;
        }
        ?>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Estudiantes</h5>
                    <h2 class="text-primary"><?= $totalEstudiantes ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Promedio General</h5>
                    <h2 class="text-success"><?= number_format($promedioGeneral, 2) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Estudiantes Activos</h5>
                    <h2 class="text-info"><?= $estudiantesActivos ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Ejemplo de POO -->
    <div class="mt-4">
        <h4>Ejemplo POO</h4>
        <?php
        $s = new Student('Sofía Díaz', 18, true);
        echo '<p><strong>Nombre (get):</strong> ' . htmlspecialchars($s->getName()) . '</p>';
        echo '<p><strong>Descripción (método):</strong> ' . htmlspecialchars($s->describe()) . '</p>';
        echo '<p><strong>Contador estático (Student::$count):</strong> ' . Student::$count . '</p>';
        ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>