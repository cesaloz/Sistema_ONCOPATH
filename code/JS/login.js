const usuariosLocales = [
    {
        usuario: "admin",
        password: "1234",
        nombre: "Admin",
        rol: "admin"
    },
    {
        usuario: "doctor",
        password: "doctor123",
        nombre: "Dr. Enrique Chayane",
        rol: "doctor"
    },
    {
        usuario: "enfermera",
        password: "enf123",
        nombre: "Enf. Yennifer Lopez",
        rol: "enfermera"
    }
];

/* ===== VALIDAR LOGIN ===== */
function validarLogin(event) {
    event.preventDefault();

    const usuario = document.getElementById("usuario").value.trim();
    const password = document.getElementById("password").value.trim();

    // Buscar en el array local
    const encontrado = usuariosLocales.find(
        u => u.usuario === usuario && u.password === password
    );

    if (!encontrado) {
        mostrarMensaje("❌ Usuario o contraseña incorrectos.", "error");
        return;
    }

    // Guardar SOLO la sesión actual (se borra al cerrar el navegador)
    sessionStorage.setItem("usuarioLogueado", JSON.stringify({
        usuario: encontrado.usuario,
        nombre: encontrado.nombre,
        rol: encontrado.rol,
        hora: new Date().toISOString()
    }));

    mostrarMensaje(`✅ Bienvenido(a), ${encontrado.nombre}`, "success");

    // Redirigir después de un pequeño delay
    setTimeout(() => {
        window.location.href = "Dashboard.php";
    }, 800);
}

/* ===== MOSTRAR MENSAJE ===== */
function mostrarMensaje(texto, tipo) {
    const mensaje = document.getElementById("mensajeLogin");
    if (!mensaje) return;

    mensaje.textContent = texto;
    mensaje.className = `login_verificacion ${tipo}`;
    mensaje.style.display = "block";
}