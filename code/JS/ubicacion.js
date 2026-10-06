/* ===================================================================
   ONCOPATH - ubicacion.js
   Selects dependientes: Estado → Municipio → Parroquia
   ⚠️ Guarda NOMBRES, no IDs
   =================================================================== */

const API_URL = "../api_ubicacion.php";

/* ===== CARGAR ESTADOS AL INICIO ===== */
document.addEventListener("DOMContentLoaded", function () {
    cargarEstados("nac");
    cargarEstados("pro");
});

/* ===== CARGAR ESTADOS ===== */
async function cargarEstados(prefijo) {
    const select = document.getElementById(prefijo + "Estado");
    if (!select) return;

    try {
        const res = await fetch(API_URL + "?tipo=estados");
        const data = await res.json();

        if (!data.success) {
            console.error("Error:", data.message);
            return;
        }

        select.innerHTML = '<option value="">Seleccionar...</option>';
        data.datos.forEach(e => {
            // ✅ Guardamos el NOMBRE
            select.innerHTML += `<option value="${e.estado}">${e.estado}</option>`;
        });

    } catch (err) {
        console.error("Error al cargar estados:", err);
    }
}

/* ===== CARGAR MUNICIPIOS ===== */
async function cargarMunicipios(prefijo) {
    const selectEstado = document.getElementById(prefijo + "Estado");
    const selectMunicipio = document.getElementById(prefijo + "Municipio");
    const selectParroquia = document.getElementById(prefijo + "Parroquia");

    if (!selectEstado || !selectMunicipio || !selectParroquia) return;

    const nombreEstado = selectEstado.value;

    selectMunicipio.innerHTML = '<option value="">Seleccionar...</option>';
    selectParroquia.innerHTML = '<option value="">Seleccionar municipio primero</option>';

    if (!nombreEstado) {
        selectMunicipio.innerHTML = '<option value="">Seleccionar estado primero</option>';
        return;
    }

    try {
        const res = await fetch(
            API_URL + "?tipo=municipios&filtro=" + encodeURIComponent(nombreEstado)
        );
        const data = await res.json();

        if (!data.success || data.datos.length === 0) {
            selectMunicipio.innerHTML = '<option value="">Sin municipios</option>';
            return;
        }

        data.datos.forEach(m => {
            // ✅ Guardamos el NOMBRE
            selectMunicipio.innerHTML += `<option value="${m.municipio}">${m.municipio}</option>`;
        });

    } catch (err) {
        console.error("Error al cargar municipios:", err);
    }
}

/* ===== CARGAR PARROQUIAS ===== */
async function cargarParroquias(prefijo) {
    const selectMunicipio = document.getElementById(prefijo + "Municipio");
    const selectParroquia = document.getElementById(prefijo + "Parroquia");

    if (!selectMunicipio || !selectParroquia) return;

    const nombreMunicipio = selectMunicipio.value;

    selectParroquia.innerHTML = '<option value="">Seleccionar...</option>';

    if (!nombreMunicipio) {
        selectParroquia.innerHTML = '<option value="">Seleccionar municipio primero</option>';
        return;
    }

    try {
        const res = await fetch(
            API_URL + "?tipo=parroquias&filtro=" + encodeURIComponent(nombreMunicipio)
        );
        const data = await res.json();

        if (!data.success || data.datos.length === 0) {
            selectParroquia.innerHTML = '<option value="">Sin parroquias</option>';
            return;
        }

        data.datos.forEach(p => {
            // ✅ Guardamos el NOMBRE
            selectParroquia.innerHTML += `<option value="${p.parroquia}">${p.parroquia}</option>`;
        });

    } catch (err) {
        console.error("Error al cargar parroquias:", err);
    }
}