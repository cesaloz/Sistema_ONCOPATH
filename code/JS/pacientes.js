/* ===== CONSTANTE BASE_URL ===== */
const BASE_URL = "/code";
/* ===== VER PACIENTE ===== */
function verPaciente(nombre) {
    alert(`👁️ Viendo ficha de: ${nombre}`);
    // window.location.href = `ver-paciente.html?nombre=${nombre}`;
}

/* ===== EDITAR PACIENTE ===== */
function editarPaciente(nombre) {
    alert(`✏️ Editando a: ${nombre}`);
    // window.location.href = `editar-paciente.html?nombre=${nombre}`;
}

/* ===== ELIMINAR PACIENTE ===== */
function eliminarPaciente(boton) {
    const fila = boton.closest("tr");
    const nombre = fila.cells[1].textContent;

    if (confirm(`¿Estás seguro de eliminar a "${nombre}"?`)) {
        fila.remove();
        actualizarContador();
    }
}

/* ===== FILTRAR TABLA ===== */
function filtrarTabla() {
    const texto = document.getElementById("buscador").value.toLowerCase();
    const estadoFiltro = document.getElementById("filtroEstado").value;
    const fechaFiltro = document.getElementById("filtroFecha").value;

    const filas = document.querySelectorAll("#tablaPacientes tbody tr");
    let visibles = 0;

    filas.forEach(fila => {
        const celdas = fila.cells;
        const nombre = celdas[1].textContent.toLowerCase();
        const cedula = celdas[0].textContent.toLowerCase();
        const diagnostico = celdas[3].textContent.toLowerCase();
        const estado = celdas[5].textContent.trim();
        const fecha = celdas[4].textContent;

        const coincideTexto = nombre.includes(texto) || cedula.includes(texto) || diagnostico.includes(texto);

        
        const coincideEstado = !estadoFiltro || estado === estadoFiltro;
        
        let coincideFecha = true;
            if (fechaFiltro) {
                const [year, month, day] = fechaFiltro.split("-");
                const fechaFormateada = `${day}/${month}/${year}`;
                
                coincideFecha = (fecha === fechaFormateada);
            }

        if (coincideTexto && coincideEstado && coincideFecha) {
            fila.style.display = "";
            visibles++;
        } else {
            fila.style.display = "none";
        }
    });

    document.getElementById("resultados").textContent = `Mostrando ${visibles} paciente(s)`;
    document.getElementById("emptyState").style.display = visibles === 0 ? "block" : "none";
}

/* ===== LIMPIAR FILTROS ===== */
function limpiarFiltros() {
    document.getElementById("buscador").value = "";
    document.getElementById("filtroEstado").value = "";
    document.getElementById("filtroFecha").value = "";
    filtrarTabla();
}

/* ===== ACTUALIZAR CONTADOR ===== */
function actualizarContador() {
    const total = document.querySelectorAll("#tablaPacientes tbody tr").length;
    document.getElementById("contador").textContent = total;
}

function agregarPaciente() {
    window.location.href = "historias/Registro_Paciente.php";
}
















/* ===================================================================
   MODAL DE DATOS DEL PACIENTE
   =================================================================== */

/* ===== ABRIR MODAL Y CARGAR DATOS ===== */
async function abrirModalPaciente(idPaciente) {
    const modal = document.getElementById("modalVerPaciente");
    modal.classList.add("active");

    // Mostrar "Cargando..." temporal
    document.getElementById("modalNombre").textContent = "Cargando...";
    document.getElementById("modalCedula").textContent = "—";

    try {
        const res = await fetch(`${BASE_URL}/api_obtener_paciente.php?id=${idPaciente}`);

        if (!res.ok) {
            throw new Error(`Error HTTP ${res.status}`);
        }

        const p = await res.json();

        if (p.error) {
            throw new Error(p.error);
        }

        // ===== LLENAR EL MODAL =====
        // Encabezado
        document.getElementById("modalNombre").textContent = p.nombre_completo || "—";
        document.getElementById("modalCedula").textContent = "Cédula: " + (p.cedula || "—");

        // Datos personales
        document.getElementById("modalHistoria").textContent = p.no_historia || "—";
        document.getElementById("modalCedulaField").textContent = p.cedula || "—";
        document.getElementById("modalEdad").textContent = (p.edad || "—") + " años";
        document.getElementById("modalSexo").textContent = 
            p.sexo === "M" ? "Masculino" : p.sexo === "F" ? "Femenino" : "—";
        document.getElementById("modalFechaNac").textContent = formatearFecha(p.fecha_nacimiento);
        document.getElementById("modalRaza").textContent = p.raza_grupo_etnico || "—";

        // Ubicación
        document.getElementById("modalEstadoNac").textContent = p.estado_nacimiento || "—";
        document.getElementById("modalMunicipioNac").textContent = p.municipio_nacimiento || "—";
        document.getElementById("modalEstadoProc").textContent = p.estado_procedencia || "—";
        document.getElementById("modalMunicipioProc").textContent = p.municipio_procedencia || "—";

        // Contacto
        document.getElementById("modalDireccion").textContent = p.direccion_habitacion || "—";
        document.getElementById("modalTelefono").textContent = p.telefono_contac || "—";
        document.getElementById("modalEmail").textContent = p.email || "—";

        // Botón PDF
        document.getElementById("btnDescargarPDF").href = 
            `${BASE_URL}/generar_pdf.php?id=${idPaciente}`;

    } catch (err) {
        console.error("Error al cargar paciente:", err);
        document.getElementById("modalNombre").textContent = "Error";
        document.getElementById("modalCedula").textContent = err.message;
    }
}

/* ===== CERRAR MODAL ===== */
function cerrarModalPaciente() {
    document.getElementById("modalVerPaciente").classList.remove("active");
}

/* ===== FORMATEAR FECHA ===== */
function formatearFecha(fecha) {
    if (!fecha) return "—";
    const d = new Date(fecha);
    if (isNaN(d)) return "—";
    return d.toLocaleDateString("es-VE");
}

/* ===== CERRAR AL HACER CLIC FUERA ===== */
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modalVerPaciente");
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === this) {
                cerrarModalPaciente();
            }
        });
    }
});

/* ===== CERRAR CON TECLA ESC ===== */
document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        cerrarModalPaciente();
    }
});


