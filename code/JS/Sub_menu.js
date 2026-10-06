function mostrarTab(e, id) {
    if (e) e.preventDefault();

    document.querySelectorAll(".tab-content").forEach(t => t.classList.remove("active"));
    document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));

    const tab = document.getElementById(id);
    if (tab) tab.classList.add("active");

    const boton = document.querySelector(`.tab-btn[onclick*="${id}"]`);
    if (boton) boton.classList.add("active");
}