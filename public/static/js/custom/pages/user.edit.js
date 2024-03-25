$(document).ready(function () {

    /* ================> DOM */

    const btnSrcHidden = $(".generateSrcHidden");

    /* ================> END DOM */

    /* ================> Functions */

    function btnSrcHiddenClick(){
        const thisElement = $(this);
        const target = $(thisElement.attr("data-target"));
        const parentTarget = target.parent();

        target.remove();

        parentTarget.find("#documents").val(null);
    }

    /* ================> END Functions */


    /* ================> EVENTS */
    
    btnSrcHidden.on("click" , btnSrcHiddenClick)

    /* ================> END EVENTS */

    /* ================> INIT */
    
    // enable Select2
    generateSelect2Feature($('.select2'), {
        minimumResultsForSearch: Infinity
    })

    /* ================> END INIT */
});