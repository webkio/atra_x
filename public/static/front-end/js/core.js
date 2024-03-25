$(document).ready(function () {

    /* ================> DOM */

    const search = $("#search-inp");


    /* ================> END DOM */

    /* ================> Functions */

    function searchOnKeyUp(e) {
        const target = $(e.target);
        const value = target.val();

        const form = $("#search-form");
        if (form == null) return;

        const uri = form.attr("data-uri");
        const actionTerm = uri.replace("{term?}", value);
        form.attr("action", actionTerm);
    }

    function actionDisplayPopup(item) {
        const container = $('<div class="swal2-container swal2-center swal2-shown parent"></div>');

        let img = "";

        if (item['thumbnail_url']) {
            img = `<div class="img-popup-wrapper text-center"><img class="rounded" src="` + item['thumbnail_url'] + `"></div>`;
        }

        const popupHTML = `<div class="modal-popup-wrapper rounded p-3"> <i id="close" class="bi bi-x-circle float-right h3 cursor-pointer text-danger" title="close"></i> <h2 class="text-center">${item['title']}</h2>${img}<div class="content-wrapper mt-3">${item['body']}</div> </div>`;

        container.append(popupHTML);

        $("body").append(container);

        container.on("click", function (e) {
            const thisElement = $(this);
            const targetElement = $(e.target);
            if (thisElement.get(0) == targetElement.get(0)) {
                thisElement.remove();
            }
        })
    }

    function actionDisplayNotification(item) {
        const html = `<div class="notification-wrapper w-100 text-center font-weight-bold h5 parent">
        <i class="bi bi-x-circle float-right mr-3 mb-0 mt-1 h3 cursor-pointer text-white" id="close"></i>
        <div class="content-wrapper p-2 pb-3 mb-0"><p>${item.body}</p></div>
    </div>`;

        $("body").prepend(html);
    }

    function checkForShowPopup(_popupAds = []) {

        let the_popupAds = null;

        if (_popupAds.length) {
            the_popupAds = _popupAds;
        }else if(typeof popupAds != "undefined"){ 
            the_popupAds = popupAds;
        }

        if (typeof the_popupAds == "undefined" || !the_popupAds) return false;

        if (1 < the_popupAds.length) {
            checkForShowPopup([the_popupAds[0]]);
            checkForShowPopup([the_popupAds[1]]);

            return;
        }

        const mainItem = the_popupAds[0];
        const item = mainItem.item;

        let container = "";

       

        if (mainItem.condition.display == "notification") {
            container = actionDisplayNotification(item)
        } else if (mainItem.condition.display == "popup") {
            container = $('<div class="swal2-container swal2-center swal2-shown"></div>');

            let img = "";

            if (item['thumbnail_url']) {
                img = `<div class="img-popup-wrapper text-center"><img class="rounded" src="` + item['thumbnail_url'] + `"></div>`;
            }

            const popupHTML = `<div class="modal-popup-wrapper rounded p-3"> <i id="close" class="bi bi-x-circle float-end h3 cursor-pointer text-danger" title="close"></i> <h2 class="text-center">${item['title']}</h2>${img}<div class="content-wrapper mt-3">${item['body']}</div> </div>`;

            container.append(popupHTML);
        }


        if (container) {
            $("body").append(container);

            container.on("click", function (e) {
                const thisElement = $(this);
                const targetElement = $(e.target);
                if (thisElement.get(0) == targetElement.get(0) || targetElement.attr("id") == "close") {
                    thisElement.remove();
                }
            })
        }

    }

    function checkForCookieAsk() {
        if (typeof (cookiePolicyData) == "undefined") return false;

        const html = $(`<div class="cookie-wrapper p-2 bg-white shadow"> <p>${cookiePolicyData['content']}</p> <div class="cookie-answer text-center"> <button type="button" data-action="${cookiePolicyData['actions']['agree']['action']}" class="btn btn-success">${cookiePolicyData['actions']['agree']['label']}</button> <button type="button" data-action="${cookiePolicyData['actions']['disagree']['action']}" class="btn btn-danger">${cookiePolicyData['actions']['disagree']['label']}</button> </div> </div>`);
        $("body").append(html);

        $(".cookie-answer button").on("click", function (e) {
            const target = $(e.target);
            const action = target.attr("data-action");
            if (!action) return false;


            (function () {
                const mAjaxOptions = instanceJson(ajaxOptions);
                mAjaxOptions['url'] = "/check/data/get/setcookie_policy";

                const formData = {
                    "_token": getCsrf(),
                    "action": action,
                }

                mAjaxOptions['data'] = formData;

                mAjaxOptions['success'] = function (res) {
                    window['postRequestXhr'] = false;
                }

                mAjaxOptions['error'] = function (e) {
                    ajaxErrorHandler(e);
                    window['postRequestXhr'] = false;
                }

                postRequest(mAjaxOptions);
            })();

            html.remove();

        });

        return true;
    }

    window.initStarsOnCommentName = function (element) {
        let starsIcon = "";
        const currentScore = parseInt(element.attr("data-star-rating"));
        for (let index = 1; index <= 5; index++) {
            let currentClass = "bi-star-fill";

            if (currentScore < index)
                currentClass = "bi-star"

            starsIcon += `<i class="ms-1 bi ${currentClass}"></i>`;
        }

        const html = `<span class="ms-2 star-wrapper-one text-warning">${starsIcon}</span>`;
        element.append(html)
    }

    window.getOffsetElement = function (el) {
        const rect = el.get(0).getBoundingClientRect();
        return rect;
    }

    window.getScrollYPercentElement = function (element) {
        const dimenssion = getOffsetElement(element);
        var vx = dimenssion.height - Math.abs(dimenssion.y);
        var vy = vx - window.innerHeight;

        let percent = 0;

        if (0 < vy && dimenssion.y < 0) {
            percent = Math.round(Math.abs(dimenssion.y) / element.height() * 100);
        } else if (vy < 0) {
            percent = 100;
        }

        return percent;
    }

    function removeParentClose() {
        const thisElement = $(this);
        const parent = thisElement.parents(".parent");
        parent.remove();
    }

    function formCommentTypeCancelHandler() {
        const formWrapper = $(".comment-form-wrapper");
        const cancelBtn = $("#cancel-comment-form");

        if (!cancelBtn.length || !formWrapper.length) return;

        cancelBtn.on("click", function () {
            $(".comment-reply-btn")
            cancelBtn.parents(".comment-item").find(".comment-reply-btn").removeClass("d-none");
            const formCommentType = cancelBtn.parents("form");
            formCommentType.appendTo(formWrapper);
            formCommentType.find("#parent_id").val("-1");
        });
    }

    function formCommentTypeReplyHandler() {
        const formCommentType = $(".form-type-comment");
        const replyBtnCommentType = $(".comment-reply-btn");

        if (!formCommentType.length || !replyBtnCommentType.length) return;

        replyBtnCommentType.on("click", function (e) {
            const thisElement = $(e.target);
            const parent = thisElement.parents(".comment-item");
            parent.append(formCommentType);
            formCommentType.find("#parent_id").val(parent.attr("data-id"))
            thisElement.addClass("d-none");
        });
    }

    window.setOldValueForFormSchema = function (wrapper) {
        if (!jsonDataServer || !jsonDataServer['keymap_data_page']) return;

        const oldMap = jsonDataServer['keymap_data_page'];

        const keys = Object.keys(oldMap);

        // checkbox
        for (const key of keys) {
            const theValue = oldMap[key];
            if (theValue instanceof Array) {
                for (const valueItem of theValue) {
                    const dom = wrapper.find(`[name='${key}[]'][value='${valueItem}']`).first();

                    if (!dom.length) continue;

                    dom.prop("checked", true);
                }
            }

        }

        // radiobox
        const radioBoxes = wrapper.find("[type='radio']");

        for (let radioBox of radioBoxes) {
            radioBox = $(radioBox);
            const radioName = radioBox.attr('name');

            const oldValueForRadio = oldMap[radioName];
            if (!oldValueForRadio) continue;

            const dom = wrapper.find(`[type='radio'][value='${oldValueForRadio}']`).first();
            if (!dom.length) continue;

            dom.prop("checked" , true);
        }


    }

    /* ================> END Functions */

    /* ================> options */

    /* ================> END options */


    /* ================> EVENTS */

    search.on("keyup", searchOnKeyUp);

    /* ================> END EVENTS */

    /* ================> INIT */

    setTimeout(showErrorServer, 500);

    checkForShowPopup();

    setTimeout(checkForCookieAsk, 1000);

    // init stars
    $("[data-star-rating]").each(function (i, element) {
        element = $(element);
        initStarsOnCommentName(element)
    });

    $(document).on("click", "#close", removeParentClose);

    // show user menu in front end
    $("#user-fullname-menu").click(function () {
        $(".user-list-menu").toggleClass("d-none");
    });

    // submit logout form when clicked on logout button
    $("#logout-action").click(function () {
        $($(this).attr("data-target")).submit();
    });


    // when click on reply btn comment type
    formCommentTypeReplyHandler();

    // when click on cancel btn comment type
    formCommentTypeCancelHandler();


    const formSchemaHtml = $("#form_schema");
    if (formSchemaHtml.length) {
        setOldValueForFormSchema(formSchemaHtml);

        
    }

    /* ================> END INIT */

})