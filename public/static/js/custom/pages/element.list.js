$(document).ready(function () {
    /* ================> Functions */

    function preventSwitchModalOff(e) {
        const thisElement = $(e.target);
        const thisElementParent = thisElement.parent('.dropright');

        if (thisElement.attr('aria-expanded') == "false") {
            setTimeout(function () {
                thisElementParent.removeClass('show');
            }, 10)
        } else {
            thisElementParent.addClass('show');
        }
    }

    function onClickClearFilter(e) {
        const Form = $(filterOptions.form);

        Form.attr("action", Form.attr("data-route"));
        Form.submit();
    }

    window.reorderPageLinks = function () {
        let li = $(".pagination li");
        if (!li.length) return false;

        const nav = li.parents("nav");
        nav.addClass("text-center");
        nav.find(".pagination").css("display", "inline-flex")

        // remove first and last (previous and next)
        li.first().remove();
        li.last().remove();

        li = $(".pagination li");
        if (!li.length) return false;

        for (let item of li) {
            item = $(item);
            const pageNumber = item.text();
            const link = updateQueryString("page", [[["page"], [pageNumber]]]);
            item.find("a").attr("href", link);
        }

        return true;

    }

    window.generateExportLink = function () {
        const formatList = $('.format-list');

        formatList.find(".export-btn").each(function (i, element) {
            element = $(element);
            const format = element.attr('data-format');
            const link = updateQueryString("page", [[["export"], ["true"]], [["export_type"], [format]]]);
            element.attr("href", link);
        });
    }

    window.onClickReplyQuestion = function (thisElement, modalDOM, options) {
        const uid = thisElement.attr('data-user-id');
        const pid = thisElement.attr('data-product-id');

        if (!uid || !pid) {
            options.text = "invalid id";
            Swal.fire(options);
            modalDOM.find('button.close').trigger("click");
            return false;
        }

        const user_id = modalDOM.find('#user_id');
        const question_id = modalDOM.find('#question_id');

        user_id.val(null);
        question_id.val(null);

        user_id.val(uid);
        question_id.val(pid);

    }

    window.onClickDecline = function (thisElement, modalDOM, options) {
        const id = thisElement.attr('data-id');

        if (!id) {
            options.text = "invalid id";
            Swal.fire(options);
            modalDOM.find('button.close').trigger("click");
            return false;
        }

        const idDOM = modalDOM.find('input#x_id');

        idDOM.val(null);
        idDOM.val(id);
    }

    function onChangeDiscountType() {
        const thisElement = $(this);
        const value = thisElement.val();
        const target = $(thisElement.attr("data-target"));


        target.attr("date-btype", value);

        target.removeAttr("step");
        target.removeAttr("max");

        if (value == "tooman") {
            target.attr("step", 1);
        } else if (value == "percent") {
            target.attr("step", 0.1);
            target.attr("max", 100);
        }

        target.trigger("input")
    }

    function onInputVolume() {
        const thisElement = $(this);
        const value = Number(thisElement.val());

        const max = Number(thisElement.attr("max"));

        if (max) {

            if (max < value) {
                thisElement.val(max);
            }
        }
    }

    function fileSizeSelectChange() {
        const thisElement = $(this);
        const thisElementParent = thisElement.parents(".wrapper:first");
        const thisElementValue = thisElement.val();
        thisElementParent.find(".clipboard-inp").val(thisElementValue)

    }

    function addGroupActions() {
       
        /* GROUP ACTION SAMPLE | (options = JSON.parse(options);)
        {
    "includes": [
        {
            "actionName": "Delete",
            "actionType": "entity",
            "actionAsk": "Do You Want To Delete All ?",
            "actionCbk": "input#delete_action",
            "actionCbkHelper": "deleteItemsList"
        },
        {
            "actionName": "Confirm",
            "actionType": "cbk",
            "actionCbk": "MakeItConfirm"
        }
    ]
    }
        */

        const tableMain = $(".datatable.table");
        const table = $("#datatable-list");
        const tableWrapper = table.parents("#datatable-list_wrapper");
        const rows = table.find("tbody tr");

        if (!rows.length || rows.first().find(".dataTables_empty").length) return;

        let options = tableMain.attr("data-group-action-option");
        if (options) {
            options = JSON.parse(options);
        } else if (!options && $("input#delete_action").length) {
            options = {
                "includes": [
                    {
                        "actionName": __local("Delete"),
                        "actionType": "entity",
                        "actionAsk": __local("Do You Want To Delete Selected Items ?"),
                        "actionCbk": "input#delete_action",
                        "actionCbkHelper": "deleteItemsList"
                    }
                ]
            }
        } else {
            return;
        }



        // add checkbox to table row
        let counter = 1;
        for (let row of rows) {
            row = $(row);
            const firstItem = row.find("td:first");
            firstItem.html(firstItem.html() + "<br>" + `<div class="custom-control custom-checkbox lg group-action group-action-checkbox">
            <input type="checkbox" class="custom-control-input" id="custom-check-${counter}">
            <label class="custom-control-label" for="custom-check-${counter}"></label>
          </div>`)
            counter++;
        }

        // add form action to top table
        selectOptions = "";
        for (const option of options.includes) {
            const optionVal = __local(option['actionName']);
            selectOptions += `<option value='${JSON.stringify(option)}'>${optionVal}</option>`;
        }

        tableWrapper.prepend(`<div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 form-inline mb-2"><select class="form-control group-action group-action-select ml-2 mr-2">${selectOptions}</select><button class="btn btn-primary group-action group-action-select-all ml-2" data-step="check-all">${__local('Check All')}</button><button class="btn active-bg text-dark ml-2 group-action group-action-apply">${__local('Apply')}</button></div>`);

        $(".group-action-apply").on("click", function () {
            let optionValue = $(".group-action-select").val();
            if (!optionValue) {
                alert(__local("No Option Select"));
                return;
            }

            optionValue = JSON.parse(optionValue);

            const checkedRows = $(".group-action-checkbox input:checked");

            if (!checkedRows.length) {
                alert(__local("there is no checked input"));
                return;
            }

            let rows = [];
            for (let checkedRow of checkedRows) {
                checkedRow = $(checkedRow);
                rows.push(checkedRow.parents("tr:first")[0]);
            }

            const data = {
                rows: rows,
                options: optionValue,
            }

            if (optionValue['actionType'] == "entity" && optionValue['actionCbkHelper']) {
                window[optionValue['actionCbkHelper']](data);
            } else if (optionValue['actionType'] == "cbk") {
                window[optionValue['actionCbk']](data);
            } else {
                alert(__local("BAD CONFIG"));
            }

        });

        $(".group-action-select-all").on("click", function () {
            const thisElement = $(this);

            let targetSelector = "";

            if (thisElement.attr("data-step") == "check-all") {
                thisElement.removeClass("btn-primary").addClass("btn-secondary");
                thisElement.attr("data-step", "uncheck-all");
                thisElement.text(__local("Uncheck All"));
                targetSelector = ".group-action-checkbox input:not(:checked)";

            } else if (thisElement.attr("data-step") == "uncheck-all") {
                thisElement.removeClass("btn-secondary").addClass("btn-primary");
                thisElement.attr("data-step", "check-all");
                thisElement.text(__local("Check All"));
                targetSelector = ".group-action-checkbox input:checked";
            }

            $(targetSelector).trigger("click");
        });

    }

    /*
    GROUP ACTION CALLBACK
    */

    window.trueActionGeneralGroupAction = function (rows, actionCbk) {

        const result = {};

        var actionCounter = 0;
        var theRealActionCounter = 0;

        makeElementLoading($("body"));

        function doneAction() {
            actionCounter++;
            let reloadPage = false;

            if (actionCounter == rows.length) {
                makeElementLoading($("body"), true);
                reloadPage = true;
            }

            let targetLinkDOM = $(".nav-item.active a");
            if (!targetLinkDOM.length) targetLinkDOM = $(".active-bg a");

            const targetLink = !targetLinkDOM.length ? location.href : targetLinkDOM.attr("href");

            if (reloadPage && theRealActionCounter) {
                window.location.assign(targetLink);
            }

        }

        for (let row of rows) {
            row = $(row);
            const element = row.find(actionCbk);

            if (!element.length) {
                doneAction();
                continue;
            }

            element.trigger({
                type: "click",
                triggerData: {
                    isTriggering: true,
                }
            });

            const formID = element.attr("data-id-form");
            const form = $(formID);

            const mAjaxOptions = instanceJson(ajaxOptions);
            mAjaxOptions['url'] = form.attr("action");

            const args = {};
            for (let input of form.find("[name]")) {
                input = $(input);
                args[input.attr("name")] = input.val();
            }

            mAjaxOptions['data'] = args;

            window['postRequestXhr'] = false;

            mAjaxOptions['success'] = function (res) {
                doneAction();
            }

            mAjaxOptions['error'] = function (e) {
                ajaxErrorHandler(e);
                window['postRequestXhr'] = false;
                doneAction();
            }

            postRequest(mAjaxOptions);

            theRealActionCounter++;
        }

        result['theRealActionCounter'] = theRealActionCounter;

        if (result['theRealActionCounter'] === 0) {
            const options = { ...sweetAlertOptions.info };
            options.title = __local("Server Message");
            options.html = __local("There are not item to apply");
            Swal.fire(options);
        }

        return result;
    }

    window.deleteItemsList = function (data) {
        const ask = data.options.actionAsk;
        const actionCbk = data.options.actionCbk;
        const rows = data.rows;


        if (ask) {
            sweetAlertActionQuestion("", () => trueActionGeneralGroupAction(rows, actionCbk), function () { }, null, __local(ask));
        } else {
            trueActionGeneralGroupAction(rows, actionCbk);
        }

    }

    window.setStatusItemsList = function (data) {
        const ask = data.options.actionAsk;
        const actionCbk = data.options.actionCbk;
        const rows = data.rows;


        if (ask) {
            sweetAlertActionQuestion("", () => trueActionGeneralGroupAction(rows, actionCbk), function () { }, null, __local(ask));
        } else {
            trueActionGeneralGroupAction(rows, actionCbk);
        }
    }

    /*
    END GROUP ACTION CALLBACK
    */



    /* ================> END Functions */


    /* ================> EVENTS */

    // check blue button prevent hide container
    $('.dropright button[data-toggle=dropdown]').on("click", preventSwitchModalOff);
    // change switch hide/show filter .input-wrapper
    $(filterOptions.switchWrapper).find('.active-filter').on("change", onSwitchFilter);
    // clear filter red button click
    $(filterOptions.clearFilter).on("click", onClickClearFilter);

    // discount
    $("[name=discount_type]").on("change", onChangeDiscountType);
    $("#volume").on("input click", onInputVolume);

    // file.list.php select size 
    $(".file-size-select").on("change", fileSizeSelectChange);

    /* ================> END EVENTS */

    /* ================> INIT */

    // range slider init
    $(".ion-ranger").each(function (index, element) {
        element = $(element);
        generateIonRangeSliderFeature(element, ionRangeSliderOptions)
    });


    // enable Select2 All init
    $('select.select2-multiple-filter').each(function (index, element) {
        element = $(element);
        generateSelect2Feature(element, {}).on('change', onMultipleSelect2Change)
        element.trigger("change");
    });

    // add fixed width for select2
    setTimeout(() => Select2InitialWidthAsMainWidth() , 1000);

    // trigger discount type
    $("[name=discount_type]:checked").trigger("change");

    // paginate
    reorderPageLinks();

    // get export link
    generateExportLink();

    // add group actions if admin|super_admin logged in
    if ($(".view-admin").length || $(".view-super_admin").length) {
        addGroupActions();
    }

    

    /* ================> END INIT */
});