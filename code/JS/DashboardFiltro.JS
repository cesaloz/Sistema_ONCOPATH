    function filtrarTabla() {
<<<<<<< Updated upstream

        const texto = document.getElementById("buscador")?.value.toLowerCase() || "";
        const fechaFiltro = document.getElementById("filtroFecha")?.value || "";
        

=======
        // Obtener valores de los filtros
        const texto = document.getElementById("buscador")?.value.toLowerCase() || "";
        const fechaFiltro = document.getElementById("filtroFecha")?.value || "";
        
        // Obtener todas las filas de la tabla
>>>>>>> Stashed changes
        const filas = document.querySelectorAll("#tablaPacientes tbody tr");
        let visibles = 0;
        
        filas.forEach(fila => {
            const celdas = fila.cells;
            
<<<<<<< Updated upstream
            if (celdas.length < 6) return;       

=======

            if (celdas.length < 6) return;
            
            // Extraer contenido de cada celda
>>>>>>> Stashed changes
            const id = celdas[0].textContent.toLowerCase();
            const nombre = celdas[1].textContent.toLowerCase();
            const edad = celdas[2].textContent.toLowerCase();
            const diagnostico = celdas[3].textContent.toLowerCase();
            const fecha = celdas[4].textContent.trim(); // Formato: 06/10/2026
            const estado = celdas[5].textContent.toLowerCase();
            
<<<<<<< Updated upstream

=======
            // Filtrar por texto (busca en todas las columnas)
>>>>>>> Stashed changes
            const coincideTexto = !texto || 
                id.includes(texto) ||
                nombre.includes(texto) ||
                edad.includes(texto) ||
                diagnostico.includes(texto) ||
<<<<<<< Updated upstream
                fecha.includes(texto) ||
                estado.includes(texto);
            
            let coincideFecha = true;
            if (fechaFiltro) {
=======
                estado.includes(texto);
            
            // Filtrar por fecha
            let coincideFecha = true;
            if (fechaFiltro) {
                // Convertir fecha ISO (2026-10-06) a formato español (06/10/2026)
>>>>>>> Stashed changes
                const [year, month, day] = fechaFiltro.split("-");
                const fechaFormateada = `${day}/${month}/${year}`;
                
                coincideFecha = (fecha === fechaFormateada);
            }
            
<<<<<<< Updated upstream

=======
            // Mostrar u ocultar fila
>>>>>>> Stashed changes
            if (coincideTexto && coincideFecha) {
                fila.style.display = "";
                visibles++;
            } else {
                fila.style.display = "none";
            }
        });
        
<<<<<<< Updated upstream
=======
        // Mostrar mensaje si no hay resultados
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
        const buscador = document.getElementById("buscador").value = "";
        const filtroFecha = document.getElementById("filtroFecha").value = "";
=======
        const buscador = document.getElementById("buscador");
        const filtroFecha = document.getElementById("filtroFecha");
        
        if (buscador) buscador.value = "";
        if (filtroFecha) filtroFecha.value = "";
        
>>>>>>> Stashed changes
        filtrarTabla();
    }
    
    function verPaciente(id) {
        window.location.href = "pacientes.php?ver=" + id;
    }
    
    function editarPaciente(id) {
        window.location.href = "pacientes.php?editar=" + id;
    }
