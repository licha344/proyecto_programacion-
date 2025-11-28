<?php
// classes/Student.php - clase ejemplo POO
class Student {
    private string $name;
    private int $age;
    private bool $active;

    // propiedad estática (ejemplo de contador)
    public static int $count = 0;

    public function __construct(string $name, int $age, bool $active = true) {
        $this->name = $name;
        $this->age = $age;
        $this->active = $active;
        self::$count++; // incrementar contador estático
    }

    // getters y setters
    public function getName(): string { return $this->name; }
    public function setName(string $n) { $this->name = $n; }

    public function getAge(): int { return $this->age; }
    public function setAge(int $a) { $this->age = $a; }

    public function isActive(): bool { return $this->active; }
    public function setActive(bool $v) { $this->active = $v; }

    // método de instancia
    public function describe(): string {
        $estado = $this->active ? 'activo' : 'inactivo';
        return "{$this->name}, {$this->age} años, {$estado}";
    }

    // método estático
    public static function staticInfo(): string {
        return "Estudiantes creados: " . self::$count;
    }
}
