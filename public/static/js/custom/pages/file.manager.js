$(document).ready(function () {

    /* ================> DOM & data */

    const alignBlock = $("html").attr("dir") == "rtl" ? "d-block text-right" : "";

    const fileManagerOptions = {
        fileListMode: "normal",
        groupType: "all",
        type: null,
        fListFiles: [],
        dialogReload: false,
        dataMutiple: false,
        dataMutipleMaxItem: 100,
        iconFileUrl: "static/img/folder.png",
        target: null,
        xhrSearch: false,
    }

    window.finalFileManagerOptions = JSON.stringify(fileManagerOptions);
    window.finalFileManagerOptions = JSON.parse(window.finalFileManagerOptions);

    const searchFileListOption = {
        url: "",
        data: {
            group_type: "",
            q: "",
            page: 1
        },
        dataType: "json",
        error: function (e) {
            if (e.statusText == "error")
                alert(__local("Network Error"))
        },
        success: function () { },
        beforeSend: function () { },
        fileListWrapper: null
    }

    /* ================> END DOM */

    /* ================> Functions */

    window.resetObjectElements = function (newObj, oldObj, keys) {
        for (const key of keys) {
            newObj[key] = oldObj[key];
        }

        return newObj;
    }

    window.getTemplateFile = function (name) {

        const labelSizePx = __local('size x-widthpx in x-heightpx');
        const labelFileManager = __local('File Manager');
        const labelSearch = __local('Search');
        const labelAdd = __local('Add');
        const labelCurrentName = __local('Current Name');
        const labelOriginalName = __local('Original Name');

        const template = {
            "fileSize": `<div class="custom-control custom-radio mb-2"> <input type="radio" id="x-index" x-original x-checked data-file="x-base-file" name="file_sizes" class="custom-control-input"> <label class="custom-control-label" for="x-index">${labelSizePx}</label> </div>`,
            "fileElement": `<div class="col-sm-6 col-md-4 col-lg-4 col-xl-4 border border-6 border-light cursor-pointer file-element normal-load" data-original-url="x-originalUrl" data-selected-file="x-originalUrl" data-info='x-json'><div class="file-element-wrapper"> <div class="badge badge-large badge-warning text-dark position-absolute">x-type</div> <img class="w-100" src="x-preview"> </div></div>`,
            "fileElementSearch": `<div class="col-sm-6 col-md-4 col-lg-4 col-xl-4 border border-6 border-light cursor-pointer file-element search-load" data-original-url="x-originalUrl" data-selected-file="x-originalUrl" data-info='x-json'><div class="file-element-wrapper"> <div class="badge badge-large badge-warning text-dark position-absolute">x-type</div> <img class="w-100" src="x-preview"> </div></div>`,
            "fileManagerElement": `<button type="button" class="btn btn-primary mb-3 openFileManager d-none" data-toggle="modal" data-target=".file-manager-modal">click</button> <div class="modal fade file-manager-modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true"> <div class="modal-dialog modal-xl"> <div class="modal-content"> <div class="modal-header" dir="ltr"> <h5 class="modal-title d-inline mt-0 ml-2 mr-2" id="myExtraLargeModalLabel">${labelFileManager}</h5> <span id="file_group_type" class="badge badge-info badge-large ml-2"></span><span id="file_type" class="badge badge-warning badge-large ml-2"></span> <span id="file_count" class="badge badge-primary badge-large ml-2"></span> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button> </div> <div class="modal-body overflow-hidden"> <div class="row"> <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8"> <div class="form-inline mb-3"> <label class="ml-2 mr-2" for="fileSearch">${labelSearch}</label> <input type="text" class="form-control" id="fileSearch" data-target=".file-wrapper"> </div> <div class="row-wrapper overflow-x-hidden overflow-auto scroll-auto" data-minus="177"> <div class="row file-wrapper"></div> </div> </div> <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 fileDetails"> <div class="row"> <div class="col-12 input-wrapper-edit mb-3"> <label for="file_name" class="${alignBlock}">${labelOriginalName}</label> <input type="text" class="form-control" name="file_name" id="file_name" value="" readonly> </div> <div class="col-12 input-wrapper-edit mb-3"> <label for="file_name_current" class="${alignBlock}">${labelCurrentName}</label> <input type="text" class="form-control" name="file_name_current" id="file_name_current" value="" readonly> </div> <div class="col-12 input-wrapper-edit file-size-wrapper"> </div> </div> <div class="wrapper text-right mt-5"> <button type="button" class="btn btn-success" id="submitFiles" disabled>${labelAdd}</button> </div> </div> </div> </div> </div> </div> </div>`
        }

        return template[name];
    }

    if (typeof window.autoScrollElement === "undefined")
        window.autoScrollElement = function (element) {
            const windowHeight = window.innerHeight;
            const minus = element.attr("data-minus") ? Number(element.attr("data-minus")) : 0;
            const height = windowHeight - minus;
            element.css("height", height);
            element.addClass("fixed");


            element.on("scroll", function (e) {

                const thisElement = $(this);

                const maxScrollY = Math.ceil(thisElement.get(0).scrollHeight - thisElement.get(0).clientHeight);
                const currentScrollY = Math.ceil(thisElement.get(0).scrollTop);

                if (maxScrollY <= currentScrollY) {
                    thisElement.find(".file-wrapper").trigger("scrollYEnd");
                }
            })
        }

    window.getLabelByKey = function (key) {
        const labels = {
            "all": __local("All"),
            "image": __local("Image"),
            "archive": __local("Archive"),
            "video": __local("Video"),
        }

        return labels[key] ? labels[key] : "undefined";
    }

    window.getFileDataElements = function (fileWrapper) {
        window.finalFileManagerOptions['fListFiles'] = [];
        fileWrapper.find(".file-element.active:not(.d-none)").each(function (index, element) {
            element = $(element)
            window.finalFileManagerOptions['fListFiles'].push(element.attr("data-selected-file"));
        });

        if (!window.finalFileManagerOptions['fListFiles'].length) {
            $("#submitFiles").attr("disabled", "disabled");
        } else {
            $("#submitFiles").removeAttr("disabled");
        }

        makeFileCountGetNumber();

    }

    window.getFileInfo = function (e) {
        const thisElement = $(this);
        const thisElementParent = $(this).parents('.file-wrapper');
        let currentActive = false;

        if (thisElement.hasClass("active")) {
            thisElement.removeClass("active")
            currentActive = true;
        }

        const dataFile = JSON.parse(thisElement.attr("data-info"));
        const selectedFileAttr = thisElement.attr("data-selected-file");

        const fileDetailsDOM = $(".fileDetails");
        const fileName = fileDetailsDOM.find("#file_name");
        const fileNameCurrent = fileDetailsDOM.find("#file_name_current");
        const fileWrapperForm = fileDetailsDOM.find(".file-size-wrapper");

        const fileElementActive = thisElementParent.find(".file-element.active");

        if (!currentActive) {
            if (e.ctrlKey && window.finalFileManagerOptions['dataMutiple'] === true) {
                if (fileElementActive.length < window.finalFileManagerOptions['dataMutipleMaxItem']) {
                    thisElement.addClass("active");
                } else {
                    alert(`Max ${window.finalFileManagerOptions['dataMutipleMaxItem']} Item`);
                    return false;
                }
            } else {
                fileElementActive.removeClass("active");
                thisElement.addClass("active");
            }
        }

        getFileDataElements(thisElementParent);

        fileName.html(null);
        fileNameCurrent.html(null);
        fileWrapperForm.html(null);

        fileName.val(dataFile['name']);
        fileNameCurrent.val(dataFile['current_name']);

        if (dataFile['sizes']) {
            let counter = 0;
            const endCounter = (dataFile['sizes'].length) - 1;

            const fileUrlList = generateImageSizeByArraySize(dataFile);

            for (const size of dataFile['sizes']) {
                let finallFileUrl = fileUrlList[counter];
                const width = size[0];
                const height = size[1];
                let original = "";
                let checked = "";
                if (endCounter == counter) {
                    original = "data-original=\"yes\"";
                    finallFileUrl = dataFile['file_url']
                }

                if (selectedFileAttr == finallFileUrl) {
                    checked = "checked";
                }

                let template = getTemplateFile("fileSize");
                if (!template) continue;

                template = template.replace(/x-index/gi, `fileSize_${counter + 1}`).replace(/x-base-file/gi, `${finallFileUrl}`).replace(/x-width/gi, `${width}`).replace(/x-height/gi, `${height}`).replace(/x-original/gi, `${original}`).replace(/x-checked/gi, `${checked}`);

                fileWrapperForm.append(template);
                counter++;

            }


        }
    }

    window.generateImageSizeByArraySize = function (jsonData) {
        const fileUrlList = [];
        const nameLabel = 'current_name';
        if (jsonData['sizes']) {
            for (const size of jsonData['sizes']) {

                const width = size[0];
                const height = size[1];

                const stuffix = `-${width}x-${height}y`;
                const fileListDetails = jsonData[nameLabel].split(".");
                let finallName = "";

                const deleted = fileListDetails.splice(fileListDetails.length - 1);
                fileListDetails.push(stuffix);
                fileListDetails.push(deleted)

                finallName = fileListDetails.join(".");

                let finallFileUrl = jsonData['file_url'].replace(jsonData[nameLabel], finallName);
                finallFileUrl = finallFileUrl.replace(`.${stuffix}`, stuffix);

                if (jsonData['sizes'].length === 1) {
                    finallFileUrl = jsonData['file_url'];
                }

                fileUrlList.push(finallFileUrl);
            }
        }

        return fileUrlList;
    }

    window.fileSizeRadioChange = function () {
        const thisElement = $(this);
        const thisElementParent = thisElement.parents(".file-size-wrapper");
        const originalRadio = thisElementParent.find("[data-original=yes]");
        const originalDataFile = originalRadio.attr("data-file");
        const dataFile = thisElement.attr("data-file");

        const target = $(`[data-original-url='${originalDataFile}']`);

        if (!target.length) return false;

        target.attr("data-selected-file", dataFile);
        getFileDataElements(target.parents(".file-wrapper"));
    }

    window.openFileManager = function (options) {

        const btnTrigger = $(".openFileManager");
        btnTrigger.eq(0).trigger("click");

        const inputSearch = $("#fileSearch");

        searchFileListOption["url"] = options['url'] ? options['url'] : "/check/data/get/getfilerest";

        let defaultOptionFileManager = JSON.stringify(fileManagerOptions);
        defaultOptionFileManager = JSON.parse(defaultOptionFileManager);

        if (window.finalFileManagerOptions['target'] && window.finalFileManagerOptions['target'] != options['target']) {
            inputSearch.val(null);
        }

        window.finalFileManagerOptions['target'] = options['target'] ? options['target'] : defaultOptionFileManager['target'];
        window.finalFileManagerOptions['dataMutiple'] = options['multiple'] ? options['multiple'] : defaultOptionFileManager['multiple'];
        window.finalFileManagerOptions['dialogReload'] = options['dialogReload'] ? options['dialogReload'] : defaultOptionFileManager['dialogReload'];
        window.finalFileManagerOptions['groupType'] = options['groupType'] ? options['groupType'] : defaultOptionFileManager['groupType'];
        window.finalFileManagerOptions['type'] = options['type'] ? options['type'] : defaultOptionFileManager['type'];
        window.finalFileManagerOptions['iconFileUrl'] = options['iconFileUrl'] ? options['iconFileUrl'] : defaultOptionFileManager['iconFileUrl'];
        if (window.finalFileManagerOptions['dataMutiple']) {
            window.finalFileManagerOptions['dataMutipleMaxItem'] = options['dataMutipleMaxItem'] ? options['dataMutipleMaxItem'] : defaultOptionFileManager['dataMutipleMaxItem'];;
        }

        const label = getLabelByKey(window.finalFileManagerOptions['groupType']);

        $("#file_group_type").text(label);

        if (window.finalFileManagerOptions['type'])
            $("#file_type").text(window.finalFileManagerOptions['type']);

        const fileListWrapper = $(".file-wrapper");
        const modalFileManager = $(".file-manager-modal");

        modalFileManager.attr("data-input", options['target'])

        // append file manager modal
        const condition2 = !fileListWrapper.hasClass("loaded") || window.finalFileManagerOptions['groupType'] != fileListWrapper.attr("data-group-type")
        const condition1 = window.finalFileManagerOptions['dialogReload'];
        if (condition1 || condition2) {
            // reset
            window.finalFileManagerOptions = resetObjectElements(window.finalFileManagerOptions, fileManagerOptions, ['fileListMode', 'fListFiles', 'xhrSearch'])
            resetHtmlAndRemoveAttr(fileListWrapper, ["data-group-type", "data-next-page", "data-search-next-page"]);

            modalFileManager.find("#submitFiles").attr("disabled", "true");
            // load basic data
            let ajaxOption = JSON.stringify(searchFileListOption);
            ajaxOption = JSON.parse(ajaxOption);
            ajaxOption["data"]["group_type"] = window.finalFileManagerOptions['groupType'];

            if (window.finalFileManagerOptions['type']) {
                ajaxOption["data"]["type"] = window.finalFileManagerOptions['type'];
            }
            ajaxOption["data"]["q"] = inputSearch.val();
            ajaxOption["success"] = dynamicAjaxHtmlTemplateFileRow;
            ajaxOption["beforeSend"] = makeFileCountLoading;
            ajaxOption["fileListWrapper"] = fileListWrapper;


            searchFileList(ajaxOption);
        }

        $(document).trigger({
            type: "openFileManager"
        });
    }

    window.closeFileManager = function (e) {
        let input = null;
        if (e.submitPopUp === true) {
            const modalFileManager = $(this).parents(".file-manager-modal");
            input = $(modalFileManager.attr("data-input")).first();
            const urlList = window.finalFileManagerOptions['fListFiles'].join(",");
            
            input.val(urlList).attr("value", urlList);
            input.trigger("importFileURL");
        }

        let attrHidden = "";
        if (input) {
            attrHidden = $(input.attr("data-button-opener"))
        }

        const finallDomEventer = attrHidden ? attrHidden : $(document);
        finallDomEventer.trigger({
            type: "closeFileManager",
        });
    }

    window.makeFileCountLoading = function () {
        $(".file-manager-modal #file_count").text(__local("Loading"));
    }

    window.makeFileCountGetNumber = function () {
        $(".file-manager-modal #file_count").text($(".file-element:not(.d-none)").length + " " + __local('File/s'));
    }

    window.dynamicAjaxHtmlTemplateFileRow = function (data) {
        data = data.data;
        const elementLabel = 'element';

        let theK = 0;
        const sizeLabel = 'sizes';

        for (const elementOne of data[elementLabel]) {

            if (elementOne[sizeLabel]) {
                data[elementLabel][theK][sizeLabel] = JSON.parse(elementOne[sizeLabel]);
            }
            theK++;
        }

        const searchRow = window.finalFileManagerOptions['fileListMode'] === "search" ? true : false;
        const fileListWrapper = this.fileListWrapper ? this.fileListWrapper : data.fileListWrapper;
        let previewUrl = "";

        const templateKey = searchRow ? "fileElementSearch" : "fileElement";
        for (const fileElement of data[elementLabel]) {
            const fileID = fileElement['id'];
            const currentFileElementByID = fileListWrapper.find(`[data-file-id=${fileID}]`);
            if (currentFileElementByID.length) {
                if (searchRow && currentFileElementByID.length === 1 && currentFileElementByID.hasClass("normal-load")) {
                    const clone = currentFileElementByID.clone();
                    clone.removeClass("normal-load");
                    clone.removeClass("d-none");
                    clone.addClass("search-load");
                    fileListWrapper.append(clone)
                }
                continue;
            }


            let templateFileElement = getTemplateFile(templateKey);
            const sizesUrl = generateImageSizeByArraySize(fileElement);

            previewUrl = window.finalFileManagerOptions.iconFileUrl;

            if (fileElement['group_type'] == "image" && sizesUrl[0]) {
                previewUrl = sizesUrl[0];
            } else if (fileElement['group_type'] == "image" && !sizesUrl[0]) {
                previewUrl = fileElement['file_url'];
            }



            templateFileElement = templateFileElement.replace(/x-originalUrl/gi, fileElement.file_url).replace(/x-json/gi, JSON.stringify(fileElement)).replace(/x-type/gi, fileElement.type).replace(/x-preview/gi, previewUrl);
            templateFileElement = $(templateFileElement);
            templateFileElement.attr("data-file-id", fileID);
            fileListWrapper.append(templateFileElement)
        }

        fileListWrapper.addClass("loaded");
        fileListWrapper.attr("data-group-type", window.finalFileManagerOptions['groupType']);

        let nextPage = 0;
        if (data['current_page'] < data['pages'])
            nextPage = data['current_page'] + 1;
        else {
            nextPage = `-1:${data['current_page']}`;
        }

        const attrPage = searchRow ? "data-search-next-page" : "data-next-page";

        this.fileListWrapper.attr(attrPage, nextPage);
        makeFileCountGetNumber();
    }

    window.searchFileList = function (settings) {

        // if (window.finalFileManagerOptions['xhrSearch'] !== false) {
        //     window.finalFileManagerOptions['xhrSearch'].abort();
        // }

        // laravel csrf
        settings.data['_token'] = getCsrf();
        window.finalFileManagerOptions['xhrSearch'] = $.post(settings);
    }

    window.getFileLoadPage = function (element) {
        const dataKey = window.finalFileManagerOptions['fileListMode'] === "search" ? "data-search-next-page" : "data-next-page";

        let page = element.attr(dataKey) ? element.attr(dataKey) : String(1);
        const pageList = page.split(":");

        if (1 < pageList.length) {
            page = pageList[pageList.length - 1];
        }

        return page;
    }

    window.scrollYEndFileWrapperHandler = function (e) {

        const thisElement = $(e.target);
        const page = getFileLoadPage(thisElement);

        const query = $("#fileSearch").val().trim();

        

        if (0 < page) {
            let ajaxOption = JSON.stringify(searchFileListOption);
            ajaxOption = JSON.parse(ajaxOption);
            ajaxOption["data"]["group_type"] = window.finalFileManagerOptions['groupType'];

            if (window.finalFileManagerOptions['type']) {
                ajaxOption["data"]["type"] = window.finalFileManagerOptions['type'];
            }

            ajaxOption["data"]["q"] = query;
            ajaxOption["data"]["page"] = page;
            ajaxOption["success"] = dynamicAjaxHtmlTemplateFileRow;
            ajaxOption["beforeSend"] = makeFileCountLoading;
            ajaxOption["fileListWrapper"] = thisElement;
            searchFileList(ajaxOption);
        }

    }

    window.balanceDisplyDOM = function (option) {
        option.Parent.find(option.showElement).removeClass("d-none");
        option.Parent.find(option.hideElement).addClass("d-none");
    }

    window.resetHtmlAndRemoveAttr = function (element, attrs) {
        element.html(null);
        for (const attr of attrs) {
            element.removeAttr(attr);
        }
    }

    window.searchFileByInput = function () {
        const thisElement = $(this);
        const target = $(thisElement.attr('data-target'));
        const value = thisElement.val().trim();

        const normalLoadElementSelector = ".file-element.normal-load";
        const searchLoadElementSelector = ".file-element.search-load";

        target.find(".file-element.active").removeClass("active");

        if (value == "") {
            window.finalFileManagerOptions['fileListMode'] = "normal";

            balanceDisplyDOM({
                Parent: target,
                showElement: normalLoadElementSelector,
                hideElement: searchLoadElementSelector
            });

            target.find(searchLoadElementSelector).remove();

            getFileDataElements($(".file-wrapper"));
            makeFileCountGetNumber();
            if (window.finalFileManagerOptions['dialogReload']) {
                resetHtmlAndRemoveAttr(target, ["data-group-type", "data-next-page", "data-search-next-page"]);
                target.trigger({
                    type: "scrollYEnd",
                });
            }
            return false;
        }

        window.finalFileManagerOptions['fileListMode'] = "search";

        target.find(searchLoadElementSelector).remove();
        target.removeAttr("data-search-next-page")

        balanceDisplyDOM({
            Parent: target,
            showElement: searchLoadElementSelector,
            hideElement: normalLoadElementSelector
        });
        getFileDataElements($(".file-wrapper"));

        target.trigger({
            type: "scrollYEnd",
        });

    }

    window.submitFilesClick = function () {
        if (!(window.finalFileManagerOptions['fListFiles'] && window.finalFileManagerOptions['fListFiles'].length)) {
            alert(__local("Nothing Selected !"))
        } else {
            $(".file-manager-modal .close").trigger({
                type: "click",
                submitPopUp: true
            });
        }
    }

    /* ================> END Functions */


    /* ================> EVENTS */

    $(document).on("click", ".file-element", getFileInfo);
    $(document).on("change", "[name=file_sizes]", fileSizeRadioChange);

    $(document).on("click", ".file-manager-modal .close", closeFileManager);

    $(document).on("scrollYEnd", ".file-wrapper", scrollYEndFileWrapperHandler);

    $(document).on("keyup", "#fileSearch", searchFileByInput);

    $(document).on("click", "#submitFiles", submitFilesClick);

    $(document).on("openFileManager", function (e) { console.log("open file manager"); });

    $(document).on("closeFileManager", function (e) { console.log("close file manager"); });



    /* ================> END EVENTS */

    /* ================> INIT */


    // init filemanger html
    if (!$(".file-manager-modal").length) {
        const fileMangerTemplate = getTemplateFile("fileManagerElement");
        $("body").append(fileMangerTemplate);
    }

    // auto scroll
    $(".scroll-auto:not(.fixed)").each(function (index, element) {
        element = $(element);
        autoScrollElement(element);
    });


    /* ================> END INIT */
});