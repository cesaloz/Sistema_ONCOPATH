/* ===================================================================
   ONCOPATH - historiales.js
   Módulo de Historiales Clínicos
   =================================================================== */

let pacienteActualId = null;
let pacienteActualNombre = "";

/* ===== FILTRAR PACIENTES EN LA TABLA ===== */
function filtrarPacientes() {
    const texto = document.getElementById("buscarPaciente").value.toLowerCase();
    const filas = document.querySelectorAll("#tablaPacientes tbody tr");
    let visibles = 0;

    filas.forEach(fila => {
        const celdas = fila.cells;
        if (celdas.length < 7) return;

        const id = celdas[0].textContent.toLowerCase();
        const nombre = celdas[1].textContent.toLowerCase();
        const cedula = celdas[2].textContent.toLowerCase();
        const historia = celdas[4].textContent.toLowerCase();

        const coincide = id.includes(texto) ||
                         nombre.includes(texto) ||
                         cedula.includes(texto) ||
                         historia.includes(texto);

        if (coincide) {
            fila.style.display = "";
            visibles++;
        } else {
            fila.style.display = "none";
        }
    });

    const emptyState = document.getElementById("emptyState");
    if (emptyState) emptyState.style.display = visibles === 0 ? "block" : "none";
}

/* ===== SELECCIONAR PACIENTE ===== */
function seleccionarPaciente(boton, idPaciente, nombre, cedula) {
    // Quitar selección anterior
    document.querySelectorAll("#tablaPacientes tbody tr").forEach(tr => tr.classList.remove("selected"));
    boton.closest("tr").classList.add("selected");

    // Guardar datos
    pacienteActualId = idPaciente;
    pacienteActualNombre = nombre;

    // Mostrar aviso
    const info = document.getElementById("pacienteSeleccionado");
    document.getElementById("nombreSeleccionado").textContent = `${nombre} (Céd. ${cedula})`;
    info.style.display = "flex";

    // Actualizar el header del TAB 2
    document.getElementById("infoNombrePaciente").textContent = nombre;
    document.getElementById("infoCedula").textContent = cedula;

    // Desbloquear TAB 2
    desbloquearTab("btnTabHistorial");
}

/* ===== CONTINUAR ENTRE PASOS ===== */
function continuarPaso(idSiguiente) {
    if (idSiguiente === "tab-historial") {
        if (!pacienteActualId) {
            alert("⚠️ Primero debe seleccionar un paciente.");
            return;
        }
        cargarHistoriales(pacienteActualId);
    }

    mostrarTab(null, idSiguiente);
}

/* ===== MOSTRAR TAB ===== */
function mostrarTab(e, id) {
    if (e) e.preventDefault();

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
        alert("🔒 Seleccione un paciente primero.");
        return;
    }
    continuarPaso(id);
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

/* ===== CARGAR HISTORIALES DEL PACIENTE ===== */
async function cargarHistoriales(idPaciente) {
    const contenedor = document.getElementById("listaHistoriales");

    contenedor.innerHTML = `
        <div class="historiales-vacio">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Cargando historiales...</p>
        </div>
    `;

    try {
        const res = await fetch(`${BASE_URL}/api_obtener_historiales.php?id_paciente=${idPaciente}`);
        const historiales = await res.json();

        if (!historiales || historiales.length === 0) {
            contenedor.innerHTML = `
                <div class="historiales-vacio">
                    <i class="fas fa-folder-open"></i>
                    <p>Este paciente no tiene historiales registrados.</p>
                </div>
            `;
            return;
        }

        // Renderizar tarjetas
        contenedor.innerHTML = "";
        historiales.forEach(h => {
            contenedor.appendChild(crearTarjetaHistorial(h));
        });

    } catch (err) {
        console.error("Error al cargar historiales:", err);
        contenedor.innerHTML = `
            <div class="historiales-vacio" style="color: #dc2626;">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Error al cargar los historiales.</p>
            </div>
        `;
    }
}

/* ===== CREAR TARJETA DE HISTORIAL ===== */
function crearTarjetaHistorial(h) {
    const div = document.createElement("div");
    div.className = "historial-card";

    // Clase según tipo
    const tipoClase = (h.tipo || "").toLowerCase();

    // Formatear fecha
    const fecha = h.fecha_registro
        ? new Date(h.fecha_registro).toLocaleDateString("es-VE")
        : "—";

    div.innerHTML = `
        <div class="historial-icon ${tipoClase}">
            <i class="fas ${h.icono || 'fa-file-medical'}"></i>
        </div>
        <div class="historial-info">
            <h4>${h.titulo || 'Historial'}</h4>
            <p>${h.descripcion || 'Sin descripción'}</p>
            <div class="historial-meta">
                <span><i class="fas fa-calendar"></i> ${fecha}</span>
                <span><i class="fas fa-user-md"></i> ${h.medico || 'No asignado'}</span>
            </div>
        </div>
        <div class="historial-acciones">
            <button class="action-btn view" onclick="verDetalleHistorial(${h.id}, '${h.tipo}')" title="Ver detalle">
                <i class="fas fa-eye"></i>
            </button>
            <a href="${BASE_URL}/generar_pdf_historial.php?id=${h.id}&tipo=${h.tipo}"
               target="_blank"
               class="action-btn Descarga"
               title="Descargar PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
        </div>
    `;

    return div;
}

/* ===== FILTRAR HISTORIALES ===== */
function filtrarHistorial() {
    const texto = document.getElementById("buscadorHistorial").value.toLowerCase();
    const tipoFiltro = document.getElementById("filtroTipo").value.toLowerCase();
    const fechaFiltro = document.getElementById("filtroFecha").value;

    const tarjetas = document.querySelectorAll(".historial-card");
    let visibles = 0;

    tarjetas.forEach(card => {
        const titulo = card.querySelector("h4").textContent.toLowerCase();
        const desc = card.querySelector("p").textContent.toLowerCase();
        const tipo = card.className.toLowerCase();

        const fechaTexto = card.querySelector(".historial-meta span").textContent;
        const fecha = new Date(fechaTexto.split(":")[1]?.trim() || "").toISOString().split("T")[0];

        const coincideTexto = titulo.includes(texto) || desc.includes(texto);
        const coincideTipo = !tipoFiltro || tipo.includes(tipoFiltro);
        const coincideFecha = !fechaFiltro || fecha === fechaFiltro;

        if (coincideTexto && coincideTipo && coincideFecha) {
            card.style.display = "";
            visibles++;
        } else {
            card.style.display = "none";
        }
    });
}

/* ===== LIMPIAR FILTROS DE HISTORIAL ===== */
function limpiarFiltrosHistorial() {
    document.getElementById("buscadorHistorial").value = "";
    document.getElementById("filtroTipo").value = "";
    document.getElementById("filtroFecha").value = "";
    filtrarHistorial();
}

/* ===== VER DETALLE DE HISTORIAL ===== */
function verDetalleHistorial(id, tipo) {
    alert(`👁️ Ver historial ID: ${id} - Tipo: ${tipo}`);
    // window.location.href = `${BASE_URL}/historias/ver_historial.php?id=${id}&tipo=${tipo}`;
}