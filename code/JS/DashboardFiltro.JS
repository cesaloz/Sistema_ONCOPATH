    function filtrarTabla() {
        // Obtener valores de los filtros
        const texto = document.getElementById("buscador")?.value.toLowerCase() || "";
        const fechaFiltro = document.getElementById("filtroFecha")?.value || "";
        
        // Obtener todas las filas de la tabla
        const filas = document.querySelectorAll("#tablaPacientes tbody tr");
        let visibles = 0;
        
        filas.forEach(fila => {
            const celdas = fila.cells;
            

            if (celdas.length < 6) return;
            
            // Extraer contenido de cada celda
            const id = celdas[0].textContent.toLowerCase();
            const nombre = celdas[1].textContent.toLowerCase();
            const edad = celdas[2].textContent.toLowerCase();
            const diagnostico = celdas[3].textContent.toLowerCase();
            const fecha = celdas[4].textContent.trim(); // Formato: 06/10/2026
            const estado = celdas[5].textContent.toLowerCase();
            
            // Filtrar por texto (busca en todas las columnas)
            const coincideTexto = !texto || 
                id.includes(texto) ||
                nombre.includes(texto) ||
                edad.includes(texto) ||
                diagnostico.includes(texto) ||
                estado.includes(texto);
            
            // Filtrar por fecha
            let coincideFecha = true;
            if (fechaFiltro) {
                // Convertir fecha ISO (2026-10-06) a formato español (06/10/2026)
                const [year, month, day] = fechaFiltro.split("-");
                const fechaFormateada = `${day}/${month}/${year}`;
                
                coincideFecha = (fecha === fechaFormateada);
            }
            
            // Mostrar u ocultar fila
            if (coincideTexto && coincideFecha) {
                fila.style.display = "";
                visibles++;
            } else {
                fila.style.display = "none";
            }
        });
        
        // Mostrar mensaje si no hay resultados
        const tableContainer = document.querySelector(".table-container");
        let emptyState = document.getElementById("emptyState");
        
        if (visibles === 0 && filas.length > 0) {
            if (!emptyState) {
                emptyState = document.createElement("div");
                emptyState.id = "emptyState";
                emptyState.className = "empty-state";
                emptyState.innerHTML = `
                    <i class="fas fa-search"></i>
                    <p>No se encontraron pacientes con esos filtros.</p>
                `;
                tableContainer.appendChild(emptyState);
            }
            emptyState.style.display = "block";
        } else if (emptyState) {
            emptyState.style.display = "none";
        }
    }
    
    function limpiarFiltros() {
        const buscador = document.getElementById("buscador");
        const filtroFecha = document.getElementById("filtroFecha");
        
        if (buscador) buscador.value = "";
        if (filtroFecha) filtroFecha.value = "";
        
        filtrarTabla();
    }
    
    function verPaciente(id) {
        window.location.href = "pacientes.php?ver=" + id;
    }
    
    function editarPaciente(id) {
        window.location.href = "pacientes.php?editar=" + id;
    }
