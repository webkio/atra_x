$(document).ready(function () {


    /* ================> DOM */
    const menuOrdering = $('ul.menu-order-list');
    const btnAddElementToMenu = $(".btn.add-to-menu");
    const customLink = $('.custom-link-menu');
    const menuLabelDOM = $("#title");


    /* ================> END DOM */



    /* ================> Functions */

    function disableSortable(e) {
        const thisElement = $(this);
        const thisElementParentDadj = thisElement.parents(".dadj");
        if (thisElementParentDadj.hasClass("sortable")) {
            thisElementParentDadj.sortable('destroy');
        }
        thisElement.focus();
    }

    function enableSortable(e) {
        const thisElement = $(this);
        const thisElementParentDadj = thisElement.parents(".dadj");
        
        if (!thisElementParentDadj.hasClass("sortable")) {
            generateDadjFeature(thisElementParentDadj, dadjOption);
        }

    }

    function btnClickDeleteDOM() {
        const thisElement = $(this);
        const thisElementParentDadj = thisElement.parents(".dadj");
        const LI = thisElement.parents("li:first");

        LI.remove();

        if (!thisElementParentDadj.hasClass("sortable")) {
            generateDadjFeature(thisElementParentDadj, dadjOption);
        }
    }

    function menuJsonData(e) {
        
        const thisElement = $(this);
        const thisElementVal = thisElement.val();
        const jsonDOMSave = $(thisElement.attr('data-field'));

        
        if (!menuLabelDOM.length) return false;

        if(thisElementVal == "") return false;

       
        let json = JSON.parse(thisElement.val());

        json = {
            menuLabel: menuLabelDOM.val(),
            menuElements: json
        }

        loopThrowObjects(json, mIndexer);

        function mIndexer(element) {
            if (typeof element === "object" && element != null) {
                if (element.id && !element.checked) {
                    const LiDOM = $(`#${element.id}`);
                    if (LiDOM.length) {
                        const inputs = LiDOM.children('.input-event');
                        for (let input of inputs) {
                            input = $(input);
                            const key = input.attr("id");
                            const value = input.val();
                            element[key] = value;
                        }

                        element.checked = true;
                    }
                }
                loopThrowObjects(element, mIndexer);
            }
        }

        json = JSON.stringify(json);
        jsonDOMSave.val(json);

        jsonDOMSave.attr('value', jsonDOMSave.val())
        thisElement.attr('value', thisElement.val())

    }

    function getHTMLLiMenu() {
        const align = 'd-block text-right';
        
        const labelName = __local('Name');
        const labelMenuName = __local('Menu Name');
        const labelLink = __local('Link');
        const labelMenuURL = __local('Menu URL');
        const labelMenuCssClass = __local('Css Class ( optional )');
        const labelMenuCss = __local('Menu Css');
        const labelDelete = __local("Delete");

        let html = `<li id=""> <label class="${align}" for="menu_li_name">${labelName}</label> <input type="text" class="form-control input-event focus-disable-sortable text-center mb-2" id="menu_li_name" data-label="${labelMenuName}"> <label class="${align}" for="menu_li_url">${labelLink}</label> <input type="text" class="form-control input-event focus-disable-sortable text-center mb-2" dir="ltr" id="menu_li_url" data-label="${labelMenuURL}"> <label class="${align}" for="menu_li_css">${labelMenuCssClass}</label> <input type="text" class="form-control input-event focus-disable-sortable text-center mb-2" id="menu_li_css" data-label="${labelMenuCss}"> <input type="button" class="btn btn-danger btn-delete focus-disable-sortable mt-3" value="${labelDelete}"> <ul></ul> </li>`;
        return html;
    }

    function clickAddElementToMenu() {
        const thisElement = $(this);
        const target = $(thisElement.attr("data-target"));
        const parent = target.parent();
        if (!target.length) return false;

        const select2 = parent.find("select.select2");

        const jsonVal = target.val();

        const options = sweetAlertOptions.info;
        options.title = __local("Warning");

        if (jsonVal == "") {
            options.text = __local("There is no item to add");
            Swal.fire(options);
            return false;
        }

        const obj = JSON.parse(jsonVal);
        const itemName = obj.name || obj.title;

        if (!itemName || !obj.id) {
            options.text = __local("Name or Link is Empty");
            Swal.fire(options);
            return false;
        }

        let itemLink = obj.extra_link ?? obj.id;

        if(obj.link){
            itemLink = obj.link;
        }
      
        const attr = {
            label: {
                id: 'menu_li_name',
                value: itemName
            },
            url: {
                id: 'menu_li_url',
                value: itemLink,
            }
        }

        const liDOM = $(getHTMLLiMenu());
        
        liDOM.attr("id", "menu-" + generateRandomCharacter(6) + generateTimestampLastNumbers(-4));
        
        if (!liDOM.length) return false;

        for (const attrC in attr) {
            const mAttr = attr[attrC]
            liDOM.find(`#${mAttr['id']}`).val(mAttr['value']);
        }

        menuOrdering.append(liDOM);
        
        liDOM.find(".focus-disable-sortable").trigger("mouseover").trigger("mouseleave");

        let theMessage = __local(`x-item added to menu`).replace("x-item" , itemName);
        
        cuteToast({
            type: "success",
            message: theMessage,
            timer: "2000"
        });

        // reset select2 value
        select2.val(null).trigger("change");
        // reset input value
        parent.find(".input-event").val(null).trigger("input");
        // reset push data
        target.val(null);
    }

    function inputCustomLink() {
        const thisElement = $(this);
        const pushDataDOM = thisElement.find(".push-data");
        const inputs = thisElement.find("input");
        

        const obj = {
            title: "",
            id: "",
        }

        const keymap = {
            "menu-name-custom": "title",
            "menu-link-custom": "id",
        }

        for (let input of inputs) {
            input = $(input);
            const key = keymap[input.attr("id")];
            const value = input.val();

            if (!key) continue;

            obj[key] = value
        }

        pushDataDOM.val(JSON.stringify(obj));

    }

    /* ================> END Functions */


    /* ================> EVENTS */
    $(document).on("mouseover", ".focus-disable-sortable", disableSortable);
    $(document).on("mouseleave", ".focus-disable-sortable", enableSortable);
    $(document).on("click",".btn-delete.focus-disable-sortable", btnClickDeleteDOM);

    btnAddElementToMenu.on("click", clickAddElementToMenu)

    customLink.on("input", inputCustomLink)


    /* ================> END EVENTS */

    /* ================> INIT */

    // enable menu ordering
    menuOrdering.each(function (index, element) {
        element = $(element);
        generateDadjFeature(element, dadjOption);
        
        const jsonSortableField = $(element.attr('data-field'));
        jsonSortableField.on("input", menuJsonData);
        menuLabelDOM.on("input", function () {jsonSortableField.trigger("input") }).trigger("input");
    });

    // add placeholder
    $('#placehoder-menu').html(getHTMLLiMenu());

    /* ================> END INIT */
});