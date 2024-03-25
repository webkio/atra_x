$(document).ready(function () {

    /* ================> DOM */

    // ****

    /* ================> END DOM */

    /* ================> Functions */

    // ****

    /* ================> END Functions */


    /* ================> EVENTS */

    window.afterToggleClonerHomeCarousel = function (clone) {
        if(typeof index == "undefined"){
            index = 0;
        }

        const btnOpenerFileManager = clone.find("button.openTheFileManager").eq(index);
        const target = clone.find(".file-input").eq(index);
        const previewSelector = clone.find(".thumbnails-preview").eq(index);

        console.log(clone.find(".file-input") , target.attr("id"));
        

        const currentCloneNumber = parseInt(target.attr("id").replace(/\D/gi, ""));

        // reset items
        clone.find(".wrapper-thumbnail-preview").eq(index).html(null);
        target.val(null);

        // update some attribute 
        target.attr("data-button-opener", target.attr("data-button-opener").replaceAll(currentCloneNumber - 1, currentCloneNumber));

        const options = JSON.parse(btnOpenerFileManager.attr('data-options'));

        options['target'] = "#" + target.attr("id");
        options['previewSelector'] = "#" + previewSelector.attr("id");

        btnOpenerFileManager.attr('data-options', JSON.stringify(options));

        generateFileManagerFeature(btnOpenerFileManager, fileMangerOptions);
    }

    

    /* ================> END EVENTS */

    /* ================> INIT */

    

    /* ================> END INIT */
});