// assets/script.js - Interacciones del cliente

document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleFormBtn');
    const formContainer = document.getElementById('formContainer');
    const cancelBtn = document.getElementById('cancelBtn');
    const addForm = document.getElementById('addForm');

    // Toggle del formulario
    if (toggleBtn && formContainer) {
        toggleBtn.addEventListener('click', function() {
            if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                formContainer.style.display = 'block';
                toggleBtn.innerHTML = '<i class="fas fa-minus"></i> Ocultar Formulario';
                // Scroll suave al formulario
                formContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                formContainer.style.display = 'none';
                toggleBtn.innerHTML = '<i class="fas fa-plus"></i> Agregar Alumno / Notas';
            }
        });
    }

    // Botón cancelar
    if (cancelBtn && formContainer && addForm) {
        cancelBtn.addEventListener('click', function() {
            formContainer.style.display = 'none';
            if (toggleBtn) {
                toggleBtn.innerHTML = '<i class="fas fa-plus"></i> Agregar Alumno / Notas';
            }
            addForm.reset();
        });
    }

    // Validación del formulario (lado del cliente)
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            const nameInput = addForm.querySelector('input[name="name"]');
            const ageInput = addForm.querySelector('input[name="age"]');
            const gradeInputs = addForm.querySelectorAll('input[name^="grades"]');
            
            let errors = [];

            // Validar nombre
            if (!nameInput.value.trim()) {
                errors.push('El nombre es requerido');
                nameInput.classList.add('is-invalid');
            } else {
                nameInput.classList.remove('is-invalid');
            }

            // Validar edad
            const age = parseInt(ageInput.value);
            if (!age || age <= 0 || age > 100) {
                errors.push('La edad debe estar entre 1 y 100');
                ageInput.classList.add('is-invalid');
            } else {
                ageInput.classList.remove('is-invalid');
            }

            // Validar notas
            let notasInvalidas = false;
            gradeInputs.forEach(function(input) {
                const value = parseFloat(input.value);
                if (input.value === '' || isNaN(value) || value < 0 || value > 10) {
                    input.classList.add('is-invalid');
                    notasInvalidas = true;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (notasInvalidas) {
                errors.push('Todas las notas deben estar entre 0 y 10');
            }

            // Si hay errores, prevenir envío
            if (errors.length > 0) {
                e.preventDefault();
                alert('Por favor corrige los siguientes errores:\n\n• ' + errors.join('\n• '));
                return false;
            }
        });

        // Limpiar validación al escribir
        const allInputs = addForm.querySelectorAll('input');
        allInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    }

    // Auto-cerrar alertas después de 5 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});