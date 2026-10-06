/* ===================================================================
   ONCOPATH - estadisticas.js
   Gráficos de Estadísticas Regionales
   =================================================================== */


let datosPacientes = {
    estados: {},
    municipiosPorEstado: {}
};

const coloresEstados = [
    "#098cd7", "#633ab6", "#10b981", "#f59e0b", "#ef4444",
    "#14b8a6", "#8b5cf6", "#f97316", "#06b6d4", "#84cc16"
];

const coloresMunicipios = [
    "#098cd7", "#633ab6", "#10b981", "#f59e0b", "#ef4444",
    "#14b8a6", "#8b5cf6", "#f97316"
];

let graficoEstados;
let graficoMunicipios;

function crearGraficoEstados() {
    const ctx = document.getElementById("graficoEstados");
    if (!ctx) return;

    const estados = Object.keys(datosPacientes.estados);
    const cantidades = Object.values(datosPacientes.estados);

    if (estados.length === 0) {
        ctx.parentNode.innerHTML = `
            <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#94a3b8; flex-direction:column;">
                <i class="fas fa-chart-bar" style="font-size:3rem; margin-bottom:1rem;"></i>
                <p>No hay datos de estados</p>
            </div>
        `;
        return;
    }

    if (graficoEstados) graficoEstados.destroy();

    graficoEstados = new Chart(ctx, {
        type: "bar",
        data: {
            labels: estados,
            datasets: [{
                label: "Pacientes",
                data: cantidades,
                backgroundColor: coloresEstados,
                borderColor: "#ffffff",
                borderWidth: 2,
                borderRadius: 8,
                barThickness: 28
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: function (event, elements) {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const estado = estados[index];
                    actualizarGraficoMunicipios(estado);
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "#1e293b",
                    titleColor: "#ffffff",
                    bodyColor: "#ffffff",
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            const total = cantidades.reduce((a, b) => a + b, 0);
                            const porcentaje = ((context.parsed.y / total) * 100).toFixed(1);
                            return `${context.parsed.y} pacientes (${porcentaje}%)`;
                        },
                        afterLabel: function () {
                            return "💡 Clic para ver municipios";
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: "#f1f5f9" },
                    ticks: { color: "#64748b", font: { size: 12 } }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: "#1e293b",
                        font: { size: 11, weight: "500" },
                        maxRotation: 45,
                        minRotation: 0
                    }
                }
            }
        }
    });
}

function crearGraficoMunicipios(estado) {
    const ctx = document.getElementById("graficoMunicipios");
    if (!ctx) return;

    if (!estado) {
        estado = Object.keys(datosPacientes.estados)[0];
    }



    const municipios = datosPacientes.municipiosPorEstado[estado] || {};
    const labels = Object.keys(municipios);
    const data = Object.values(municipios);
    const total = data.reduce((a, b) => a + b, 0);

    const subtitulo = ctx.closest(".chart-card")?.querySelector(".chart-header p");
    if (subtitulo) subtitulo.textContent = `Estado: ${estado} (${total} pacientes)`;

    if (graficoMunicipios) graficoMunicipios.destroy();

    if (labels.length === 0) {
        ctx.parentNode.innerHTML = `
            <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#94a3b8; flex-direction:column;">
                <i class="fas fa-chart-pie" style="font-size:3rem; margin-bottom:1rem;"></i>
                <p>No hay municipios para "${estado}"</p>
            </div>
        `;
        return;
    }

    graficoMunicipios = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: coloresMunicipios,
                borderColor: "#ffffff",
                borderWidth: 3,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "62%",
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        padding: 14,
                        font: { size: 12 },
                        color: "#1e293b",
                        usePointStyle: true,
                        pointStyle: "circle"
                    }
                },
                tooltip: {
                    backgroundColor: "#1e293b",
                    titleColor: "#ffffff",
                    bodyColor: "#ffffff",
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            const porcentaje = ((context.parsed / total) * 100).toFixed(1);
                            return ` ${context.label}: ${context.parsed} (${porcentaje}%)`;
                        }
                    }
                }
            }
        }
    });
}

function actualizarGraficoMunicipios(estado) {
    if (graficoMunicipios) {
        graficoMunicipios.destroy();
    }
    crearGraficoMunicipios(estado);
}

/* ===== INICIALIZAR ===== */
document.addEventListener("DOMContentLoaded", async function () {
    
    try {
        const respuesta = await fetch("api_estadisticas.php");
        const data = await respuesta.json();

        if (!data.success) {
            console.error("Error del servidor:", data.message);
            return;
        }

        datosPacientes.estados = data.estados || {};
        datosPacientes.municipiosPorEstado = data.municipiosPorEstado || {};

        console.log("📊 Estadísticas regionales cargadas desde BD");
        console.log("   Estados:", datosPacientes.estados);



    crearGraficoEstados();

    const primerEstado = Object.keys(datosPacientes.estados)[0];
        if (primerEstado) {
            crearGraficoMunicipios(primerEstado);
        } else {
            crearGraficoMunicipios(); // muestra mensaje vacío
        }

    } catch (err) {
        console.error("Error al cargar estadísticas:", err);
    }
});
