/**
 * Simplified Security JavaScript module using jQuery
 * Handles essential security measures for authenticated pages
 */

$(document).ready(function () {
    /**
     * Prevent back navigation after logout
     */
    function preventBackNavigation() {
        if (typeof history.pushState === "function") {
            history.pushState("noBrowserBack", null, "");
            $(window).on("popstate", function (event) {
                history.pushState("noBrowserBack", null, "");
                // alert(
                //     "Por favor, use el botón de logout para cerrar sesión de manera segura.",
                // );
            });
        }
    }

    /**
     * Handle page loaded from browser cache
     */
    function handleCachedPage() {
        // If page loaded from back/forward navigation, force refresh
        if (
            performance.navigation.type ==
            performance.navigation.TYPE_BACK_FORWARD
        ) {
            window.location.replace(window.location.href);
        }

        // Handle pageshow event for cached pages
        $(window).on("pageshow", function (event) {
            if (event.originalEvent && event.originalEvent.persisted) {
                window.location.reload(true);
            }
        });
    }

    /**
     * Add logout confirmation
     */
    // function addLogoutConfirmation() {
    //     $('a[href*="logout"], form[action*="logout"]').on(
    //         "submit click",
    //         function (e) {
    //             if (!confirm("¿Está seguro de que desea cerrar sesión?")) {
    //                 e.preventDefault();
    //                 return false;
    //             }
    //         },
    //     );
    // }

    /**
     * Initialize security measures
     */
    function init() {
        preventBackNavigation();
        handleCachedPage();
        addLogoutConfirmation();

        console.log("Security module loaded");
    }

    // Initialize when document ready
    init();

    // Public API
    window.SecurityModule = {
        init: init,
        preventBack: preventBackNavigation,
        handleCache: handleCachedPage,
    };
});
