function continuarPaso(idSiguiente) {
    // Paso 1 → Paso 2
    if (idSiguiente === 'tab-consulta') {
        const seleccionado = document.getElementById("pacienteSeleccionado")?.style.display;
        if (seleccionado === "none") {
            alert("⚠️ Primero debe seleccionar un paciente de la tabla.");
            return;
        }
    }

    // Paso 2 → Paso 3: exige diagnóstico de inicio
    if (idSiguiente === 'tab-clinica') {
        const diagnostico = document.getElementById("diagnosticoInicio")?.value.trim();
        if (!diagnostico) {
            alert("⚠️ Debe completar el 'Diagnóstico de Inicio' antes de continuar.");
            document.getElementById("diagnosticoInicio").focus();
            return;
        }
        desbloquearTab("btnTabClinica");
    }

    // Paso 3 → Paso 4: exige enfermedad actual
    if (idSiguiente === 'tab-tratamiento') {
        const enfermedad = document.getElementById("Enfermedad_Actual")?.value.trim();
        if (!enfermedad) {
            alert("⚠️ Debe describir la 'Enfermedad Actual' antes de continuar.");
            document.getElementById("Enfermedad_Actual").focus();
            return;
        }
        desbloquearTab("btnTabTratamiento");
    }

    mostrarTab(null, idSiguiente);
}