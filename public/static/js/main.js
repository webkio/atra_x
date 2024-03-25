$(document).ready(function () {

    /* ================> DOM */

    const liDepth1 = $(".menu-side .nav#depth-1 > li");
    const allLiList = $(".menu-side li");


    /* ================> END DOM */

    /* ================> Functions */

    // #### General

    window.sweetAlertActionQuestion = function (label, functionTrue, functionFalse, title = null, text = null) {
        const options = { ...sweetAlertOptions.question };
        options.title = title ? title : __local("Are You Sure ?");
        options.text = text ? text : __local("Do You Want To Delete this x-text");

        options.text = options.text.replace(/x-text/gi, label);

        Swal.fire(options).then(function (result) {
            if (result.value) {
                functionTrue();
            } else {
                functionFalse();
            }
        });
    }

    window.afterToggleClonerSocial = function (clone) {
        clone.find(".wrapper-thumbnail-preview").html(null);

        const cloneIDOld = clone.attr("id");

        const templateId = clone.attr("data-template-id");
        clone.attr("id", templateId.replace("x", clone.attr("data-clone-number")));

        const btnMediaSelector = clone.find(".openTheFileManager");
        const json = JSON.parse(btnMediaSelector.attr("data-options"));

        const openerPerview = clone.find("[data-button-opener]");
        openerPerview.attr("data-button-opener", openerPerview.attr("data-button-opener").replace(cloneIDOld, clone.attr("id")));

        // json
        json['target'] = "#" + clone.find(".icon-one").attr("id");
        json['previewSelector'] = "#" + clone.find(".thumbnails-preview").attr("id");
        // end


        btnMediaSelector.attr("data-options", JSON.stringify(json));
        generateFileManagerFeature(btnMediaSelector, fileMangerOptions);
    }

    window.afterClonerDelete = function (firstClone) {
        setTimeout(function () {
            firstClone.find(".input-one").first().trigger("input");
        }, 10)
    }

    window.makeJsonByDataFields = function (e) {
        const thisElement = $(e.target);
        const inputWrapper = thisElement.parents(".input-wrapper:first()");
        const theJsonDOM = inputWrapper.find(".the-json:first()");
        const childElements = inputWrapper.find(".child-element");

        let json = "";
        const theArray = [];

        for (let childElement of childElements) {
            childElement = $(childElement);
            const currrentInputs = childElement.find(".input-one");
            const theObject = {}
            for (let currrentInput of currrentInputs) {
                currrentInput = $(currrentInput);
                theObject[currrentInput.attr("data-field")] = {
                    "value": currrentInput.val(),
                    "id": currrentInput.attr("id")
                };

            }
            theArray.push(theObject);
        }

        json = JSON.stringify(theArray);

        theJsonDOM.val(json);
    }

    window.afterToggleFormInput = function (clone) {
        // reset new clone item
        toggleActiveClonable(clone, false);
        clone.attr('data-details', clone.attr('data-default-details'));

        // active new clone
        clone.trigger("click");
    }

    window.afterDeleteFormInput = function (firstClone) {
        setTimeout(function () {
            firstClone.trigger("click");
            $(".wrapper-properties").find(".input-form-prop-one:first").trigger("input");
        }, 10);
    }

    window.getCurrentBootstrapColumn = function () {
        const windowWidth = window.innerWidth;

        let currentColumn = "";
        if (columns["xs"] <= windowWidth && windowWidth < columns["sm"]) {
            currentColumn = "xs";
        } else if (columns["sm"] <= windowWidth && windowWidth < columns["md"]) {
            currentColumn = "sm";
        } else if (columns["md"] <= windowWidth && windowWidth < columns["lg"]) {
            currentColumn = "md";
        } else if (columns["lg"] <= windowWidth && windowWidth < columns["xl"]) {
            currentColumn = "lg";
        } else if (columns["xl"] <= windowWidth) {
            currentColumn = "xl";
        }

        return currentColumn;
    }

    window.callColumnCallback = function (e = null) {

        const callbackList = window["columnCallbackList"][currentBootstrapColumn];

        if (!callbackList) return;

        for (const theCallback of callbackList) {
            window[theCallback](e, currentBootstrapColumn);
        }
    }

    window.sidebarHandler = function () {
        const thisElement = $(this);
        const thisElementIcon = thisElement.find(".bi:first()");

        const sideNav = $("#side-nav");
        const sideNavIsActive = sideNav.hasClass("d-none") === false;

        if (!sideNavIsActive) {
            thisElementIcon.removeClass("bi-menu-button-wide-fill").addClass("bi-arrow-90deg-up");
            makeSidebarDashboardActive();
        } else {
            thisElementIcon.removeClass("bi-arrow-90deg-up").addClass("bi-menu-button-wide-fill");
            makeSidebarDashboardActive(null, null, false);
        }

    }

    window.jsonSwitchValueInit = function (element) {
        const dataJsonField = element.find(".the-value-data:first()");
        const json = JSON.parse(dataJsonField.val());

        const dataPageSelector = "[data-page]";
        const checkboxSelector = "[type=checkbox]";

        const parents = element.find(dataPageSelector);

        for (let parent of parents) {
            parent = $(parent);
            const dataPage = parent.attr("data-page");

            let data = json[dataPage];
            const childs = parent.find(".child-element");
            for (let child of childs) {
                child = $(child);
                const inp = child.find(".the-value:first()");
                if (!inp.length) continue;
                const value = data[inp.attr("id")];
                if (value) {
                    inp.attr("checked", "checked");
                }
            }
        }

        element.find(checkboxSelector).off("change");
        element.find(checkboxSelector).on("change", function (e) {
            const thisElement = $(e.target);
            const parent = thisElement.parents(dataPageSelector).first();
            const currentDataPage = parent.attr("data-page");
            if (!parent.length) return;

            const objectData = {};
            parent.find(checkboxSelector).each(function (index, item) {
                item = $(item);
                objectData[item.attr("id")] = item.prop("checked");
            });
            json[currentDataPage] = objectData;
            dataJsonField.val(JSON.stringify(json));
            jsonSwitchValueInit(element);
        })
    }

    window.Select2ValueToTextInput = function (e) {
        const thisElement = $(e.target);
        const inputWrapper = thisElement.parents(".input-wrapper:first()");
        const selectDOM = inputWrapper.find("select");
        const theTextValueDOM = inputWrapper.find(".the-text-value");

        let extraData = selectDOM.attr("data-extra-data");
        let current_extraData = null;
        if (extraData) {
            extraData = JSON.parse(extraData);
            current_extraData = extraData[thisElement.val()];
            if (current_extraData["sample"])
                theTextValueDOM.attr("placeholder", "Sample : " + current_extraData["sample"]);
        }

        theTextValueDOM.trigger("input");

        if (thisElement.val().search(":") === -1) {
            thisElement.val(null);
        }
    }

    window.Select2InitialWidthAsMainWidth = function(wrapperClass = ".select2-fixed-width "){
        const elements = $(wrapperClass).next().find(`span.select2-selection__rendered`);
        for (let element of elements) {
            element = $(element);
            element.css({
                "width" : element.get(0).clientWidth
            })
        }
    }

    window.theTextValueInput = function (e) {
        const thisElement = $(e.target);
        const inputWrapper = thisElement.parents(".input-wrapper:first()");
        const selectDOM = inputWrapper.find("select");
        const valueDOM = inputWrapper.find(".select-value-to-text");

        const thisElementValue = thisElement.val();

        if (thisElementValue == "") {
            valueDOM.val(null);
            return;
        }

        const compressedValue = selectDOM.val() + ":" + thisElementValue;
        valueDOM.val(compressedValue);
    }

    window.addQuickElementTaxonomy = function (e) {
        const thisElement = $(e.target);
        const parent = thisElement.parents(".quick-create-wrapper").first();
        const inputWrapper = parent.parents(".input-wrapper").first();
        const inputs = parent.find(".quick_input");
        const buttonAdd = inputWrapper.find(".quick-add").first();

        const data = {
            "_token": getCsrf()
        };

        const buttonField = buttonAdd.attr("data-field");
        if (buttonField) {
            const tmp_buttonField = buttonField.split(",");
            for (let buttonFieldElement of tmp_buttonField) {
                buttonFieldElement = buttonFieldElement.trim();
                const tmp_buttonFieldElement = buttonFieldElement.split(":");
                data[tmp_buttonFieldElement[0]] = tmp_buttonFieldElement[1];
            }
        }

        for (let input of inputs) {
            input = $(input);
            const key = input.attr("data-name");
            const value = input.val();

            data[key] = value;
        }

        window['postRequestXhr'] = false;

        const mAjaxOptions = instanceJson(ajaxOptions);
        mAjaxOptions['url'] = "/check/data/set/setTaxonomyRest";
        mAjaxOptions['data'] = data;
        mAjaxOptions['error'] = function (e) {
            ajaxErrorHandler(e);
        }
        mAjaxOptions['success'] = function (res) {
            const options = { ...sweetAlertOptions.info };

            if (res.status == "success") {
                inputs.val(null);
                options.text = __local("Added");
            } else if (res.status == "fail") {
                const prefix = res.data[0] ? $("#" + res.data[0]).attr("data-label") + " " : "";
                options.text = prefix + res.message;
            }

            if (options.text) {
                swal.fire(options);
            }
        }

        postRequest(mAjaxOptions)

    }

    window.instanceForm = function (data) {
        const formData = new FormData();

        for (const key of Object.keys(data)) {
            const element = data[key];
            formData.append(key, element)
        }

        return formData;
    }

    window.checkBoxOnAction = function (element, cbk) {
        if (cbk) {
            runUntilExists([window, cbk], { "action": function () { element.on("change", window[cbk]) }, "args": [] })
        }
    }

    window.elementHeightFitter = function (element, height, cbk = null) {
        const elementHeight = element.get(0).clientHeight;

        if (elementHeight < height) {
            element.css("height", height);
            if (cbk !== null) {
                cbk();
            }
        }
    }

    window.getElementByLabelAndNext = function (label, prefix, selectorInput) {
        return $(`${prefix}('${label}')`).parent().find(selectorInput);
    }

    window.getElementByNthChild = function (parentSelector, childSelector, index) {
        return $(parentSelector).find(childSelector).eq(index);
    }

    window.initEyeOnPassword = function (element) {
        element.after('<i class="password-eye cursor-pointer bi bi-eye"></i>');
        element.parent().find(".bi.password-eye").on("click", function (e) {
            const currentElement = $(e.target);
            const inputPassword = currentElement.prev();

            if (currentElement.hasClass("bi-eye-slash")) {
                currentElement.removeClass("bi-eye-slash active-text").addClass("bi-eye");
                inputPassword.attr("type", "password");
            } else if (currentElement.hasClass("bi-eye")) {
                currentElement.removeClass("bi-eye").addClass("bi-eye-slash active-text");
                inputPassword.attr("type", "text");
            }

        })
    }

    window.generateTimestampLastNumbers = function (number = -4) {
        return Date.now().toString().slice(number);
    }

    window.getUniqueIdDOM = function (DOM, id, seperator = "_t_") {
        let theID = id ? id : DOM.attr('id');
        theID = theID.split(seperator);
        theID = theID[0];

        const finallID = theID + seperator + generateTimestampLastNumbers();
        DOM.attr('id', finallID);
        return finallID;
    }

    window.loopThrowObjects = function (objectList, callback) {

        if (typeof objectList === "object" && objectList != null) {
            const keys = Object.keys(objectList);
            for (const key of keys) {
                if (varList.loopBreak) return;
                const mElement = objectList[key]
                callback(mElement);
            }
        }

    }

    window.convertBToMB = function (byte) {
        return (isNaN(Number(byte))) ? 0 : byte / 1048576;
    }

    window.getTimeZone = function () {
        return jsonDataServer['timezone'];
    }

    // get number from format ex: 15.66px → 16
    function pxToNumber(str) {
        return Math.round(str.replace("px", ""));
    }

    // remove class active related to #cls
    function elementsRemoveClass(elements, cls, callback = null) {
        elements.each(function (index, element) {
            element = $(element);
            if (callback) callback(element);

            element.removeClass(cls);
        });
    }


    window.select2SetValue = function (select2DOM, value) {

        if (select2DOM.hasClass(select2AjaxOptions.generatedClass)) {
            setTimeout(function () {
                select2DOM.val(value).trigger('change', "queryCheck");
            }, 200);
        } else {
            var runUntilGenerateSelect2 = function (obj) {
                if (!obj.element.hasClass(select2AjaxOptions.generatedClass)) {
                    setTimeout(runUntilGenerateSelect2, 100, obj);
                    return false;
                }

                obj.element.val(obj.value).trigger('change', "queryCheck");

            }

            setTimeout(runUntilGenerateSelect2, 100, {
                element: select2DOM,
                value: value
            });
        }
    }


    window.showSelect2AjaxResult = function (data, li) {
        if (data.loading || !data.id) {
            return data.text;
        }

        var container = $(`<div class="result-select2-ajax">${data.name}</div>`);
        return container;
    }

    window.showSelection2AjaxResult = function (data) {
        if (data.id) {
            const option = $(data.element);
            const parent = option.parents(".select2:first").parent();
            const pushElementData = parent.find(".push-data");
            if (pushElementData.length) pushElementData.val(JSON.stringify(data));
        }

        return data.name || data.text || select2AjaxOptions['placeholder'];
    }

    window.updateQueryString = function (queryNameList, queryReplace) {
        if (typeof queryNameList != "object")
            queryNameList = [queryNameList];

        const queryList = Array.from(getCurrentQuery());

        for (const queryName of queryNameList) {
            const index = queryList.findIndex((element) => element[0] == queryName);
            if (index != -1) queryList.splice(index, 1);
        }

        const tmpQueryList = Object.assign([], queryList);
        for (const element of queryReplace) {
            tmpQueryList.push(element);
        }

        let query = "?";
        for (const qList of tmpQueryList) {

            query += qList[0] + "=" + qList[1] + "&";
        }
        query = query.substring(0, query.length - 1);
        const link = location.pathname + query;

        return link;
    }

    // #### END General

    // #### select2 functions

    window.runUntilQueueOver = function () {
        if (!queueArray.length) return false;

        const obj = queueArray[0];

        let mAjaxOptions = instanceJson(ajaxOptions);

        const select2 = obj.select2;

        const values = obj.values;

        if (!values.length) {
            queueArray.shift();
            runUntilQueueOver();
            return false;
        }

        const id = values[0];

        values.shift();

        queueArray[0]['values'] = values;

        const select2Options = JSON.parse(select2.attr("data-optioned"));

        mAjaxOptions['url'] = select2Options['ajax']['url'];
        mAjaxOptions['data'] = {
            "_token": getCsrf(),
            "id": id,
            "taxonomy": select2.attr('data-taxonomy')
        }

        mAjaxOptions['data'] = select2AjaxExtraParams(select2, mAjaxOptions['data']);

        mAjaxOptions['success'] = function (res) {
            if (res.data.length === 0) return false;

            const theRes = res.data[0];
            let option = new Option(theRes.name, theRes.id, true, true);
            select2.append(option).trigger("change");
            window['postRequestXhr'] = false;
            runUntilQueueOver();
        }

        postRequest(mAjaxOptions);
    }

    window.loadAjaxDataSelect2Filter = function (ajaxWrappers, defaultSelect2Selector = "select.select2") {
        window.queueArray = [];

        for (let ajaxWrapper of ajaxWrappers) {
            ajaxWrapper = $(ajaxWrapper);
            const select2 = ajaxWrapper.find(defaultSelect2Selector);
            const contentListSelect2 = ajaxWrapper.find(".the-value");
            const valuesRaw = select2.attr("data-ajx") ? select2.attr("data-ajx") : contentListSelect2.val();

            if (!valuesRaw) continue;

            const values = valuesRaw.split(",");

            const obj = {
                select2: select2,
                contentListSelect2: contentListSelect2,
                values: values,
            }
            queueArray.push(obj);
        }

        runUntilQueueOver();
    }

    // #### END select2 functions

    // #### tinymce functions

    window.getSelectionVideoObjectTinyMce = function (editor, selector = "[data-mce-object=\"video\"]") {
        const domSelected = $(editor.selection.getNode());
        const foundedDOM = domSelected.find(selector).last();
        return foundedDOM;
    }

    window.getElementByNthChildTinyMce = function (number) {
        const [parent, child, index] = ['.tox-dialog__body', "input", number - 1];
        return getElementByNthChild(parent, child, index);
    }

    window.getElementByLabelAndNextTinyMce = function (label) {
        const prefix = ".tox-dialog label:contains";
        const selectorInput = "input";
        return getElementByLabelAndNext(label, prefix, selectorInput);
    }

    window.addCustomMediaBtnTinyMce = function (editor, title, parent, elementStrTemplate, callback = null) {
        const element = $(elementStrTemplate);
        const attrOptions = element.attr('data-options');
        const options = attrOptions ? JSON.parse(attrOptions) : {};
        const inputTarget = options.target;
        if (editor.title == title) {
            $(parent).append(element);
            generateFileManagerFeature(element, fileMangerOptions);
            if (callback) {
                window[callback](inputTarget);
            }

            return true;
        }

        return false;
    }

    window.generateDimensionImageTinyMce = function (inputTarget) {
        const target = $(inputTarget);

        target.on("textInput", function (e) {
            const thisElement = $(this);
            const image = new Image();
            image.src = thisElement.val();
            $(image).on("load", function (e) {
                const [width, height] = [image.width, image.height];
                getElementByNthChildTinyMce(3).val(width);
                getElementByNthChildTinyMce(4).val(height);
            })
        });

        target.on("importFileURL", function () {
            $(this).trigger("textInput")
        })
    }

    window.getContentTinyMce = function (e) {
        const editor = this;
        const thisElement = $('#' + editor.id);
        let rawContent = editor.getContent({ format: 'text' });
        rawContent = rawContent.replace("\n", "").replace(/\s{2,}/gi, " ").replace(/\t{2,}/gi, " ")
        const htmlContent = editor.getContent();


        const rawContentField = $('#' + editor.id + '_raw');
        const htmlContentField = $('#' + editor.id + '_html');

        if (rawContentField.length)
            rawContentField.val(rawContent);
        if (htmlContentField.length)
            htmlContentField.val(htmlContent);


        thisElement.val(htmlContent).trigger("input");
    }

    window.addCustomToolbarsTinyMce = function (editor) {
        // video
        editor.ui.registry.addToggleButton('csVideo', {
            icon: 'embed',
            tooltip: 'Insert Video',
            onAction: function (e) {
                const res = editor.windowManager.open({
                    title: 'Insert/Edit Video',
                    body: {
                        type: 'panel',
                        items: [
                            {
                                type: 'input',
                                name: 'source',
                                label: 'Source (URL)'
                            },
                            {
                                type: 'selectbox',
                                name: 'preload',
                                label: 'Preload',
                                items: [
                                    {
                                        value: "auto",
                                        text: "Auto"
                                    },
                                    {
                                        value: "metadata",
                                        text: "Meta Data"
                                    },
                                    {
                                        value: "none",
                                        text: "None"
                                    },
                                ]
                            },
                            {
                                type: 'input',
                                name: 'poster',
                                label: 'Poster (URL)'
                            },
                            {
                                type: 'sizeinput',
                                name: 'dimension',
                            },
                        ]
                    },
                    buttons: [
                        {
                            type: 'cancel',
                            name: 'closeButton',
                            text: 'Cancel'
                        },
                        {
                            type: 'submit',
                            name: 'submitButton',
                            text: 'Save',
                            primary: true
                        }
                    ],
                    initialData: {
                        dimension: {
                            "width": "300",
                            "height": "150",
                        }
                    },
                    onSubmit: (api) => {
                        const data = api.getData();
                        const autoload = data.preload;
                        const poster = data.poster != "" ? `poster="${data.poster}"` : "";
                        const source = data.source.trim();

                        const dimension = data.dimension;
                        const width = dimension.width ? dimension.width : "300";
                        const height = dimension.height ? dimension.height : "150";

                        const templateHtml = `<video class="video tinymce video-rapidcode" width="${width}" controls height="${height}" src="${source}" preload="${autoload}" ${poster}></video>`;

                        if (source) {
                            const currentSelection = editor.currentSelectionOnAction;
                            if (currentSelection && editor.currentSelectionOnAction.length) {
                                currentSelection.remove();
                            }
                            tinymce.activeEditor.execCommand('mceInsertContent', false, templateHtml);
                            api.close();
                        } else {
                            const options = { ...sweetAlertOptions.info };
                            options.title = __local("Required Field");
                            options.text = __local("Source not completed !");
                            Swal.fire(options);
                        }


                    }
                });
                const currentSelectionOnAction = getSelectionVideoObjectTinyMce(editor);
                editor.currentSelectionOnAction = currentSelectionOnAction;
                if (currentSelectionOnAction.length) {

                    const prefix = 'data-mce-p-';
                    // force to use poster 
                    let poster = currentSelectionOnAction.attr(prefix + "poster");
                    poster = poster ? poster : "";
                    res.setData({
                        "source": currentSelectionOnAction.attr(prefix + "src"),
                        "preload": currentSelectionOnAction.attr(prefix + "preload"),
                        "poster": poster,
                        "dimension": {
                            "width": currentSelectionOnAction.attr("width"),
                            "height": currentSelectionOnAction.attr("height"),
                        },
                    });

                }


            },
            onSetup: function (buttonApi) {
                const checkBtnActive = function (e) {
                    const foundedDOM = getSelectionVideoObjectTinyMce(editor);
                    if (foundedDOM.length) {
                        buttonApi.setActive(true)
                    } else {
                        buttonApi.setActive(false);
                    }
                }
                editor.on('NodeChange', checkBtnActive);
            }
        });

        // align
        editor.ui.registry.addGroupToolbarButton('alignGroup', {
            icon: 'align-justify',
            tooltip: 'Alignment',
            items: 'alignleft aligncenter alignright alignjustify'
        });

        // featured Format 
        editor.ui.registry.addGroupToolbarButton('featuredFormat', {
            icon: 'color-picker',
            tooltip: 'Format Style',
            items: 'bold italic forecolor backcolor emoticons'
        });

    }

    window.tinyMceEditLink = function (e) {
        const editor = e.target;
        editor.windowManager.oldOpen = editor.windowManager.open;
        editor.windowManager.open = function (windowMNG, r) {
            var modal = this.oldOpen.apply(this, [windowMNG, r]);

            const mediaPopUpIntreact = {
                target: null,
                importBtn: null,
            }

            // insert image
            mediaPopUpIntreact['target'] = getElementByNthChildTinyMce(1);
            mediaPopUpIntreact['importBtn'] = `<input type="button" id="image_import_image_tinymce" class="openTheFileManager tox-button" value="${__local('Import Image')}" data-options='{ "multiple":false, "groupType":"image", "target": "#${mediaPopUpIntreact['target'].attr('id')}" }'>`;
            addCustomMediaBtnTinyMce(windowMNG, "Insert/Edit Image", '.tox-dialog__footer-end', mediaPopUpIntreact['importBtn'], "generateDimensionImageTinyMce");


            // insert video
            // video
            mediaPopUpIntreact['target'] = getElementByNthChildTinyMce(2);
            mediaPopUpIntreact['importBtn'] = `<input type="button" id="image_import_video_poster_tinymce" class="openTheFileManager tox-button" value="${__local('Import Video Poster')}" data-options='{ "multiple":false, "groupType":"image", "target": "#${mediaPopUpIntreact['target'].attr('id')}" }'>`;
            addCustomMediaBtnTinyMce(windowMNG, "Insert/Edit Video", '.tox-dialog__footer-end', mediaPopUpIntreact['importBtn']);
            // video poster
            mediaPopUpIntreact['target'] = getElementByNthChildTinyMce(1);
            mediaPopUpIntreact['importBtn'] = `<input type="button" id="image_import_video_tinymce" class="openTheFileManager tox-button" value="${__local('Import Video')}" data-options='{ "multiple":false, "groupType":"video", "target": "#${mediaPopUpIntreact['target'].attr('id')}" }'>`;
            addCustomMediaBtnTinyMce(windowMNG, "Insert/Edit Video", '.tox-dialog__footer-end', mediaPopUpIntreact['importBtn']);



            return modal;
        };
    }

    window.setupTinyMce = function (editor) {
        editor.on('init', tinyMceEditLink);
        editor.on('init', getContentTinyMce);
        editor.on('keyup', getContentTinyMce);
        editor.on('NodeChange', getContentTinyMce);

        addCustomToolbarsTinyMce(editor);
    }


    // #### END tinymce functions

    // #### Menu Aside

    // when click on li element depth-1
    function liDepth1Click(e) {

        const thisElement = $(this);
        const currentElement = $(e.target);

        const href = currentElement.attr("href");
        if (currentElement.parents(".nav:first").attr("id") != "depth-1" || href) {
            if (href) location.assign(href);
            return false;
        }

        const thisElementNext = thisElement.next();
        const ulChild = thisElement.find("ul:first");
        const parentUl = thisElement.parent();
        const activeClass = "active-bg";
        const ulChildList = parentUl.children("li").find("ul");
        const hasActiveClass = thisElement.hasClass(activeClass);

        elementsRemoveClass(parentUl.children("li"), activeClass, function (element) { element.removeAttr("style") });

        if (hasActiveClass) {
            elementsRemoveClass(ulChildList, varList.displayBlock)
            return false;
        }

        thisElement.addClass(activeClass);
        elementsRemoveClass(ulChildList, varList.displayBlock);

        if (ulChild.length) {
            if (thisElementNext.length) {
                const offsetTop = pxToNumber(thisElementNext.css("margin-top")) + pxToNumber(ulChild.css("height"));
                thisElementNext.attr("style", "margin-top:" + offsetTop + "px !important")
            }
            ulChild.addClass(varList.displayBlock);

        }
    }

    // after load site auto active Li
    function checkForActiveLi(List) {

        const removeQueryItemFromQueryList = function (queryList, keyToRemove) {
            if (queryList[keyToRemove]) {
                delete (queryList[keyToRemove]);
            }

            return queryList;
        }

        for (let ls of List) {
            ls = $(ls);
            const aTag = ls.children("a:first");
            if (aTag.length) {
                let href = aTag.attr("href");
                if (href) {
                    href = href.replace(/\?/gi, "\\?");

                    const regex = new RegExp(href, 'gi');

                    // remove query page to fix active class menu

                    const isInRoute = location.href.replace(/\?page=\d{1,}/gi, "").replace(/&page=\d{1,}/gi, "").search(regex) == -1 ? false : true;
                    const [aTagLinkQuery, hrefLinkQuery] = [JSON.stringify(removeQueryItemFromQueryList(getAllQuery(location.origin + href), "page")), JSON.stringify(removeQueryItemFromQueryList(getAllQuery(location.href), "page"))];

                    if (isInRoute && aTagLinkQuery == hrefLinkQuery) {
                        ls.addClass(ls.attr("data-active-class"));
                        ls.children("a").attr("href", location.href);
                        const liParent = ls.parents("li:first");
                        if (liParent.length) {
                            liParent.trigger("click");
                            ls.parents("ul:first").addClass("d-block");
                            liParent.addClass(liParent.attr("data-active-class"))
                        }
                        break;
                    }
                }
            }
        }
    }

    function setHeightAsideNav() {
        const sideNav = $("#side-nav");
        sideNav.css("height", window.innerHeight);

        if (getCurrentBootstrapColumn() == "lg" || getCurrentBootstrapColumn() == "xl") {
            sideNav.css("position", "sticky");
            sideNav.addClass("sticky-top");
        }
    }

    function checkForActiveLiRemove() {
        const ul2DOM = $('.menu-side #depth-2');
        for (let ul of ul2DOM) {
            ul = $(ul);
            const liChildren = ul.children("li");
            if (liChildren.length === 1) {

                const currentLi = liChildren.eq(0);
                const editDOM = currentLi.children(".edit-active");
                if (editDOM.length && !currentLi.hasClass("active")) {
                    ul.parent().remove();
                }
            }
        }
    }

    window.makeSidebarDashboardActive = function (event = null, column = null, makeActive = true) {
        const sideNav = $("#side-nav");
        if (makeActive) {
            sideNav.removeClass("d-none");
        } else {
            sideNav.addClass("d-none");
        }
    }

    window.makeSidebarDashboardDeactive = function (event = null, column = null) {
        const sideNav = $("#side-nav");
        makeSidebarDashboardActive(null, null, false);
        if ($(".sidebar-handler").length == 0) {
            sideNav.after(`<div class="sidebar-handler col-12 mt-2 text-center"><span class="active-text cursor-pointer d-lg-none d-xl-none"><i class="bi bi-menu-button-wide-fill h2"></i></span></div>`);
        }
    }

    // #### END Menu Aside

    // #### Features

    window.getDataOptionPlugin = function (element, options) {
        const dataOptions = element.attr('data-options') ? JSON.parse(element.attr('data-options')) : {};
        const mOptions = Object.assign({}, options);

        for (const key of Object.keys(dataOptions)) {
            const currentElement = dataOptions[key];
            mOptions[key] = currentElement;
        }

        return mOptions;
    }

    window.generateClipboardFeature = function (selector) {
        if (typeof ClipboardJS == "undefined") return false;

        const res = new ClipboardJS(selector)
        return res;
    }

    window.generateFileManagerFeature = function (element, options = {}) {
        if (typeof finalFileManagerOptions == "undefined") return false;

        const mOptions = getDataOptionPlugin(element, options);
        element.off("click");
        element.on("click", function () {
            openFileManager(mOptions);
        });

        element.off("closeFileManager");
        element.on("closeFileManager", function () {

            if (mOptions["preview"])
                window[mOptions.onCloseCallback](mOptions);
        });

        // if has value from server or some where else and also for image placeholder
        const target = $(mOptions['target']);
        let isPlaceholder = false;

        if (target.attr("data-placeholder") !== undefined) {
            target.val(varList.noImageSrc);
            isPlaceholder = true;
        }

        if (target.length && target.val()) {
            const callbackClose = mOptions['onCloseCallback'];
            window[callbackClose](mOptions);

            if (isPlaceholder) {
                target.val(null);
            }
        }
    }

    window.select2AjaxExtraParams = function (select2Dom, query) {
        let extraParams = $(select2Dom).attr('data-extra-param') ?? "{}";
        extraParams = JSON.parse(extraParams);

        if (Object.keys(extraParams).length) {
            query['extra_params'] = extraParams;
        }

        return query;
    }

    window.generateSelect2Feature = function (element, options = {}) {
        if (typeof select2 == "undefined") return false;

        const mOptions = getDataOptionPlugin(element, options);
        mOptions['placeholder'] = mOptions['placeholder'] ? mOptions['placeholder'] : select2AjaxOptions['placeholder'];

        if (mOptions['ajax'] && mOptions['ajax']['url']) {
            if (!mOptions['ajax']['data']) {
                mOptions['ajax']['data'] = select2AjaxOptions['ajax']['data'];
            }
            if (!mOptions['ajax']['processResults']) {
                mOptions['ajax']['processResults'] = select2AjaxOptions['ajax']['processResults'];
            }
        }


        const res = element.select2(mOptions);
        res.attr("data-optioned", JSON.stringify(mOptions));
        return res;
    }

    window.generateFeatureSimpleBar = function (element, options) {
        if (typeof SimpleBar == "undefined") return false;

        const mOptions = getDataOptionPlugin(element, options);
        const res = new SimpleBar(element.get(0), mOptions);
        return res;
    }

    window.generateFeatureTinyMce = function (options) {
        if (typeof tinymce == "undefined") return false;

        const element = $(options['selector']);
        const mOptions = getDataOptionPlugin(element, options);
        const res = tinymce.init(mOptions);
        return res;

    }

    window.generateDataTableFeature = function (element, options = {}) {
        if (typeof $().DataTable == "undefined") return false;

        const mOptions = getDataOptionPlugin(element, options);

        const res = element.DataTable(mOptions);
        return res;
    }

    window.generateJqueryCloner = function (element, options = {}) {
        if (typeof $().cloner === "undefined") return false;

        function smoothScrollToView(element) {
            element[0].scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        }

        const mOptions = getDataOptionPlugin(element, options);
        const rawClassPromiseToAppend = "promise-to-append";

        // fix afterToggle callback
        if (mOptions['afterToggle']) {
            let tmpData1 = mOptions['afterToggle'];
            mOptions['afterToggle'] = function (clone, index, self) {

                if (mOptions["appendAfterBeforeElement"]) {
                    setTimeout(function () {
                        const appendAfterThis = clone.parents(".clonable-block:first").find(`>.clonable:last.${rawClassPromiseToAppend}`);

                        appendAfterThis.after(clone);
                        // scroll into view 
                        smoothScrollToView(clone);

                    }, 100);
                }

                return window[tmpData1](clone, index, self);
            }

        }

        // fix beforeToggle callback
        if (mOptions['beforeToggle']) {
            let tmpData2 = mOptions['beforeToggle'];
            mOptions['beforeToggle'] = function (clone, index, self) {
                return window[tmpData2](clone, index, self);
            }
        }

        // fix afterDelete callback
        if (mOptions["afterDelete"]) {
            element.on("click", ".clonable-button-close", function () {
                window[mOptions["afterDelete"]](element.find(".clonable:first"));
            });
        }

        // set when clicked on plus element will after clicked element
        if (mOptions["appendAfterBeforeElement"]) {
            element.on("click", ".clonable-button-add", function () {
                const thisElement = $(this);

                // make sure old promise class removed 
                const oldPromise = thisElement.parents(".clonable-block:first").find("." + rawClassPromiseToAppend);
                oldPromise.removeClass(rawClassPromiseToAppend);

                const firstParentClonable = thisElement.parents(".clonable:first");
                firstParentClonable.addClass(rawClassPromiseToAppend);
            });
        }

        // set events
        if (mOptions["pureEvent"]) {
            for (const eventOne of mOptions["pureEvent"]) {
                element.on(eventOne["event"], eventOne["selector"], function (e) {
                    if (window[eventOne["callback"]])
                        window[eventOne["callback"]](e);
                    else {
                        setTimeout(() => window[eventOne["callback"]](e), 1000);
                    }
                });

                if (eventOne["event"].search("input") != -1 && eventOne["selector"]) {
                    element.find(eventOne["selector"]).trigger("input");
                }
            }
        }


        // set movement
        const movements = element.find(".action-movement");
        if (movements.length) {
            element.on("click", ".action-movement", function () {
                const thisElement = $(this);
                const currentClone = thisElement.parents(".clonable:first");

                const before_currentClone = currentClone.prev();
                const after_currentClone = currentClone.next();

                if (thisElement.hasClass("go-to-up") && before_currentClone.length) {
                    before_currentClone.before(currentClone);
                } else if (thisElement.hasClass("go-to-down") && after_currentClone.length) {
                    after_currentClone.after(currentClone);
                }

                // scroll into view s
                smoothScrollToView(currentClone);

            });

        }



        // empty clonable except first one
        const emptyClonable = element.find("#remove-elements");
        if (emptyClonable.length) {
            emptyClonable.on("click", function (e) {
                const thisElement = $(e.target);
                sweetAlertActionQuestion(thisElement.attr("data-label"), function () {

                    const clonbleBlock = thisElement.parents(".clonable-block:first()");

                    if (!clonbleBlock.length) return false;
                    const firstClonableElement = clonbleBlock.find(".clonable:first()");
                    clonbleBlock.find(".clonable").not(firstClonableElement).find(".delete-action").trigger("click");

                    firstClonableElement.find(".input-one").val(null);

                    const extraElement = clonbleBlock.find(".on-empty-all");
                    if (extraElement.length) {
                        extraElement.html(null);
                    }

                }, () => 1, null, __local("Do You Want To Delete All x-text"));

            })
        }

        const res = element.cloner(mOptions);

        return res;
    }

    window.generateColorPickerFeature = function (element, options = {}) {
        if (typeof $().colorpicker === "undefined") return false;

        const mOptions = getDataOptionPlugin(element, options);

        const res = element.colorpicker(mOptions);
        return res;
    }

    window.generateIonRangeSliderFeature = function (element, options = {}) {
        if (typeof $().ionRangeSlider === "undefined") return false;

        const mOptions = getDataOptionPlugin(element, options);

        const res = element.ionRangeSlider(mOptions);
        return res;
    }

    window.generateDadjFeature = function (element, options = {}) {
        if (typeof $().sortable === "undefined") return false;

        const mOptions = getDataOptionPlugin(element, options);
        element.sortable(mOptions);

        menuOrderToInput({
            data: {
                thisElement: element.get(0)
            }
        });

        return element;
    }

    // #### End Features

    // #### filter Functions
    window.onSwitchFilter = function (e) {
        const thisElement = $(e.target);
        const checkStatus = thisElement.prop('checked');

        const inputWrapper = $(filterOptions.inputWrapper + `[data-name=${thisElement.attr('data-id')}]`);
        const select2DOM = $(filterOptions.sortWrapper).find("select.select2");

        const dataset = {
            title: inputWrapper.find("center").text(),
            name: inputWrapper.attr("data-name")
        }

        const templateOption = generateFilterInput('option');

        if (checkStatus) {
            inputWrapper.addClass('active');

            const theTemplateDOM = $("th[data-name=\"" + dataset['name'] + "\"");
            const options = [];
            if (theTemplateDOM.attr("data-unsort") === undefined) {
                options.push(templateOption.replace(/x-title/gi, dataset['title'] + `: ${__local('ascending')}`).replace(/x-value/gi, dataset['name'] + ":asc"));
                options.push(templateOption.replace(/x-title/gi, dataset['title'] + `: ${__local('descending')}`).replace(/x-value/gi, dataset['name'] + ":desc"));
                select2DOM.append(options.join("\n"));
                select2DOM.trigger("change");
            }
        } else {
            inputWrapper.removeClass('active');
            const options = select2DOM.find(`option[value='${dataset['name'] + ":asc"}'],option[value='${dataset['name'] + ":desc"}']`)
            options.remove();
            select2DOM.trigger("clear");
        }

        const filterWrapper = inputWrapper.parents(filterOptions.wrapper);
        const firstInput = filterWrapper.find(filterOptions.valueInput).first();

        if (firstInput.length) {
            firstInput.trigger("input");
        }

    }

    window.generateFilterInput = function (inputName) {
        const columnResponsive = "col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4";

        const labels = {
            "from": __local("from"),
            "to": __local("to"),
        }

        const inputList = {
            "text": `<div class="input-wrapper-filter ${columnResponsive}" data-name="x-wrapper"> <center> <div>x-title</div> </center> <input class="form-control mt-2 the-value text-center" type="text" id="x-id"> </div>`,
            "numberSeperator": `<div class="input-wrapper-filter ${columnResponsive}" data-name="x-wrapper"> <center> <div>x-title</div> </center> <input type="text" data-seperator="true" class="form-control mt-2 the-value text-center" id="x-id"></div>`,
            "numberRange": `<div class="input-wrapper-filter multi-value ${columnResponsive}" data-name="x-wrapper"> <center> <div>x-title</div> </center> <input type="text" id="x-id" data-options='x-options' class="ion-ranger"> <label id="from-label" for="from">${labels['from']}</label> <input class="form-control form-control-sm from the-value text-center navigator" type="number" id="from" placeholder="${labels['from']}"> <input class="form-control form-control-sm to the-value navigator text-center" type="number" id="to" placeholder="${labels['to']}"> <label id="to-label" for="to">${labels['to']}</label> </div>`,
            "selectMultiple": `<div class="input-wrapper-filter multi-value ${columnResponsive}" data-name="x-wrapper"> <center class="mb-2"> <div>x-title</div> </center> <select class="form-control select2 select2-multiple select2-multiple-filter w-100" multiple data-empty-content="true" data-options='x-options'>x-options-value</select> <input type="text" id="x-id" class="d-none the-value select2-content-list" value=""> </div>`,
            "selectMultipleAjax": `<div class="input-wrapper-filter wrapper-ajax taxonomy multi-value ${columnResponsive}" data-name="x-wrapper"> <center class="mb-2"> <div>x-title</div> </center> <select class="form-control select2 select2ajax select2-multiple w-100" id="taxonomy-x-taxonomy" data-taxonomy="x-taxonomy" multiple data-empty-content="true" data-options='x-options'>x-options-value</select> <input type="text" id="x-id" class="d-none the-value select2-content-list" value=""> </div>`,
            "dateRange": `<div class="input-wrapper-filter multi-value ${columnResponsive}" data-name="x-wrapper"> <center class="mb-2"> <div>x-title</div> </center> <div class="row"> <div class="col-6"> <label for="from-date">${labels['from']}</label> <input type="text" id="from-date" class="form-control from the-value x-id date-picker-shamsi text-center" data-options='x-options'> </div> <div class="col-6"> <label for="to-date">${labels['to']}</label> <input type="text" id="to-date" class="form-control to the-value x-id date-picker-shamsi" data-options='x-options'> </div> </div> </div>`,
            "switch": `<div class="custom-control custom-switch mb-2" dir="ltr"> <input type="checkbox" class="custom-control-input active-filter" data-id="x-id" id="for-x-id" x-checked> <label class="custom-control-label" for="for-x-id">x-title</label> </div>`,
            "option": `<option value="x-value" x-selected>x-title</option>`,
        }

        if (!inputList[inputName]) return false;

        return inputList[inputName];
    }

    window.filterInputOnInput = function (e) {
        const thisElement = $(e.target);
        const thisElementParent = thisElement.parents(filterOptions.inputWrapper);
        let key = thisElementParent.attr('data-name');
        let value = '';


        if (!thisElementParent.hasClass("active")) return false;

        if (thisElementParent.find("input.from,input.to").length) {
            let from, to;
            from = thisElementParent.find('input.from').val();
            to = thisElementParent.find('input.to').val();
            value = `${from},${to}`;
        }
        else {
            value = thisElementParent.find(filterOptions.valueInput).attr('data-value') ? thisElementParent.find(filterOptions.valueInput).attr('data-value') : thisElementParent.find(filterOptions.valueInput).val();
        }

        const map = `${key}=${value}`;

        return map;

    }

    window.checkQueryForFilters = function () {

        if (!$(filterOptions.wrapper).length) return false;

        let params = getCurrentQuery();
        params = Array.from(params.entries());

        const iteratedList = [];

        for (const param of params) {
            const key = param[0];
            const value = param[1];

            if (iteratedList.includes(key)) continue;
            if (value == "" || value == ",") continue;

            const inputWrapper = $(`div[data-name=${key}`);
            const switchWrapper = $(filterOptions.switchWrapper);

            const select2DOM = inputWrapper.find('select.select2');

            if (!inputWrapper.length) continue;

            iteratedList.push(key);

            if (switchWrapper.length) {

                const activeSwitch = switchWrapper.find(`.active-filter[data-id="${key}"]`);

                if (activeSwitch.length) {
                    activeSwitch.prop("checked", true);
                    onSwitchFilter({
                        target: activeSwitch
                    });
                }

            }

            if (value.search(/,/gi) != -1) {
                const multiVal = value.split(",");
                const from = multiVal[0];
                const to = multiVal[1];
                const fromDOM = inputWrapper.find('input.from');
                const toDOM = inputWrapper.find('input.to');

                if (fromDOM.length && toDOM.length) {
                    fromDOM.val(from).trigger('input');
                    toDOM.val(to).trigger('input');
                } else if (select2DOM.length) {

                    select2SetValue(select2DOM, multiVal);

                    const select2ContentList = inputWrapper.find('.select2-content-list');
                    if (select2ContentList.length) {
                        select2ContentList.val(multiVal.join(","));
                    }
                }

            } else {
                inputWrapper.find(filterOptions.valueInput).val(value).trigger('input');
                if (select2DOM.length) {
                    select2SetValue(select2DOM, value);
                }

            }
        }

        if (iteratedList.length) {
            $(filterOptions.blockWrapper).removeClass('collapse').addClass('collapsed show')
        }



    }

    window.generateFilterForm = function (elements, filterWrapper, swtichWrapper, sortWrapper) {

        if (!filterWrapper.length) return false;

        const select2SortDOM = sortWrapper.find('select.select2');

        for (let element of elements) {
            element = $(element);

            const dataset = {
                type: null,
                options: null,
                values: null,
                name: null,
                title: null,
            }

            dataset['type'] = element.attr('data-input');
            dataset['options'] = element.attr('data-options') ? element.attr('data-options') : '';
            dataset['extra'] = element.attr('data-extra') ? JSON.parse(element.attr('data-extra')) : '';
            dataset['values'] = element.attr('data-values') ? JSON.parse(element.attr('data-values')) : [];
            dataset['name'] = element.attr('data-name');
            dataset['title'] = element.text();

            if (!dataset['type']) return false;

            const template = generateFilterInput(dataset['type']);
            if (!template) return false;


            // switch filter for appended element
            const templateSwitch = generateFilterInput('switch');
            let templateSwitchDynamic = templateSwitch.replace(/x-title/gi, dataset['title']).replace(/x-id/gi, dataset['name']);

            const taxonomy = dataset['extra'] ? dataset['extra']['taxonomy'] : '';
            const tempateDynamic = template.replace(/x-wrapper/gi, dataset['name']).replace(/x-title/gi, dataset['title']).replace(/x-id/gi, dataset['name']).replace(/x-options-value/gi, dataset['values'].join("\n")).replace(/x-options/gi, dataset['options']).replace(/x-taxonomy/gi, taxonomy);

            filterWrapper.append(tempateDynamic);
            swtichWrapper.append(templateSwitchDynamic);
        }

        const sortDOM = sortWrapper.find(filterOptions.valueInput);
        if (sortDOM.length) {
            if (sortDOM.val() == "") {
                sortDOM.val(select2SortDOM.val());
            }
        }

        const inputWrapper = filterWrapper.find(filterOptions.inputWrapper);
        const theValue = inputWrapper.find(filterOptions.valueInput);
        theValue.on('input seperatedvalue', function (e) {
            const queryList = [];
            for (let valDOM of theValue) {
                const res = filterInputOnInput({
                    target: valDOM
                });

                if (!res) continue;

                if (!queryList.includes(res))
                    queryList.push(res);
            }

            const query = "?" + queryList.join("&");

            const form = $(filterOptions.form);
            form.attr('action', form.attr('data-route') + query)

        });

    }

    // #### End filter Functions

    // #### Single

    window.loadMetaDataByJs = function () {
        const parentName = 'meta';
        loadInputValueByJs(parentName);

        /*if (!jsonDataServer.keymap_data_page) return false;


        const data = jsonDataServer.keymap_data_page[parentName];
        if (data) {
            for (const element of data) {
                const childName = element.key;
                const finalName = `${parentName}:${childName}`;
                const theDOM = $(`[data-group-id="${finalName}"]`);

                if (theDOM.length) {
                    theDOM.val(element.value);
                }
            }
        }*/
    }

    // #### End Single

    // #### Dom functions

    window.initToolTip = function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            const element = $(tooltipTriggerEl);
            element.attr("data-clipboard-text", element.attr("title"));
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: element.attr("data-clipboard-html") == "true"
            })
        })

        return tooltipList;
    }

    window.menuOrderToInput = function (evt) {
        const thisElement = evt.data && evt.data.thisElement != undefined ? $(evt.data.thisElement) : $(this);
        const dataField = $(thisElement.attr('data-field'));
        const jsonData = JSON.stringify(thisElement.sortable('serialize'));
        dataField.val(jsonData);
        dataField.trigger("input");
    }

    window.elementSeperator = function (index, element) {
        const currentElement = $(element);

        let val = currentElement.text();
        let finalVal = seperateNumber(val, true);
        currentElement.text(finalVal['seperatedValue'])

        currentElement.attr('data-value', finalVal['value']);
    }

    window.onInputNumberSeperator = function (e) {
        const thisElement = $(e.target);

        const val = seperateNumber(thisElement.val());
        const seperatedValue = val['seperatedValue'] === false ? '' : val['seperatedValue'];

        thisElement.val(seperatedValue);
        thisElement.attr('data-value', val['value']);
        thisElement.trigger('seperatedvalue');
    }

    window.onMultipleSelect2Change = function (e, extraParam = null) {
        const thisElement = $(e.target);
        if (extraParam == "queryCheck" && (thisElement.val() == null || thisElement.val() == "")) {
            const firstOption = thisElement.find("option").first();
            if (firstOption.length)
                thisElement.val(firstOption.val()).trigger("change");
        }

        const thisElementParent = thisElement.parent();
        const thisElementContentList = thisElementParent.find('.select2-content-list');
        if ((thisElementContentList.length && thisElement.val() != "") || (thisElement.attr("data-empty-content"))) {
            thisElementContentList.val(thisElement.val());
            thisElementContentList.trigger("input")
        }

    }

    window.buttonFormAction = function (event) {
        const currentElement = $(event.target);

        const triggerData = event.triggerData;

        const idForm = currentElement.attr("data-id-form");

        const actionCallback = currentElement.attr("data-callback");


        const parent = currentElement.parents(idForm);
        const form = parent.length ? parent : $(idForm);
        const theID = currentElement.parents("td:first").attr('data-action-id');

        if (!form.length) {
            alert("form not found");
            return false;
        }

        const eventList = {
            thisElement: currentElement,
            form: form,
            pageType: currentElement.attr("data-page-type"),
            theID: theID
        }

        window[actionCallback](eventList, triggerData);
    }

    window.submitFormActionClick = function (data) {
        const elements = jsonDataServer['keymap_json_input'][data.pageType]['elements'];
        const elementsKey = Object.keys(elements);
        for (let element of elementsKey) {
            //break;
            let theDOM = $(`#${element}`);
            if (!theDOM.length) theDOM = $(`[data-group-id='${element}']`);


            if (!theDOM.length) continue;

            let val = theDOM.val().toString();

            if (val.trim() === "") {
                const options = instanceJson({ ...sweetAlertOptions.info });
                delete (options['html']);

                options.title = __local("Required Field");
                options.text = __local("fill out field x-text");
                options.text = options.text.replace(/x-text/gi, theDOM.attr("data-label"));

                Swal.fire(options);
                return false;
            }
        }

        data.form.submit();
    }

    window.deleteFormActionClick = function (data, extra = {}) {
        const thisElement = data.thisElement;

        if (data.theID) {
            data.form.find("#id").val(data.theID);
        }

        const label = thisElement.attr("data-label") ? thisElement.attr("data-label") : thisElement.parents("[data-label]").attr("data-label");

        if (extra.isTriggering === null || extra.isTriggering === undefined)
            sweetAlertActionQuestion(label, function () { makeElementLoading($("body"));data.form.submit(); }, function () { }, extra.title, extra.text);
    }

    window.setStatusFormActionClick = function (data, extra = {}) {
        const thisElement = data.thisElement;

        var actionCallbackZero = thisElement.attr("data-callback-zero");

        const submitTheFormCbk = function () {
            if (actionCallbackZero) {
                window[actionCallbackZero](data);
            } else {
                data.form.submit()
            }
        }

        if (data.theID) {
            data.form.find("#id").val(data.theID);
        }


        const action = thisElement.attr("data-action");
        const label = thisElement.attr("data-label") ? thisElement.attr("data-label") : thisElement.parents("[data-label]").attr("data-label");
        const prompt = thisElement.attr("data-prompt");

        data.form.find("#action").val(action).trigger("change");

        if (extra.isTriggering === null || extra.isTriggering === undefined) {
            if (prompt) {
                extra.text = prompt;
                sweetAlertActionQuestion(label, function () {
                    submitTheFormCbk();
                }, function () { }, extra.title, extra.text);
            } else {
                submitTheFormCbk();
            }
        }

    }

    window.applyCloneClickHandler = function () {
        const thisElement = $(this);

        const idEntity = parseInt(thisElement.attr("data-id-entity"));
        const metaIncludes = thisElement.attr("data-meta-includes");

        const mAjaxOptions = instanceJson(ajaxOptions);
        mAjaxOptions['url'] = "/check/data/get/clonePostTypeRest";
        mAjaxOptions['data'] = {
            "_token": getCsrf(),
            "post_type_id": idEntity,
            "clone_type": thisElement.parents(".wrapper").find("#clone").val(),
            "clone_include_keys": metaIncludes
        }
        mAjaxOptions['error'] = function (e) {
            ajaxErrorHandler(e);
        }
        mAjaxOptions['success'] = function (res) {
            const response = res.data;
            const options = { ...sweetAlertOptions.info };
            options.title = __local('Server Message');
            options.text = "";

            if (response) {
                options.text = __local("Successfully Cloned");
            } else if (!response) {
                options.text = __local("Fail");
            }

            if (options.text) {
                swal.fire(options);
            }

        }

        window['postRequestXhr'] = false;

        postRequest(mAjaxOptions)
    }

    window.cancelFormActionClickFormActionClick = function (data, extra = {}) {
        const thisElement = data.thisElement;

        if (data.theID) {
            data.form.find("#id").val(data.theID);
        }

        const label = thisElement.attr("data-label") ? thisElement.attr("data-label") : thisElement.parents("[data-label]").attr("data-label");

        sweetAlertActionQuestion(label, function () { data.form.submit() }, function () { }, extra.title, __local(`Do You Want To Cancel x-label`).replace("x-label", label));
    }

    window.deactiveFormActionClick = function (data) {
        deleteFormActionClick(data, {
            text: __local("Do You Want To Deactive x-text")
        });
    }

    window.deactive_blockFormActionClick = function (data) {
        deleteFormActionClick(data, {
            text: __local("Do You Want To Deactive (Block) x-text")
        });
    }

    window.activeFormActionClick = function (data) {
        deleteFormActionClick(data, {
            text: __local("Do You Want To Active x-text")
        });
    }

    window.confirmFormActionClick = function (data) {
        data.form.find("#id").val(data.theID);
        data.form.submit();
    }

    window.ReplyFormActionClick = function (data) {
        const parent = data.thisElement.parents("tr:first");
        const target = $(data.thisElement.attr('data-target'));

        const fullname = parent.find("#fullname_th");
        const post_type_id = data.thisElement.parents("td").first().attr("data-action-pid");

        target.find("h5 strong").text(fullname.text().trim());
        data.form.find("#id").val(data.theID);
        data.form.find("#post_type_id").val(post_type_id);
    }

    window.deletePreviewImageClick = function (data) {
        const thisElement = data.thisElement;
        const thisElementParent = thisElement.parents(".wrapper-thumbnail-preview:first");
        const thisElementParentParentParent = thisElementParent.parent().parent();

        thisElementParent.remove();

        if (!thisElementParentParentParent.find(".wrapper-thumbnail-preview").length) {
            const fileInput = thisElementParentParentParent.find(".file-input:first");
            fileInput.val(varList.noImageSrc);
            const targetSelector = "#" + fileInput.attr("id");
            const target = $(targetSelector);
            target.attr('data-placeholder', "true");

            let selectorPreview = "#thumbnails-preview";

            if (!thisElementParentParentParent.find(selectorPreview).length) {
                selectorPreview = selectorPreview + "-" + thisElementParentParentParent.attr("data-num");
            }

            previewImages({
                previewSelector: selectorPreview,
                target: targetSelector,
            });

            setTimeout(function () { fileInput.val(null).trigger("unload"); }, 100)
        }

        checkForUrlByDom(thisElementParentParentParent, ".thumbnail-preview", "#thumbnail_url");
    }

    window.checkForUrlByDom = function (parent, elementSelector, inputSelector) {
        const data = [];
        parent.find(elementSelector).each(function (index, element) {
            const url = $(element).attr("src");
            if (url) data.push(url);
        });

        const urls = data.join(",");
        parent.find(inputSelector).val(urls);
        if (data.length == 1 && data[0] == varList.noImageSrc) {
            setTimeout(function () { parent.find(inputSelector).val(" ") }, 101);
        }
    }

    window.previewImages = function (data) {
        const previewWrapper = $(data.previewSelector).first();
        const inputTarget = $(data.target).first();

        previewWrapper.html(null);

        const urls = inputTarget.val().split(",");

        const labelDelete = __local('Delete');
        let template = `<div class="wrapper-thumbnail-preview"> <img class="thumbnail-preview w-100" src="x-url"> <input data-callback="deletePreviewImageClick" data-label="image" class="btn btn-danger mt-3" onclick="buttonFormAction(event)" id="delete_action" type="button" value="${labelDelete}"> </div>`;

        for (const url of urls) {
            const dynamicTemplate = $(template.replace(/x-url/gi, url));

            if (inputTarget.attr('data-placeholder') !== undefined) {
                inputTarget.removeAttr('data-placeholder');
                dynamicTemplate.find("#delete_action").remove();
            }

            previewWrapper.append(dynamicTemplate);
        }
    }

    window.syncRangeSliderAndInput = function (data) {

        const thisElement = data.input;
        const thisElementParent = thisElement.parent();

        const fromDOM = thisElementParent.find(".navigator#from");
        const toDOM = thisElementParent.find(".navigator#to");

        if (fromDOM.length && toDOM.length) {


            if ((!fromDOM.attr('data-initial') && !toDOM.attr('data-initial'))) {

                function syncInitialValueToRanger(e) {
                    if (!e.thisElement.data('ionRangeSlider')) {
                        setTimeout(syncInitialValueToRanger, 100, e);
                        return false;
                    }

                    if (e.fromDOM.val() != "" && e.toDOM.val() != "") {
                        e.thisElement.data('ionRangeSlider').update({
                            from: fromDOM.val(),
                            to: toDOM.val()
                        });
                    } else {
                        e.fromDOM.val(e.from);
                        e.toDOM.val(e.to);
                    }


                    e.fromDOM.attr('data-initial', "true");
                    e.toDOM.attr('data-initial', "true");
                }

                setTimeout(syncInitialValueToRanger, 100, {
                    fromDOM: fromDOM,
                    toDOM: toDOM,
                    thisElement: thisElement,
                    from: data.from,
                    to: data.to,
                })


            } else {
                if (typeof data['from'] != "undefined") {
                    fromDOM.val(data.from)
                    fromDOM.trigger("input")
                }

                if (typeof data['to'] != "undefined") {
                    toDOM.val(data.to)
                    toDOM.trigger("input")
                }
            }


            function onInput(e) {
                const thisElement = $(this);
                const thisElementVal = thisElement.val();
                const ion = e.data.ion;

                if (!isNaN(Number(e.data.min)) && !isNaN(Number(e.data.max))) {

                    const options = { ...sweetAlertOptions.info };
                    options.title = __local("limitation");
                    options.text = __local("passed value is less or more than limitation");
                    if (!(e.data.min <= thisElementVal)) {
                        Swal.fire(options);
                        thisElement.val(e.data.min);
                        return false;
                    } else if (!(thisElementVal <= e.data.max)) {
                        Swal.fire(options);
                        thisElement.val(e.data.max);
                        return false;
                    }
                }


                if (thisElement.attr('id') == "from")
                    ion.data('ionRangeSlider').update({ from: thisElementVal })
                else if (thisElement.attr('id') == "to") {
                    ion.data('ionRangeSlider').update({ to: thisElementVal })
                }

            }

            fromDOM.off("input", onInput).on("input", {
                ion: thisElement,
                from: data.from,
                to: data.to,
                min: data.min,
                max: data.max
            }, onInput);


            toDOM.off("input", onInput).on("input", {
                ion: thisElement,
                from: data.from,
                to: data.to,
                min: data.min,
                max: data.max
            }, onInput)

        }
    }

    window.sendAlertAjax = function (e) {
        const thisElement = $(this);

        const localCbk = thisElement.attr("data-alt-callback");
        const serverCbk = thisElement.attr("data-alt-action");
        const args = JSON.parse(thisElement.attr("data-alt-args"));
        const type_id = JSON.parse(thisElement.attr("data-alt-type-id"));

        if (!serverCbk) return false;

        thisElement.attr("disabled", "disabled");

        const mAjaxOptions = instanceJson(ajaxOptions);
        mAjaxOptions['url'] = "/check/data/get/" + serverCbk;


        args['_token'] = getCsrf();
        args['type_id'] = type_id;

        mAjaxOptions['data'] = args;

        // exception fix delivery fail sometimes
        window['postRequestXhr'] = false;

        mAjaxOptions['success'] = function (res) {
            window['postRequestXhr'] = false;
            thisElement.removeAttr("disabled");
            thisElement.addClass("text-success font-weight-bold");
            res = res.data;

            if (localCbk) {
                window[localCbk](res, args);
            }



        }

        mAjaxOptions['error'] = function (e) {
            ajaxErrorHandler(e);
            window['postRequestXhr'] = false;
            thisElement.removeAttr("disabled");
        }

        postRequest(mAjaxOptions);
    }

    // #### END Dom functions 

    window.toggleActiveClonable = function (element, state = true) {
        if (state) {
            element.removeClass('border-dark').addClass('border-warning active-one');
        } else {
            element.removeClass('border-warning active-one').addClass('border-dark');
        }
    }

    window.getTipForPropertiesDOM = function (wrapper, description, htmlCode) {

        const list = [];

        const tipDOM = wrapper.find(".tip");

        if (!tipDOM.length) return list;

        // wrapper tip
        list.push(tipDOM);
        // description
        list.push(tipDOM.find(".description"))
        // code
        list.push(tipDOM.find(".sample"))

        return list;
    }

    window.addTipForProperties = function (wrapper, description, htmlCode) {

        const list = getTipForPropertiesDOM(wrapper);

        if (!list.length) return;

        const [tipDOM, descriptionDOM, sampleDOM] = list;

        tipDOM.removeClass('d-none');

        // description
        descriptionDOM.html(description);

        // code
        sampleDOM.html(htmlCode);
    }

    window.resetTipForProperties = function (wrapper) {
        const list = getTipForPropertiesDOM(wrapper);

        if (!list.length) return;

        const [tipDOM, descriptionDOM, sampleDOM] = list;

        tipDOM.addClass('d-none');

        // description
        descriptionDOM.html('');

        // code
        sampleDOM.html('');
    }

    window.emptyAndDisableFormInputProperties = function (element) {
        element.attr('disabled', 'disabled').val(null).trigger('input');
    }

    window.normalizeFormInputProperties = function (element) {
        element.removeAttr('disabled');
    }

    window.loadFormTemplateData = function (tagName, jsonDetails) {

        const targetSourceDOM = $('.item-input.active-one');

        const funcs = {
            "general": function (details, targetSourceDOM) {

                const formInputItems = $('.input-form-prop-one');
                for (let formInputItem of formInputItems) {
                    formInputItem = $(formInputItem);

                    const id = formInputItem.attr('id');
                    const valueDetail = details[id] ?? "";

                    if (formInputItem.attr('type') == "checkbox") {
                        formInputItem.prop("checked", Boolean(valueDetail));
                    } else {
                        formInputItem.val(valueDetail);

                        if (formInputItem.attr('class').search('select2') != -1) {
                            formInputItem.trigger('change');
                        }
                    }
                }

                // trigger to update json and active clonable
                formInputItems.first().trigger({
                    type: 'input',
                });
            },

            "checkbox_TT": function (details, targetSourceDOM) {
                //
            }
        }

        let cbk = funcs[tagName] ?? null;

        if (!cbk) cbk = funcs['general'];

        cbk(jsonDetails, targetSourceDOM);
    }

    window.resetFormInputProperties = function (wrapper) {
        const inputs = wrapper.find('.input-form-prop-one:not(#type-input-form)');

        for (let input of inputs) {
            input = $(input);

            if (input.attr('type') == "checkbox") {
                input.prop("checked", false);
            } else {
                input.val(null);
            }

        }

        inputs.last().trigger("input")
    }

    window.updateFormSchema = function (activeClonableItem) {
        const parents = activeClonableItem.parents('.input-wrapper');
        const jsonDOM = parents.find('.the-json');
        const clonableItems = parents.find('.item-input');

        const map = [];


        for (let clonableItem of clonableItems) {
            clonableItem = $(clonableItem);
            const currentMap = JSON.parse(clonableItem.attr('data-details'));

            map.push(currentMap);
        }

        const mapStr = JSON.stringify(map);

        jsonDOM.val(mapStr);
    }

    window.getActiveClonableItemForm = function () {
        const dom = $('.item-input.active-one');
        return dom;
    }

    window.typeFormInputEventInput = function (e) {

        const wrapperProperties = $('.wrapper-properties');
        // get json for callbacks
        const theJson = JSON.parse(wrapperProperties.attr('data-json'));

        const thisElement = $(this);
        const tagName = thisElement.val();
        const activeClonableItem = getActiveClonableItemForm();

        const getTagDetailsCbk = function (tagName) {
            const tagDetails = theJson[tagName];

            const cbkList = tagDetails['cbk'];

            return cbkList;
        }

        const cbkList = getTagDetailsCbk(tagName);

        // check for old tag select to undo some actions
        const oldTagName = '_oldActionChangeFormPropertiesTag';
        if (window[oldTagName]) {
            const cbkListOld = getTagDetailsCbk(window[oldTagName]);

            if (cbkListOld['onBlur']) {
                const cbk = new Function(cbkListOld['onBlur']);
                cbk();
            }
        }

        if (cbkList['onSelect']) {
            const cbk = new Function(cbkList['onSelect']);
            cbk();

            window[oldTagName] = tagName;
        }

        // get details from active clonable item
        if (activeClonableItem.length && !e.details) {
            e.details = JSON.parse(activeClonableItem.attr('data-details'));
            e.details['type-input-form'] = tagName;
        }


        // load data if details passed
        if (cbkList['onLoadData'] && e.details) {

            const cbk = new Function('tagName , details', cbkList['onLoadData']);
            cbk(tagName, e.details);
        }
    }

    window.updateFormInputJsonSchemaInput = function (e) {
        const thisElement = $(this);
        const parent = thisElement.parents('.wrapper-properties');
        const activeClonableItem = getActiveClonableItemForm();

        const allInputs = parent.find('.input-form-prop-one');

        if (!activeClonableItem.length) return;

        const map = {};

        for (let theInput of allInputs) {
            theInput = $(theInput);
            const id = theInput.attr('id');

            let value = null;

            if (theInput.attr('type') == "checkbox") {
                const isChecked = theInput.prop('checked');
                value = isChecked ? theInput.val() : null;
            } else {
                value = theInput.val();
            }


            map[id] = value;
        }


        // update clonable details attr
        activeClonableItem.attr('data-details', JSON.stringify(map));

        // update input name 
        activeClonableItem.find('[data-field=\"name\"]').val(map['name']);

        updateFormSchema(activeClonableItem);

    }

    window.activeClonableItemForm = function (e) {
        const targetElement = $(e.target);
        const thisElement = $(this);

        // if on actions clicked abort
        if (targetElement.parents('.action-wrapper').length != 0 || thisElement.hasClass("active-one")) {
            return;
        }

        const parent = thisElement.parents('.clonable-block');

        // deactive all `clonable`
        toggleActiveClonable(parent.find('.clonable'), false);

        // highlight selected clonable
        toggleActiveClonable(thisElement);


        // hidden helper text properties
        const textHelper = $('.text-help-properties');
        if (textHelper.hasClass('d-block')) {
            textHelper.removeClass('d-block').addClass('d-none');

        }

        // load item details on properties
        const jsonDetails = JSON.parse(thisElement.attr('data-details'));


        // trigger change type input based on json data and also load data by passed details
        $('#type-input-form').val(jsonDetails['type-input-form']).trigger({
            type: 'input',
            clonableID: thisElement.attr('id'),
            details: jsonDetails
        });


        // if it's fresh clonable reset input values
        if (Object.keys(jsonDetails).length == 1 && jsonDetails['type-input-form']) {
            resetFormInputProperties($(".wrapper-properties"));
        }

    }

    window.showRequiredSignForInputs = function (propertiesWrappers) {
        for (let propertiesWrapper of propertiesWrappers) {
            propertiesWrapper = $(propertiesWrapper);
            const label = propertiesWrapper.find('label');
            const input = propertiesWrapper.find('.input-form-prop-one');

            if (input.attr("required")) {
                label.html(label.html() + ` <span title='${__local('Required Field')}' class='text-danger ml-2 mr-2'>*</span>`)
            }

        }
    }

    /* ================> END Functions */

    /* ================> options */

    window.optionsSimpleBar = {
        autoHide: false
    }

    window.filterOptions = {
        wrapper: '#filter-wrapper',
        inputWrapper: '.input-wrapper-filter',
        form: '#filter-form',
        switchWrapper: '.filter-checklist .dropright .dropdown-menu',
        sortWrapper: '.select2-wrapper',
        blockWrapper: '#block-wrapper-filter',
        clearFilter: '#clear-filter',
        valueInput: '.the-value'
    }

    window.ionRangeSliderOptions = {
        skin: "round",
        type: "double",
        grid: true,
        min: 0,
        max: 1000,
        from: 100,
        to: 990,
        step: 1,
        prettify_separator: ",",
        postfix: '',
        prefix: '',
        onStart: syncRangeSliderAndInput,
        onChange: function () { },
        onFinish: syncRangeSliderAndInput,
        onUpdate: function () { }
    }

    window.defaultDataTablesOptions = {
        order: [],
        scrollX: false,
        columnDefs: [],
        paging: false,
        language: {
            lengthMenu: __local("record _MENU_"),
            zeroRecords: __local("No Data"),
            info: "",
            infoEmpty: __local("No Data"),
            infoFiltered: __local("filter _MAX_"),
            search: __local("Search") + " ",
            loadingRecords: __local("Loading"),
            processing: __local("Processing"),
            paginate: {
                first: __local("first"),
                last: __local("last"),
                next: __local("next"),
                previous: __local("previous")
            },
            aria: {
                sortAscending: __local(": sort ascending"),
                sortDescending: __local(": sort descending")
            }
        }

    }

    window.dadjOption = {
        update: menuOrderToInput,
    }

    const theStyle = [
     
    ];

    // custom persian font
    if($("html").attr("lang") == "fa_IR"){
        theStyle.push("@import url('/static/fonts/peydaweb/font.css?version=1.0.0');");
    }else{
        theStyle.push("@import url('/static/libs/google-fonts/Lato/css/google-lato.css?version=1.0.0');body { font-family: Lato;font-weight:300 }");
    }

    // placeholder color in dark mode
    if($("body.dark-mode").length){
        theStyle.push(".mce-content-body[data-mce-placeholder]:not(.mce-visualblocks)::before{color:rgba(255,255,255,0.5)}");
    }
    
    window.tinymceOptions = {
        content_style: theStyle.join("\n"),
        directionality: $("html").attr("dir"),
        language: $("html").attr("lang").replace("_IR", ""),
        relative_urls: false,
        selector: "",
        placeholder: "",
        force_p_newlines: false,
        force_br_newlines: true,
        convert_newlines_to_brs: false,

        skin:  $("body.dark-mode").length ? "oxide-dark" : null,
        content_css: $("body.dark-mode").length ? "dark" : null,
        
        remove_linebreaks: true,
        setup: function () { },
        height: 500,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table directionality emoticons template paste"
        ],
        toolbar_mode: 'floating',
        toolbar: "insertfile ltr rtl | styleselect | image csVideo | alignGroup | featuredFormat | link | bullist numlist | outdent indent | print preview fullpage",
        style_formats: [
            { title: 'Head 1', block: 'h1' },
            { title: 'Head 2', block: 'h2' },
            { title: 'Head 3', block: 'h3' },
            { title: 'Head 4', block: 'h4' },
            { title: 'Head 5', block: 'h5' },
            { title: 'Head 6', block: 'h6' },
            { title: 'Bold text', inline: 'b' },
            { title: 'Red text', inline: 'span', styles: { color: '#ff0000' } },
            { title: 'Example 1', inline: 'span', classes: 'example1' },
        ]
    }

    window.fileMangerOptions = {
        multiple: false,
        dataMutipleMaxItem: 0,
        groupType: "all",
        target: null,
        dialogReload: true,
        url: "/check/data/get/getfilerest",
        iconFileUrl: "/static/images/file.png",
        preview: false,
        previewSelector: "#preview-thumbnails",
        onCloseCallback: null,
    }

    window.select2AjaxOptions = {
        language: {
            errorLoading: function () { return __local("The results could not be loaded.") },
            inputTooLong: function (e) { var n = e.input.length - e.maximum, r = __local(`Please delete x-chr character/s`).replace("x-chr", n); return 1 != n && r },
            inputTooShort: function (e) { const chrNumber = (e.minimum - e.input.length); return __local(`Please enter x-chr or more characters`).replace("x-chr", chrNumber) },
            loadingMore: function () { return __local("Loading more results…") },
            maximumSelected: function (e) { var n = __local("You can only select x-chr item").replace("x-chr", e.maximum); return 1 != e.maximum && n },
            noResults: function () { return __local("No results found") },
            searching: function () { return __local("Searching…") },
            removeAllItems: function () { return __local("Remove all items") },
            removeItem: function () { return __local("Remove item") },
            search: function () { return __local("Search") }
        },
        ajax: {
            url: "/check/data/get/gettaxonomyrest",
            type: "POST",
            dataType: 'json',
            data: function (params) {

                const taxonomy = $(this).attr('data-taxonomy');

                let query = {
                    _token: getCsrf(),
                    q: params.term,
                    taxonomy: taxonomy
                }

                query = select2AjaxExtraParams($(this), query);

                return query;
            },
            processResults: function (res) {

                return {
                    results: res.data,
                };
            },

            cache: true
        },
        generatedClass: 'select2-hidden-accessible',
        placeholder: __local('Select ...'),
        allowClear: true,
        minimumInputLength: 1,
        templateResult: function (data) { },
        templateSelection: function (data) { }
    };

    window.select2Options = { minimumResultsForSearch: Infinity }

    window.quickCreateOptions = {
        html_taxonomy: `<div class="quick-create-wrapper collapse" id="quick-create-wrapper-"> <input type="text" class="quick_input form-control mt-2" id="quick_title" data-name="title" data-label="${__local("Title")}" placeholder="${__local('Title')}" value=""><input type="text" class="quick_input form-control mt-2" id="quick_slug" data-name="slug" data-label="${__local('Slug')}" placeholder="${__local('Slug')}" value=""><button type="button" id="add-quick-element" class="btn active-bg mt-2">${__local("Add")}</button> </div>`,
    }

    window.varList = {
        displayBlock: "d-block",
        displayNone: "d-none",
        noImageSrc: jsonDataServer['no_image_source'],
        loopBreak: null,
        DOM: null
    }

    window.columns = {
        xs: 0,
        sm: 576,
        md: 768,
        lg: 992,
        xl: 1200,
    }

    window.columnCallbackList = {
        xs: ["makeSidebarDashboardDeactive"],
        sm: ["makeSidebarDashboardDeactive"],
        md: ["makeSidebarDashboardDeactive"],
        lg: ["makeSidebarDashboardActive"],
        xl: ["makeSidebarDashboardActive"],
    }

    /* ================> END options */


    /* ================> EVENTS */
    liDepth1.on("click", liDepth1Click);

    // get current column state
    $(window).on("resize", () => {
        window.currentBootstrapColumn = getCurrentBootstrapColumn();
    });

    // call function on every column
    $(window).on("resize", callColumnCallback)

    /* ================> END EVENTS */

    /* ================> INIT */

    checkForActiveLi(allLiList);
    setHeightAsideNav();
    checkForActiveLiRemove();

    // get current column state (sm,md,lg,xl)
    window.currentBootstrapColumn = getCurrentBootstrapColumn();

    // call function on every column
    callColumnCallback();

    // sidebar handler responsive
    $(document).on("click", ".sidebar-handler", sidebarHandler);

    // filter list init
    generateFilterForm($('[data-filter]'), $(filterOptions.wrapper), $(filterOptions.switchWrapper), $(filterOptions.sortWrapper));

    // simple bar general init
    $('.simple-bar').each(function (index, element) {
        generateFeatureSimpleBar($(element), optionsSimpleBar);
    });

    // tinymce init
    const textEditorsSelector = ".editor";
    const editorWYS = $(textEditorsSelector);
    if (editorWYS.length) {
        const myTinyMce = tinymceOptions;
        Array.from(editorWYS).forEach(function (textEditor, index) {
            myTinyMce['selector'] = textEditorsSelector + ":eq(" + index + ")";
            myTinyMce['placeholder'] = $(textEditor).attr("placeholder");
            myTinyMce['setup'] = setupTinyMce;
            generateFeatureTinyMce(myTinyMce);
        })
    }

    // Select2 Regular init
    $('.select2-simple').each(function (index, element) {
        element = $(element);
        generateSelect2Feature(element, select2Options).on('change', onMultipleSelect2Change)
        element.trigger("change");
    });

    // Select2 Ajax wrapper
    select2AjaxOptions.templateResult = showSelect2AjaxResult;
    select2AjaxOptions.templateSelection = showSelection2AjaxResult;

    // Select2 Ajax Taxonomy init
    select2AjaxOptions['allowClear'] = false;
    $('.taxonomy .select2.select2ajax').each(function (index, element) {
        element = $(element);
        generateSelect2Feature(element, select2AjaxOptions).on('change', onMultipleSelect2Change);
        element.trigger("change");
    });

    $(".quick-create").each(function (index, element) {
        element = $(element);
        const IDNumber = (index + 1);
        const targetID = "quick-create-wrapper-" + IDNumber;
        element.find(".quick-add").attr("data-toggle", "collapse").attr("aria-expanded", "false").attr("data-target", "#" + targetID)
        const childElement = $(quickCreateOptions.html_taxonomy);
        childElement.attr("id", targetID);
        element.append(childElement);

        $(".quick-create-wrapper #add-quick-element").off("click");
        $(".quick-create-wrapper #add-quick-element").on("click", addQuickElementTaxonomy)
    });


    // clipboard init
    if ($('.btn-clipboard').length) {
        generateClipboardFeature('.btn-clipboard');
    }

    // file manager init
    setTimeout(function () {
        $(".openTheFileManager").each(function (index, element) {
            generateFileManagerFeature($(element), fileMangerOptions);
        }, 1000)
    });

    // datatable init
    $('.datatable').each(function (index, element) {
        generateDataTableFeature($(element), defaultDataTablesOptions);

    });

    // refresh table and align column and rows by trigger resize
    if ($('.datatable').length) {
        setTimeout(() => $(document).trigger("resize"), 1000);
    }

    // persianDatePicker init
    $('input.date-picker-shamsi').each(function (index, element) {
        element = $(element);
        generatePersianDatepickerFeature(element, shamsiDatePickerOptions)
    });

    // Picker IOS Time init
    $('.picker-ios').each(function (index, element) {
        element = $(element);
        generatePickerIOSFeature(element, pickerIOSOptions)
    });

    // jquery cloner init
    $('.clonable-block').each(function (index, element) {
        element = $(element);
        generateJqueryCloner(element)
    });

    // color picker init
    $('.color-picker').each(function (index, element) {
        element = $(element);
        generateColorPickerFeature(element)
    });

    // password eye check init
    $('input.password-eye').each(function (index, element) {
        element = $(element);
        initEyeOnPassword(element);
    });

    // init on key up input
    $('input.on-key-event-check').each(function (index, element) {
        element = $(element);
        const eventName = element.attr("data-event-name");
        const cbk = element.attr("data-keyup-callback");

        runUntilExists([window, cbk], { "action": function () { element.on(eventName, window[cbk]) }, "args": [] })

    });

    // number seperator all element init
    $('[data-seperator]').each(elementSeperator)

    // number seperator input tag event init
    $('input[data-seperator]').on("input", onInputNumberSeperator);

    // tooltip init
    initToolTip();

    // set old value filters
    checkQueryForFilters();

    // get data select2 ajax
    const ajaxWrappers = $(".wrapper-ajax.active");
    if (ajaxWrappers.length)
        loadAjaxDataSelect2Filter(ajaxWrappers);

    // show server validation error
    setTimeout(showErrorServer, 500);

    // load meta field by js
    loadMetaDataByJs();

    // make body fit 
    elementHeightFitter($("body"), window.innerHeight, function () { $("#footer-dashboard").css({ "position": "fixed", "bottom": "0" }) });

    // on check action for checkbox on action
    $('input.on-check-action').each(function (index, element) {
        element = $(element);
        const cbk = element.attr("data-callback") ? element.attr("data-callback") : null;
        checkBoxOnAction(element, cbk);
    });

    // select2 condition like popup (1-select(value 1) , 2-input(value 2))
    $(".select-value-to-text").each(function (index, element) {
        element = $(element);
        const inputWrapper = element.parents(".input-wrapper:first()");
        const theTextValueDOM = inputWrapper.find(".the-text-value:first()");

        element.on("input", Select2ValueToTextInput);

        theTextValueDOM.on("input", theTextValueInput);

        element.trigger("input");

        if (theTextValueDOM.val() == "")
            inputWrapper.find("select").val("exact").trigger("change");
    });


    // make option selected by attr data-value Like => settions#time_zone 
    const autoSetValueDOM = $("select.auto-data-value");
    autoSetValueDOM.each(function (index, element) {
        element = $(element);
        const autoSetValueDOMValue = element.attr("data-value");
        element.val(autoSetValueDOMValue).trigger("change");
    });


    // init switch value
    $('.json-switch-value').each(function (index, element) {
        element = $(element);
        jsonSwitchValueInit(element);
    });


    // add css selector for changing value date if localize needed
    const listToLocalizeIndex = [];
    $('thead:first th').each(function (i, element) {
        element = $(element);
        if (element.attr("data-input") == "dateRange") {
            listToLocalizeIndex.push(i);
        }
    });

    $("tbody:first tr").each(function (i, element) {
        element = $(element);
        for (const index of listToLocalizeIndex) {
            const tdOne = element.find("td").eq(index);
            const date = tdOne.text();
            const dateList = date.split(" ");

            const dateText = dateList[0];
            const timeText = dateList[1];

            tdOne.html(null);

            tdOne.append(`<span class="the-date-localize">${dateText}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`);

            tdOne.append(`<span class="the-time-localize">${timeText}</span>`);
        }
    });

    // make element loading when form submit
    $("form:not(.no-lock)").on("submit", function () {
        makeElementLoading($("body"));
    });

    // localize date
    $(".the-date-localize").each(function (i, element) {
        element = $(element);
        if ($("html").attr("lang") == "fa_IR") {
            const date = element.text();
            if (date) {
                const dateList = [
                    date,
                    element.next().text()
                ];

                const theDateOne = dateList[0].split("-").map(function (val, i) { return parseInt(val) });

                const localDate = leadingTimeWithZeroCore(new persianDate(new Date(theDateOne)).toCalendar('persian').toLocale('fa').format("YYYY-M-D"), "-");
                const localTime = leadingTimeWithZeroCore(dateList[1]);

                element.text(localDate);
                element.next().text(localTime)


                element.addClass("fa-number just-num").attr("dir", "ltr");
                element.next().addClass("fa-number just-num").attr("dir", "ltr");

                cbkFaNumber(0, element);
                cbkFaNumber(0, element.next());
            }
        }
    });


    // rtl mode actions
    if ($("html").attr("dir") == "rtl") {

        // align search datatables
        const currentRowSearchDatatables = $("#datatable-list_wrapper .row:nth-child(1)");
        currentRowSearchDatatables.find("div").first().remove();

        // set some inputs like(email) to ltr
        $("input.text-left").attr("dir", "ltr");

        // language fa_IR action
        if ($("html").attr("lang") == "fa_IR") {
        }

    }

    // add applyCloneClickHandler
    $("#apply_clone").on("click", applyCloneClickHandler);


    // form properties
    if (location.href.search('forms_schema/create') != -1 || location.href.search('forms_schema/edit') != -1) {

        // when change input type
        $('.input-wrapper-form-properties #type-input-form').on('input', typeFormInputEventInput);

        // update `JSON INPUT` when `Properties` inputs `inputed`
        $('.input-wrapper-form-properties .input-form-prop-one:not(#type-input-form)').on("input", updateFormInputJsonSchemaInput);

        // clonable actions
        $(document).on('click', '.wrapper-form-input-items .item-input', activeClonableItemForm);

        // trigger click if schema already has value
        const schemaJsomDOM = $('#schema');
        if (schemaJsomDOM.attr('has-value') == 'true') {
            $(".clonable.item-input:first").trigger("click");
        }

        // show required on label inputs if set on input element
        showRequiredSignForInputs($('.input-wrapper-form-properties'));
    }

    /* ================> END INIT */
});