import "./bootstrap";

import Alpine from "alpinejs";

import Swal from "sweetalert2";

window.Swal = Swal;

window.Alpine = Alpine;

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("toggleSidebar");

    if (btn) {
        btn.addEventListener("click", (e) => {
            e.stopPropagation();
            if (window.innerWidth <= 768) {
                document.body.classList.toggle("sidebar-open");
                document.body.classList.remove("sidebar-collapsed");
            } else {
                document.body.classList.toggle("sidebar-collapsed");
                document.body.classList.remove("sidebar-open");
            }
        });
    }

    // Close mobile sidebar when clicking outside of it
    document.addEventListener("click", (e) => {
        if (window.innerWidth <= 768 && document.body.classList.contains("sidebar-open")) {
            const sidebar = document.querySelector(".sidebar");
            const toggleBtn = document.getElementById("toggleSidebar");
            if (sidebar && !sidebar.contains(e.target) && toggleBtn && !toggleBtn.contains(e.target)) {
                document.body.classList.remove("sidebar-open");
            }
        }
    });

    // Reset layout states on window resize
    window.addEventListener("resize", () => {
        if (window.innerWidth > 768) {
            document.body.classList.remove("sidebar-open");
        } else {
            document.body.classList.remove("sidebar-collapsed");
        }
    });
});
