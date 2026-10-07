/* =========================================================
   VALIDACIÓN VISUAL DE CAMPOS OBLIGATORIOS + PRIORIDAD
   ========================================================= */

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('formPaciente');
    if (!form) return;

    // Quitamos cualquier onsubmit inline
    form.removeAttribute('onsubmit');

    // --- 1. Crear spans de error automáticamente ---
    const campos = form.querySelectorAll('input[required], select[required], textarea[required]');

    campos.forEach(campo => {
        if (campo.type === 'radio') {
            const grupo = campo.closest('.radio-group') || campo.closest('.checkbox-group');
            if (grupo && !grupo.parentNode.querySelector('.mensaje-error-grupo')) {
                const span = document.createElement('span');
                span.className = 'mensaje-error mensaje-error-grupo';   // 👈 clase específica
                grupo.parentNode.insertBefore(span, grupo.nextSibling);
            }
        } else {
            const grupo = campo.closest('.form-group');
            if (grupo && !grupo.querySelector('.mensaje-error')) {
                const span = document.createElement('span');
                span.className = 'mensaje-error';
                grupo.appendChild(span);
            }
        }
    });

    // --- 2. Validar un campo individual ---
    function validarCampo(campo) {
        let esValido = true;
        let mensaje = '';

        // Radios
        if (campo.type === 'radio') {
            const nombre = campo.name;
            const seleccionado = form.querySelector(`input[name="${nombre}"]:checked`);
            const grupo = campo.closest('.radio-group') || campo.closest('.checkbox-group');
            const span = grupo?.parentNode?.querySelector('.mensaje-error-grupo');   // 👈 clase específica

            if (!seleccionado) {
                esValido = false;
                mensaje = 'Seleccione una opción';
                if (grupo) grupo.classList.add('error');
                if (span) span.textContent = mensaje;
            } else {
                if (grupo) grupo.classList.remove('error');
                if (span) span.textContent = '';
            }
            return esValido;
        }

        // Inputs, selects, textareas
        const valor = (campo.value || '').trim();
        const grupo = campo.closest('.form-group');
        const span = grupo?.querySelector('.mensaje-error');

        if (campo.hasAttribute('required') && valor === '') {
            esValido = false;
            mensaje = 'Este campo es obligatorio';
        }
        // Validaciones extra por tipo
        else if (campo.type === 'email' && valor !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)) {
            esValido = false;
            mensaje = 'Correo no válido';
        }
        else if (campo.id === 'cedula' && valor !== '' && !/^\d{7,8}$/.test(valor)) {
            esValido = false;
            mensaje = 'Debe tener 7 u 8 dígitos';
        }
        else if ((campo.id === 'telefono' || campo.id === 'telefono2' || campo.id === 'telefonoHabitacion')
                 && valor !== '' && !/^\d{10,11}$/.test(valor)) {
            esValido = false;
            mensaje = 'Debe tener 10 u 11 dígitos';
        }

        if (!esValido) {
            campo.classList.add('error');
            campo.classList.remove('valido');
            if (span) span.textContent = mensaje;
        } else {
            campo.classList.remove('error');
            if (valor !== '') campo.classList.add('valido');
            if (span) span.textContent = '';
        }

        return esValido;
    }

    // --- 3. Listeners por campo ---
    campos.forEach(campo => {
        if (campo.type === 'radio') {
            campo.addEventListener('change', () => validarCampo(campo));
        } else {
            campo.addEventListener('blur', () => validarCampo(campo));
            campo.addEventListener('input', () => {
                if (campo.value.trim() !== '') {
                    campo.classList.remove('error');
                    campo.classList.add('valido');
                    const span = campo.closest('.form-group')?.querySelector('.mensaje-error');
                    if (span) span.textContent = '';
                }
            });
        }
    });

    // --- 4. Función global de validación completa (con prioridad) ---
    window.validarFormularioCompleto = function () {
        let todoValido = true;
        let primerError = null;

        const todos = Array.from(campos);
        const prioritarios = todos.filter(c => c.closest('.form-group.prioridad'));
        const normales = todos.filter(c => !c.closest('.form-group.prioridad'));
        const ordenados = [...prioritarios, ...normales];

        const camposUnicos = new Set();

        ordenados.forEach(campo => {
            if (campo.type === 'radio') {
                if (camposUnicos.has(campo.name)) return;
                camposUnicos.add(campo.name);
            }
            if (!validarCampo(campo)) {
                todoValido = false;
                if (!primerError) primerError = campo;
            }
        });

        if (!todoValido && primerError) {
            // Asegurar que la pestaña correcta esté visible
            const tab = primerError.closest('.tab-content');
            if (tab) {
                document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                tab.classList.add('active');
                const btn = document.querySelector(`.tab-btn[onclick*="${tab.id}"]`);
                if (btn) btn.classList.add('active');
            }
            primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            primerError.focus({ preventScroll: true });
        }

        return todoValido;
    };

    // --- 5. Submit manejado aquí ---
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (window.validarFormularioCompleto()) {
            if (typeof guardarPaciente === 'function') {
                guardarPaciente();
            }
        }
    });
});

/* ===== LIMPIAR FORMULARIO (global) ===== */
function limpiarFormulario() {
    const form = document.getElementById('formPaciente');
    if (!form) return;
    form.reset();
    form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
    form.querySelectorAll('.valido').forEach(el => el.classList.remove('valido'));
    form.querySelectorAll('.mensaje-error').forEach(el => el.textContent = '');
    form.querySelectorAll('.mensaje-error-grupo').forEach(el => el.textContent = '');
}