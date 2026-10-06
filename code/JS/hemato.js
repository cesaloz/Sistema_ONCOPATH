function mostrarTab(e, id) {
    if (e) e.preventDefault();

    if (id === "tab-consulta" && document.getElementById("btnTabConsulta")?.disabled) {
        alert("⚠️ Primero debe seleccionar un paciente de la tabla.");
        return;
    }
    if (id === "tab-tratamiento" && document.getElementById("btnTabTratamiento")?.disabled) {
        alert("⚠️ Primero complete la Consulta y Diagnóstico.");
        return;
    }

    document.querySelectorAll(".tab-content").forEach(t => t.classList.remove("active"));
    document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));

    const tab = document.getElementById(id);
    if (tab) tab.classList.add("active");

    const boton = document.querySelector(`.tab-btn[onclick*="${id}"]`);
    if (boton) boton.classList.add("active");

    window.scrollTo({ top: 0, behavior: "smooth" });
}

/* ===== INTENTAR IR A TAB BLOQUEADA ===== */
function intentarIrATab(id) {
    const boton = document.querySelector(`.tab-btn[onclick*="${id}"]`);
    if (boton && boton.disabled) {
        alert("🔒 Esta sección está bloqueada. Complete los pasos anteriores.");
        return;
    }
    mostrarTab(null, id);
}

/* ===== DESBLOQUEAR TAB ===== */
function desbloquearTab(idBoton) {
    const boton = document.getElementById(idBoton);
    if (!boton) return;

    boton.disabled = false;
    boton.classList.remove("locked");

    const candado = boton.querySelector(".fa-lock");
    if (candado) candado.remove();
}

/* ===== BLOQUEAR TAB ===== */
function bloquearTab(idBoton) {
    const boton = document.getElementById(idBoton);
    if (!boton) return;

    boton.disabled = true;
    boton.classList.add("locked");

    if (!boton.querySelector(".fa-lock")) {
        const candado = document.createElement("i");
        candado.className = "fas fa-lock";
        candado.style.fontSize = "0.7rem";
        candado.style.marginLeft = "0.3rem";
        boton.appendChild(candado);
    }
}

/* ===== CONTINUAR ENTRE PASOS ===== */
function continuarPaso(idSiguiente) {
    // Paso 1 → Paso 2
    if (idSiguiente === 'tab-consulta') {
        const seleccionado = document.getElementById("pacienteSeleccionado").style.display;
        if (seleccionado === "none") {
            alert("⚠️ Primero debe seleccionar un paciente de la tabla.");
            return;
        }
    }

    // Paso 2 → Paso 3
    if (idSiguiente === 'tab-tratamiento') {
        const diagnostico = document.getElementById("diagnosticoInicio").value.trim();
        if (!diagnostico) {
            alert("⚠️ Debe completar al menos el 'Diagnóstico de Inicio' antes de continuar.");
            document.getElementById("diagnosticoInicio").focus();
            return;
        }
        desbloquearTab("btnTabTratamiento");
    }

    mostrarTab(null, idSiguiente);
}

/* ===== SELECCIONAR PACIENTE ===== */
function seleccionarPaciente(boton) {
    const fila = boton.closest("tr");
    const celdas = fila.cells;

    const id = celdas[0].textContent;
    const cedula = celdas[1].textContent;
    const nombre = celdas[2].textContent;

    // Marcar fila
    document.querySelectorAll("#tablaPacientes tbody tr").forEach(tr => tr.classList.remove("selected"));
    fila.classList.add("selected");

    // Mostrar info del seleccionado
    const info = document.getElementById("pacienteSeleccionado");
    document.getElementById("nombreSeleccionado").textContent = `${nombre} (Céd. ${cedula}) - ${id}`;
    info.style.display = "flex";

    // Guardar en input oculto
    let hidden = document.getElementById("pacienteIdSeleccionado");
    if (!hidden) {
        hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.id = "pacienteIdSeleccionado";
        hidden.name = "pacienteIdSeleccionado";
        document.getElementById("formPaciente").appendChild(hidden);
    }
    hidden.value = id.replace("#", "");

    // Desbloquear pestaña 2
    desbloquearTab("btnTabConsulta");
}

/* ===== FILTRAR PACIENTES ===== */
function filtrarPacientes() {
    const texto = document.getElementById("buscarPaciente").value.toLowerCase();
    const filas = document.querySelectorAll("#tablaPacientes tbody tr");

    filas.forEach(fila => {
        const celdas = fila.cells;
        const cedula = celdas[1].textContent.toLowerCase();
        const nombre = celdas[2].textContent.toLowerCase();
        const id = celdas[0].textContent.toLowerCase();

        const coincide = cedula.includes(texto) || nombre.includes(texto) || id.includes(texto);
        fila.style.display = coincide ? "" : "none";
    });
}

//* ===== GUARDAR HISTORIA CLÍNICA ===== */
async function guardarPaciente() {
    const form = document.getElementById("formPaciente");
    if (!form) return;

    // 
    const pacienteId = document.getElementById("pacienteIdSeleccionado")?.value;
    if (!pacienteId) {
        alert("⚠️ Debe seleccionar un paciente primero.");
        return;
    }

    const diagnostico = document.getElementById("diagnosticoInicio")?.value.trim();
    if (!diagnostico) {
        alert("⚠️ Debe completar el Diagnóstico de Inicio.");
        mostrarTab(null, "tab-consulta");
        return;
    }

    const enfermedad = document.getElementById("Enfermedad_Actual")?.value.trim();
    if (!enfermedad) {
        alert("⚠️ Debe describir la Enfermedad Actual.");
        mostrarTab(null, "tab-clinica");
        return;
    }

    const formData = new FormData(form);
    
    formData.set("pacienteIdSeleccionado", pacienteId);

    const btnGuardar = form.querySelector('button[type="submit"]');
    if (btnGuardar) {
        btnGuardar.disabled = true;
        btnGuardar.textContent = "Guardando...";
    }

    try {
        const respuesta = await fetch("../regis_HCHemato.php", {
            method: "POST",
            body: formData
        });

        const data = await respuesta.json();

        if (data.success) {
            alert(`✅ Historia Clínica guardada correctamente.\nID Historia: ${data.id_historia}`);
            window.location.href = "HCHemato.php";
        } else {
            alert(`❌ Error: ${data.message}`);
        }
    } catch (error) {
        console.error("Error detallado:", error);
        alert("❌ Error de conexión. Revise la consola (F12).");
    } finally {
        if (btnGuardar) {
            btnGuardar.disabled = false;
            btnGuardar.textContent = "Guardar Historia";
        }
    }
}