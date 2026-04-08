(function () {
    function closeMenu(toggle, nav, backdrop) {
        toggle.setAttribute("aria-expanded", "false");
        nav.setAttribute("hidden", "hidden");
        if (backdrop) backdrop.setAttribute("hidden", "hidden");
        document.body.classList.remove("or-public-menu-open");
    }

    function openMenu(toggle, nav, backdrop) {
        toggle.setAttribute("aria-expanded", "true");
        nav.removeAttribute("hidden");
        if (backdrop) backdrop.removeAttribute("hidden");
        document.body.classList.add("or-public-menu-open");
    }

    function isMobile() {
        return window.matchMedia("(max-width: 1024px)").matches;
    }

    function initPublicMenu() {
        var toggle = document.getElementById("or-public-menu-toggle");
        var nav = document.getElementById("or-public-nav");
        var backdrop = document.getElementById("or-public-nav-backdrop");
        if (!toggle || !nav) return;

        document.body.classList.add("or-public-js");

        if (isMobile()) {
            closeMenu(toggle, nav, backdrop);
        } else {
            nav.removeAttribute("hidden");
        }

        toggle.addEventListener("click", function () {
            var expanded = toggle.getAttribute("aria-expanded") === "true";
            if (expanded) closeMenu(toggle, nav, backdrop);
            else openMenu(toggle, nav, backdrop);
        });

        nav.addEventListener("click", function (event) {
            var target = event.target;
            if (target && target.closest("a") && isMobile()) {
                closeMenu(toggle, nav, backdrop);
            }
        });

        if (backdrop) {
            backdrop.addEventListener("click", function () {
                closeMenu(toggle, nav, backdrop);
            });
        }

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && toggle.getAttribute("aria-expanded") === "true") {
                closeMenu(toggle, nav, backdrop);
                toggle.focus();
            }
        });

        window.addEventListener("resize", function () {
            if (isMobile()) {
                closeMenu(toggle, nav, backdrop);
            } else {
                nav.removeAttribute("hidden");
                if (backdrop) backdrop.setAttribute("hidden", "hidden");
                document.body.classList.remove("or-public-menu-open");
            }
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initPublicMenu);
    } else {
        initPublicMenu();
    }
})();
