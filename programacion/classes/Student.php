<?php
class Student {
    private string $name;
    private int $age;
    private bool $active;

    // Propiedad estática (contador de instancias)
    public static int $count = 0;

    //constructor
    public function __construct(string $name, int $age, bool $active = true) {
        $this->name = $name;
        $this->age = $age;
        $this->active = $active;
        self::$count++; // Incrementar contador estático
    }

    // getters
    
    public function getName(): string {
        return $this->name;
    }

    public function getAge(): int {
        return $this->age;
    }

    public function isActive(): bool {
        return $this->active;
    }

    // setters
    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setAge(int $age): void {
        if ($age > 0 && $age < 100) {
            $this->age = $age;
        }
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

        // metodo de distancia
    
    //devuelve descripcion al estudiante
    public function describe(): string {
        $estado = $this->active ? 'activo' : 'inactivo';
        return "{$this->name}, {$this->age} años, {$estado}";
    }

    /**
     * Verificar si es mayor de edad
     */
    public function esMayorDeEdad(): bool {
        return $this->age >= 18;
    }

    //metodo estatico 
    
    //obtener la info
    public static function staticInfo(): string {
        return "Total de estudiantes creados: " . self::$count;
    }

    //resetea 
    public static function resetCount(): void {
        self::$count = 0;
    }
}