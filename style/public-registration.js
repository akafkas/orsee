(function () {
    function onReady(fn) {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", fn);
        } else {
            fn();
        }
    }

    onReady(function () {
        if (!/participant_create\.php$/i.test(window.location.pathname)) return;

        document.body.classList.add("or-register-page");

        var form = document.querySelector("form[action$='participant_create.php'], form[action='participant_create.php']");
        if (!form) form = document.querySelector("form");
        if (!form) return;

        var emailInput = form.querySelector("input[name='email']");
        if (emailInput) {
            emailInput.setAttribute("type", "email");
            emailInput.setAttribute("autocomplete", "email");
            emailInput.setAttribute("inputmode", "email");
            emailInput.setAttribute("autocapitalize", "none");
        }

        var phoneCandidates = form.querySelectorAll("input[type='text'][name*='phone'], input[type='text'][name*='mobile'], input[type='text'][name*='tel']");
        phoneCandidates.forEach(function (input) {
            input.setAttribute("type", "tel");
            input.setAttribute("autocomplete", "tel");
            input.setAttribute("inputmode", "tel");
        });

        var errorCell = form.querySelector("td[bgcolor]");
        if (errorCell) {
            errorCell.setAttribute("tabindex", "-1");
            errorCell.scrollIntoView({ behavior: "smooth", block: "center" });
            errorCell.focus({ preventScroll: true });
        }
    });
})();
