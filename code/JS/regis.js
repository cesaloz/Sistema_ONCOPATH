/* ===== GUARDAR PACIENTE EN LA BD ===== */
async function guardarPaciente() {
    const form = document.getElementById("formPaciente");
    if (!form) return;

    // Validación visual (por si se llama directo sin pasar por submit)
    if (typeof validarFormularioCompleto === 'function') {
        const ok = validarFormularioCompleto();
        if (!ok) return;
    }

    // === ENVIAR AL SERVIDOR ===
    const formData = new FormData(form);

    const btnGuardar = form.querySelector('button[type="submit"]');
    if (btnGuardar) {
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    }

    try {
        const respuesta = await fetch("../user_confic.php", {
            method: "POST",
            body: formData
        });

        const data = await respuesta.json();

        if (data.success) {
            // Toast de éxito
            mostrarMensajeGlobal(
                `✅ Paciente registrado correctamente. ID: ${data.id_paciente}`,
                "success"
            );
            // Redirigir después de mostrar el mensaje
            setTimeout(() => {
                window.location.href = "../Pacientes.php?registro=ok&id=" + data.id_paciente;
            }, 1800);
        } else {
            mostrarMensajeGlobal(data.message || "Error al guardar", "error");
        }
    } catch (error) {
        console.error("Error detallado:", error);
        mostrarMensajeGlobal("Error de conexión. Revise la consola (F12).", "error");
    } finally {
        if (btnGuardar) {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar Paciente';
        }
    }
}

/* ===== CALCULAR EDAD ===== */
function calcularEdad() {
    const fechaNac = document.getElementById("fechaNacimiento")?.value;
    if (!fechaNac) return;

    const nacimiento = new Date(fechaNac);
    const hoy = new Date();

    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const mes = hoy.getMonth() - nacimiento.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) edad--;

    const campoEdad = document.getElementById("edad");
    if (campoEdad) campoEdad.value = edad;
}

/* ===== TOAST GLOBAL ===== */
function mostrarMensajeGlobal(mensaje, tipo = "info") {
    let toast = document.getElementById("toast-global");
    if (!toast) {
        toast = document.createElement("div");
        toast.id = "toast-global";
        document.body.appendChild(toast);
    }
    toast.textContent = mensaje;
    toast.className = "toast-visible " + tipo;
    setTimeout(() => {
        toast.className = "";
    }, 4000);
}