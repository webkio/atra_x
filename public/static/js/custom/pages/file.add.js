$(document).ready(function () {

    /* ================> Functions */

    function dragleaveHandler(e) {
        e.preventDefault()
        const thisElement = $(this);
        thisElement.find('h2').removeClass("over");
    }

    function generateUploadElement(name) {
        const templates = {
            "details": `<li data-name="x-title" class="mb-4"><div id="name">x-title</div><div id="size">x-size MB</div><p id="description" class="bg-danger text-white p-1 ml-2 rounded d-none"></p><input class="just-url d-none form-control" dir="ltr" id="x-url" readonly><div class="text-center"><button class="btn-clipboard btn badge badge-white mx-2 mt-2 d-none" data-clipboard-target="#x-url" type="button"><i class="bi bi-clipboard-check h5"></i></button></div></li>`
        }

        return templates[name];
    }

    function dragoverHandler(e) {
        e.preventDefault()
        const thisElement = $(this);
        thisElement.find('h2').addClass("over");
    }

    function dropHandler(e) {
        e.preventDefault();
        const thisElement = e.selectFileInterface ? $(e.selectFileInterface) : $(this);

        const thisElementParent = thisElement.parent();
        const fileList = thisElementParent.find(".file-list");

        if (thisElement.attr("data-file")) {
            return false;
        }
        thisElement.attr("data-file", "true");

        thisElement.find("h2").addClass("d-none");
        selectFileWrapper.addClass("d-none");
        thisElement.find(".action-wrapper").removeClass("d-none");

        if (e.originalEvent)
            e = e.originalEvent;

        const files = e.dataTransfer.items;
        window.fileObjectList = [];

        const filename_list = [];

        const maxFileList = [];
        const maxUploadSize = Number(maxSizeDOM.attr('data-size'));

        for (var i = 0; i < files.length; i++) {
            let currentFile = files[i];
            let fileObject = null;
            if (currentFile.kind === 'file') {
                fileObject = currentFile.fromObject ? currentFile : currentFile.getAsFile();

                const filename = fileObject.name;
                const size = fileObject.size;

                if (maxUploadSize < size) {
                    maxFileList.push(filename);
                    continue;
                }

                if (!filename_list.includes(filename))
                    filename_list.push(filename);
                else {
                    continue;
                }

                fileObjectList.push(fileObject);
            }
        }


        let k = 1;
        for (const fileObject of fileObjectList) {
            const template = generateUploadElement('details');
            const tempateDynamic = template.replace(/x-title/gi, fileObject.name).replace(/x-url/gi, "url-clp-" + k).replace(/x-size/gi, convertBToMB(fileObject.size).toFixed(3));
            fileList.append(tempateDynamic);
            generateClipboardFeature('.btn-clipboard');
            k++;
        }

        if (0 < maxFileList.length) {
            const options = sweetAlertOptions.info;
            options.title = __local("Warning");
            const names = maxFileList.join(" , ");
            options.text = __local("files x-names ignored ( more than max upload size )").replace("x-names", names);
            Swal.fire(options);
        }

    }

    function btnHandlerUpload(e) {
        const thisElement = $(e.target);

        const token = tokenDOM.val();
        const action = formDOM.attr("action");

        const form = new FormData();
        for (const file of fileObjectList) {
            form.append('the_file[]', file);
        }

        // add token to input
        form.append("_token", token);
        form.append("source", formDOM.attr("data-source"));

        $.ajax({
            type: 'POST',
            url: action,
            data: form,
            processData: false,
            contentType: false,
            success: function (data) {

                const options = sweetAlertOptions.info;

                if (data.jsonServerMessage) {
                    const jsonServerMessage = JSON.parse(data.jsonServerMessage);
                    options.title = __local("ERROR");
                    options.text = jsonServerMessage.message;
                    Swal.fire(options);
                    return false;
                }

                const errorMessage = " " + __local('but some issue happened you can see the issue in file list');
                const reasonMessage = 0 < data.errors.length ? errorMessage : "";

                options.title = __local('Completed');
                options.text = data.progress + reasonMessage;
                Swal.fire(options);

                // show new upload BUTTON
                labelNewUpload = __local('New Upload');
                $('.mask-progress').parent().after(`<div class="d-block text-center mt-4"><a onClick="javascript:location.reload()" class="btn btn-primary">${labelNewUpload}</a></div>`)


                var cs = function (selector) {
                    return $(`[data-name='${selector}']`);
                }


                if (0 < data.errors.length) {
                    for (const element of data.errors) {
                        cs(element.data[0]).find("#description").html(element.message).removeClass("d-none");
                    }
                }

                if (0 < data.success.length) {
                    for (const element of data.success) {
                        cs(element.data[0]).find(".just-url").val(element.url);
                        cs(element.data[0]).find(".just-url,.btn-clipboard").removeClass("d-none");
                    }
                }
            },
            error: function (err) {
                ajaxErrorHandler(err);
            },
            xhr: function (e) {

                var xhr = new XMLHttpRequest();

                var theProgressDOM = $('.mask-progress');
                theProgressDOM.css("left" , 0);

                xhr.upload.addEventListener('progress', function (e) {
                    var loaded = e.loaded;
                    var total = e.total;
                    var progress = (loaded / total * 100).toFixed(0);
                    const theProgress = progress + "%";
               
                    theProgressDOM.text(theProgress).css("width", theProgress);

                    if (progress == 100) {
                        theProgressDOM.text(__local('Completed') + " !");
                    }
                });


                return xhr;

            }
        });


        thisElement.parents(".action-wrapper").addClass("d-none");

    }

    function simpleSelectFileHandler(e) {

        for (const file of e.target.files) {
            file.kind = "file";
            file.fromObject = "selectSimple";
        }

        dropHandler({
            preventDefault: function () { },
            selectFileInterface: dropZoneDOM.get(0),
            dataTransfer: {
                items: e.target.files
            }
        })
    }

    /* ================> END Functions */


    /* ================> EVENTS */

    const dropZoneDOM = $(".file-drop-zone");
    if (dropZoneDOM.length) {
        dropZoneDOM.on("dragover", dragoverHandler);
        dropZoneDOM.on("dragleave", dragleaveHandler);
        dropZoneDOM.on("drop", dropHandler);
    }

    const uploadBtn = $('#upload-files');
    if (uploadBtn.length) {
        uploadBtn.on("click", btnHandlerUpload);
    }

    const simpleSelectFileDOM = $("#select-file");
    simpleSelectFileDOM.on("change", simpleSelectFileHandler);

    /* ================> END EVENTS */

    /* ================> INIT */

    const formDOM = $("#upload-form");
    const tokenDOM = $("input[name=_token]");

    const maxSizeDOM = $("#max-size");

    const selectFileWrapper = $(".select-file-wrapper");


    /* ================> END INIT */
});