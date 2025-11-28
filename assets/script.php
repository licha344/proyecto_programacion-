// script.js - pequeñas interacciones
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('toggleFormBtn');
    const form = document.getElementById('formContainer');
    const cancel = document.getElementById('cancelBtn');

    btn?.addEventListener('click', function(){ form.style.display = form.style.display === 'none' ? 'block' : 'none'; });
    cancel?.addEventListener('click', function(){ form.style.display = 'none'; });

    // validación simple cliente (opcional)
    const addForm = document.getElementById('addForm');
    addForm?.addEventListener('submit', function(e){
        // ejemplo: asegurar que el nombre no esté vacío
        const name = addForm.querySelector('input[name="name"]').value.trim();
        if (!name) {
            e.preventDefault();
            alert('Por favor ingresa un nombre.');
        }
    });
});
