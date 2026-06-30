const navbar = document.getElementById("landingNavbar");

function updateNavbarState() {
    if (!navbar) return;

    if (window.scrollY > 12) {
        navbar.classList.add("is-scrolled");
    } else {
        navbar.classList.remove("is-scrolled");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const navbar = document.querySelector(".landing-navbar");
    const navList = document.querySelector(".landing-navbar .navbar-nav");
    const navLinks = document.querySelectorAll(".landing-navbar .nav-link");
    const navbarCollapse = document.getElementById("landingNavbar");

    if (!navbar || !navList || navLinks.length === 0) {
        return;
    }

    let lastScrollY = window.scrollY;
    let ticking = false;

    /* Moving hover indicator */
    const indicator = document.createElement("span");
    indicator.classList.add("nav-hover-indicator");
    navList.appendChild(indicator);

    function getHashFromLink(link) {
        try {
            return new URL(link.href).hash;
        } catch (error) {
            return "";
        }
    }

    function moveIndicatorTo(link) {
        if (!link || window.innerWidth <= 991) {
            indicator.style.opacity = "0";
            return;
        }

        const navRect = navList.getBoundingClientRect();
        const linkRect = link.getBoundingClientRect();

        indicator.style.left = `${linkRect.left - navRect.left}px`;
        indicator.style.width = `${linkRect.width}px`;
        indicator.style.opacity = "1";
    }

    function getActiveLink() {
        return document.querySelector(".landing-navbar .nav-link.active");
    }

    function setActiveLink(hash) {
        navLinks.forEach(function (link) {
            const linkHash = getHashFromLink(link);
            link.classList.toggle("active", linkHash === hash);
        });

        moveIndicatorTo(getActiveLink());
    }

    function updateActiveMenuByScroll() {
        const sections = [];

        navLinks.forEach(function (link) {
            const hash = getHashFromLink(link);

            if (!hash) {
                return;
            }

            const section = document.querySelector(hash);

            if (section) {
                sections.push(section);
            }
        });

        if (sections.length === 0) {
            return;
        }

        let activeId = sections[0].id;

        sections.forEach(function (section) {
            const rect = section.getBoundingClientRect();

            if (rect.top <= 160) {
                activeId = section.id;
            }
        });

        setActiveLink(`#${activeId}`);
    }

    function handleSmartNavbar() {
        const currentScrollY = window.scrollY;
        const isMobileMenuOpen =
            navbarCollapse && navbarCollapse.classList.contains("show");

        if (currentScrollY > 40) {
            navbar.classList.add("navbar-scrolled");
        } else {
            navbar.classList.remove("navbar-scrolled");
        }

        if (
            !isMobileMenuOpen &&
            currentScrollY > lastScrollY &&
            currentScrollY > 150
        ) {
            navbar.classList.add("navbar-hidden");
        } else {
            navbar.classList.remove("navbar-hidden");
        }

        lastScrollY = Math.max(currentScrollY, 0);
        updateActiveMenuByScroll();
        ticking = false;
    }

    window.addEventListener("scroll", function () {
        if (!ticking) {
            window.requestAnimationFrame(handleSmartNavbar);
            ticking = true;
        }
    });

    window.addEventListener("resize", function () {
        moveIndicatorTo(getActiveLink());
    });

    navLinks.forEach(function (link) {
        link.addEventListener("mouseenter", function () {
            moveIndicatorTo(link);
        });

        link.addEventListener("click", function (event) {
            const hash = getHashFromLink(link);
            const target = hash ? document.querySelector(hash) : null;

            if (target) {
                event.preventDefault();

                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });

                history.pushState(null, "", hash);
                setActiveLink(hash);

                if (
                    navbarCollapse &&
                    navbarCollapse.classList.contains("show") &&
                    window.bootstrap
                ) {
                    const collapseInstance =
                        bootstrap.Collapse.getOrCreateInstance(navbarCollapse);
                    collapseInstance.hide();
                }
            }
        });
    });

    navList.addEventListener("mouseleave", function () {
        moveIndicatorTo(getActiveLink());
    });

    const initialHash = window.location.hash;

    if (initialHash && document.querySelector(initialHash)) {
        setActiveLink(initialHash);
    } else {
        updateActiveMenuByScroll();
    }

    setTimeout(function () {
        moveIndicatorTo(getActiveLink());
    }, 100);
});

updateNavbarState();
window.addEventListener("scroll", updateNavbarState);
