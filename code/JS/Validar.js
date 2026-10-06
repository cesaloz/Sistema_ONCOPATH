
/* ===== SOLO LETRAS Y ESPACIOS ===== */
function soloLetras(input) {
    input.value = input.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, '');
}

/* ===== SOLO NÚMEROS ===== */
function soloNumeros(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
}

/* ===== SOLO ALFANUMÉRICO (para direcciones) ===== */
function alfanumerico(input) {
    input.value = input.value.replace(/[^A-Za-z0-9ÁÉÍÓÚáéíóúÑñÜü\s.,#\-\/]/g, '');
}

/* ===== VALIDAR EMAIL ===== */
function validarEmail(input) {
    input.value = input.value.replace(/[^a-zA-Z0-9@._\-]/g, '');
}

/* ===== VALIDAR AL PEGAR (paste) ===== */
document.addEventListener("DOMContentLoaded", function () {
    // Para campos que solo aceptan letras
    document.querySelectorAll("[data-solo-letras]").forEach(input => {
        input.addEventListener("paste", function (e) {
            e.preventDefault();
            const texto = (e.clipboardData || window.clipboardData).getData("text");
            const limpio = texto.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü\s]/g, "");
            document.execCommand("insertText", false, limpio);
        });
    });

    // Para campos que solo aceptan números
    document.querySelectorAll("[data-solo-numeros]").forEach(input => {
        input.addEventListener("paste", function (e) {
            e.preventDefault();
            const texto = (e.clipboardData || window.clipboardData).getData("text");
            const limpio = texto.replace(/[^0-9]/g, "");
            document.execCommand("insertText", false, limpio);
        });
    });
});