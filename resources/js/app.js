import "./bootstrap";

import Alpine from "alpinejs";

import Swal from "sweetalert2";

window.Swal = Swal;

window.Alpine = Alpine;

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("toggleSidebar");

    if (btn) {
        btn.addEventListener("click", () => {
            document.body.classList.toggle("sidebar-collapsed");
        });
    }
});
