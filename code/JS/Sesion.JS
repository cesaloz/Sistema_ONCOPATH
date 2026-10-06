function toggleSubmenu(e) {
    e.preventDefault();
    const li = e.currentTarget.parentElement;
    li.classList.toggle("open");
}

function cerrarSesion() {
    if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
        // Limpiar sessionStorage y localStorage (por si acaso)
        sessionStorage.removeItem("usuarioLogueado");
        localStorage.removeItem("usuarioLogueado");

        // Redirigir al archivo PHP que destruye la sesión
        // Detectar si estamos en subcarpeta
        const enSubcarpeta = window.location.pathname.includes("/historias/");
        window.location.href = enSubcarpeta ? "../Cerrarsesion.php" : "Cerrarsesion.php";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const sesion = sessionStorage.getItem("usuarioLogueado");
    if (sesion) {
        const data = JSON.parse(sesion);
        const nombreEl = document.querySelector(".user-name");
        if (nombreEl) nombreEl.textContent = data.nombre;
    }
});