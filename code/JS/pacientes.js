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
    const texto = (document.getElementById("buscador").value || "").toLowerCase();
    const selectEstado = document.getElementById("filtroEstado");
    const estadoFiltro = selectEstado ? selectEstado.value : "Activo";
    const fechaFiltro = document.getElementById("filtroFecha").value || "";

    const filas = document.querySelectorAll("#tablaPacientes tbody tr.fila-paciente");
    let visibles = 0;

    filas.forEach(fila => {
        const celdas = fila.cells;
        if (celdas.length < 6) return;


        const id        = celdas[0].textContent.toLowerCase();
        const nombre    = celdas[1].textContent.toLowerCase();
        const edad      = celdas[2].textContent.toLowerCase();
        const historia  = celdas[3].textContent.toLowerCase();
        const fecha     = celdas[4].textContent.trim();
        const estadoReal = fila.dataset.status || "activo";


        const coincideTexto = !texto ||
            id.includes(texto) ||
            nombre.includes(texto) ||
            edad.includes(texto) ||
            historia.includes(texto);
        
        let coincideEstado = true;

            if (estadoFiltro === "Activo") {
                coincideEstado = (estadoReal === "activo");
            } else if (estadoFiltro === "Inactivo") {
                coincideEstado = (estadoReal === "inactivo");
            } else if (estadoFiltro === "") {
                coincideEstado = true;   // "Todos" → muestra ambos
        }       

        let coincideFecha = true;
            if (fechaFiltro) {
                const [year, month, day] = fechaFiltro.split("-");
                const fechaFormateada = `${day}/${month}/${year}`;
                
                coincideFecha = (fecha === fechaFormateada);
            }
            
        if (coincideTexto && coincideEstado && coincideFecha) {
            fila.classList.remove("oculta");
            visibles++;
        } else {
            fila.classList.add("oculta");
        }
    });

    const resultados = document.getElementById("resultados");
    if (resultados) resultados.textContent = `Mostrando ${visibles} paciente(s)`;

    const emptyState = document.getElementById("emptyState");
    if (emptyState) emptyState.style.display = visibles === 0 ? "block" : "none";

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




        const btnPDF = document.getElementById("btnDescargarPDF");
            if (btnPDF) {
                btnPDF.href = `${BASE_URL}/generar_pdf.php?id=${idPaciente}`;
        }

        setTimeout(() => {
            const btnCerrar = modal.querySelector(".modal-close");
            if (btnCerrar) btnCerrar.focus();
        }, 100);




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














/* ===================================================================
   MODAL DE EDITAR DATOS DEL PACIENTE
   =================================================================== */

// FUNCIÓN PARA EDITAR USUARIO
async function mostrarEditarPaciente(idPaciente) {
    const modal = document.getElementById("modalEditarPaciente");
    if (!modal) return;

    modal.classList.add("active");
    
    try {

        const res = await fetch(`${BASE_URL}/api_obtener_paciente.php?id=${idPaciente}`);

        if (!res.ok) throw new Error('Error HTTP ${res.status}');


            const p = await res.json();
            if (p.error) throw new Error(p.error);

            
            document.getElementById('editarIdPaciente').value = p.id_paciente || "";
            document.getElementById('editarCedula').textContent = "Cédula: " + (p.cedula || "-");
            
            document.getElementById('edit_nombres').value = p.primer_nombre || "";

            document.getElementById('edit_segundo_nombre').value = p.segundo_nombre || "";

            document.getElementById('edit_apellidos').value = p.primer_apellido || "";


            document.getElementById('edit_segundo_apellido').value = p.segundo_apellido || "";

            document.getElementById('edit_cedula').value = p.cedula || "";

            document.getElementById('edit_no_historia').value = p.no_historia || "";

            document.getElementById('edit_fecha_nacimiento').value = p.fecha_nacimiento || "";

            document.getElementById('edit_edad').value = p.edad || "";

            document.getElementById('edit_sexo').value = p.sexo || "";

            document.getElementById('edit_raza').value = p.raza_grupo_etnico || "";

            document.getElementById('edit_direccion').value = p.direccion_habitacion || "";

            document.getElementById('edit_telefono').value = p.telefono_contac || "";
            document.getElementById('edit_email').value = p.email || "";


            
        } catch (err) {
            console.error("Error al cargar paciente:", err);
            alert("❌ No se pudieron cargar los datos del paciente: " + err.message);
            modal.classList.remove("active");
            }       
    }

    async function guardarEdicionPaciente() {
        const form = document.getElementById("formEditarPaciente");
        if (!form) return;

        const datos = {};
        form.querySelectorAll("input, select, textarea").forEach(campo => {
            
            if (campo.name && campo.type !== 'checkbox' && campo.type !== 'radio') {
                    datos[campo.name] = campo.value.trim();
            }
        });

         if (!datos.id_paciente) {
                alert("⚠️ No se encontró el ID del paciente.");
                return;
            }


     const btnGuardar = form.querySelector('button[type="submit"]');
    const textoOriginal = btnGuardar.innerHTML;
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    try {
        const res = await fetch(`${BASE_URL}/api_actualizar_paciente.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
    });

    const resultado = await res.json();

        if (resultado.ok) {
                alert(`✅ Paciente "${datos.primer_nombre} ${datos.primer_apellido}" actualizado correctamente.`);
                cerrarModalEditarPaciente();
                // Recargar la página para ver los cambios reflejados
                window.location.reload();
            } else {
                alert("❌ Error: " + (resultado.error || "No se pudo actualizar"));
            }
                } catch (err) {
                    console.error("Error al guardar:", err);
                    alert("❌ Error al conectar con el servidor.");
                } finally {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = textoOriginal;
                }
    }


    

    /* ===== CERRAR MODAL ===== */
    function cerrarModalEditarPaciente() {
        const modal = document.getElementById("modalEditarPaciente");
        if (modal) {
            (modal.classList.remove("active"));
            }
    }


    /* ===== CERRAR AL HACER CLIC FUERA ===== */
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("modalEditarPaciente");
        if (modal) {
            modal.addEventListener("click", function (e) {
                if (e.target === this) {
                    cerrarModalEditarPaciente();
                }
            });
        }
    });

    /* ===== CERRAR CON TECLA ESC ===== */
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            cerrarModalEditarPaciente();
        }
    });






// FUNCIÓN PARA ELIMINAR Paciente
async function eliminarPaciente(boton, idPaciente) {
    const fila = boton.closest("tr");
    const nombre = fila.cells[1].textContent.trim();

    const confirmar = confirm(
        `¿Desactivar al paciente "${nombre}"?\n\n` +
        `El paciente NO se borra de la base de datos, solo se marca como inactivo. ` +
        `Podrás reactivarlo después.`

    );

    if (!confirmar) return;

    boton.disabled = true;
    const iconoOriginal = boton.innerHTML;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'

    try {
        const res = await fetch(`${BASE_URL}/api_eliminar_paciente.php`, {
            method: "POST",
            headers: {"content-type": "application/json"},
            body: JSON.stringify({id_paciente: idPaciente})

        });
            const data = await res.json();
        
        if (data.ok) {

            const badge = fila.querySelector(".badge");  

            if (badge) {
                badge.className = "badge inactive";
                badge.textContent = "Inactivo";
            }    
            
            boton.className = "action-btn reactivate";
            boton.innerHTML = '<i class="fas fa-undo"></i>';
            boton.title = "Reactivar paciente";
            boton.onclick = function () { reactivarPaciente(boton, idPaciente); };
            boton.disabled = false;   
            
            fila.dataset.status = "inactivo";
            fila.classList.add("fila-inactiva");
            fila.style.opacity = "";

            filtrarTabla();


        }else {
            alert("❌ " + (data.error || "No se pudo desactivar"));
            boton.disabled = false;
            boton.className = claseOriginal;
            boton.innerHTML = iconoOriginal;
        }
     }catch (err) {
            console.error("Error al eliminar:", err);
            alert("❌ Error de conexión");
            boton.disabled = false;
            boton.className = claseOriginal;
            boton.innerHTML = iconoOriginal;
        }
    }








    // FUNCIÓN PARA reactivar Paciente
async function reactivarPaciente(boton, idPaciente) {
    const fila = boton.closest("tr");
    const nombre = fila.cells[1].textContent.trim();

    if (!confirm(`¿Reactivar al paciente "${nombre}"?`)) return;

    boton.disabled = true;
    const iconoOriginal = boton.innerHTML;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const res = await fetch(`${BASE_URL}/api_reactivar_paciente.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id_paciente: idPaciente })
        });

        const data = await res.json();

        if (data.ok) {
            const badge = fila.querySelector(".badge");
            if (badge) {
                badge.className = "badge active";
                badge.textContent = "Activo";
            }

            boton.className = "action-btn delete";
            boton.innerHTML = '<i class="fas fa-trash"></i>';
            boton.title = "Eliminar paciente";
            boton.onclick = function () { eliminarPaciente(boton, idPaciente); };
            boton.disabled = false;

            fila.dataset.status = "activo";
            fila.classList.remove("fila-inactiva");
            fila.style.opacity = "";

            filtrarTabla();


        } else {
            alert("❌ " + (data.error || "No se pudo reactivar"));
            boton.disabled = false;
            boton.className = claseOriginal;
            boton.innerHTML = iconoOriginal;
        }
    } catch (err) {
        console.error("Error al reactivar:", err);
        alert("❌ Error de conexión");
        boton.disabled = false;
        boton.className = claseOriginal;
        boton.innerHTML = iconoOriginal;
    }
}


document.addEventListener("DOMContentLoaded", function () {

    const selectEstado = document.getElementById("filtroEstado");
    if (selectEstado) {
        selectEstado.value = "Activo";
    }


    filtrarTabla();
});