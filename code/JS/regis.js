/* ===== GUARDAR PACIENTE EN LA BD ===== */
async function guardarPaciente() {
    const form = document.getElementById("formPaciente");
    if (!form) return;

    // Validaciones básicas
    const obligatorios = [
        { id: "institucion",     nombre: "Institución de Adscripción" },
        { id: "establecimiento", nombre: "Nombre del Establecimiento" },
        { id: "fechaReferencia", nombre: "Fecha de la Referencia" },
        { id: "fecha",           nombre: "Fecha del Registro" },
        { id: "numeroHistoria",  nombre: "Número de Historia" },
        { id: "nombres",         nombre: "Nombres" },
        { id: "apellidos",       nombre: "Apellidos" },
        { id: "cedula",          nombre: "Cédula" },
        { id: "fechaNacimiento", nombre: "Fecha de Nacimiento" },
        { id: "edad",            nombre: "Edad" },
        { id: "raza",            nombre: "Raza" }
    ];

    for (const campo of obligatorios) {
        const el = document.getElementById(campo.id);
        if (!el) continue;
        if (!el.value || el.value.trim() === "") {
            mostrarTab(null, "datos-personales");
            el.style.borderColor = "#dc2626";
            el.focus();
            alert(`⚠️ Falta completar: ${campo.nombre}`);
            return;
        }
    }

    const tipoDoc = form.querySelector('input[name="tipoDocumento"]:checked');
    if (!tipoDoc) {
        alert("⚠️ Debe seleccionar el Tipo de Documento (V / E / J).");
        return;
    }

    const sexo = form.querySelector('input[name="sexo"]:checked');
    if (!sexo) {
        alert("⚠️ Debe seleccionar el Sexo del paciente.");
        return;
    }

    // === ENVIAR AL SERVIDOR ===
    const formData = new FormData(form);

    // Deshabilitar botón
    const btnGuardar = form.querySelector('button[type="submit"]');
    if (btnGuardar) {
        btnGuardar.disabled = true;
        btnGuardar.textContent = "Guardando...";
    }

    try {
        const respuesta = await fetch("../user_confic.php", {
            method: "POST",
            body: formData
        });

        const data = await respuesta.json();

        if (data.success) {
            alert(`✅ Paciente registrado correctamente.\nID: ${data.id_paciente}`);
            window.location.href = "../Pacientes.php";
        } else {
            alert(`❌ Error: ${data.message}`);
        }
    } catch (error) {
        console.error("Error detallado:", error);
        alert("❌ Error de conexión. Revise la consola (F12).");
    } finally {
        if (btnGuardar) {
            btnGuardar.disabled = false;
            btnGuardar.textContent = "Guardar Paciente";
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

