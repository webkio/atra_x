$(document).ready(function () {
    /* ================> property */

    window['canSubmitUserForm'] = false;

    const acceptDOM = $("#accept");
    const submitDOM = $("#submit-form");
    const mainFormDOM = $('#main-form');

    /* ================> END property */


    /* ================> Functions */

    window.checkUserAjax = function (data, success, error = null, url = null) {
        let mAjaxOptions = instanceJson(ajaxOptions);
        error = !error ? function (err) { ajaxErrorHandler(err); window['postRequestXhr'] = false; } : error;
        url = !url ? "/check/user/sign_up" : url;
        mAjaxOptions['url'] = url;
        const formData = {
            "_token": getCsrf()
        }

        for (const key of Object.keys(data)) {
            const itemValue = data[key];
            formData[key] = itemValue;
        }

        mAjaxOptions['data'] = formData;

        mAjaxOptions['success'] = function (data) {
            window['postRequestXhr'] = false;

            success(data);
        }

        mAjaxOptions['error'] = error;

        postRequest(mAjaxOptions);
    }

    window.enableElementByCheckClass = function (isChecked, element, trueFunc, falseFunc) {

        if (isChecked && element.length === 0) {
            trueFunc();
        } else {
            falseFunc();
        }
    }

    window.userPanelCheckTerm = function (e) {
        const thisElement = $(e.target);

        if (!thisElement.length) return false;

        const isChecked = thisElement.prop("checked");
        const target = $(thisElement.attr("data-field"));
        const formParent = thisElement.parents("form");

        if (!target.length) {
            alert("target not found");
            return false;
        } else if (!formParent.length) {
            alert("form parent not found");
            return false;
        }

        enableElementByCheckClass(isChecked, formParent.find(".error-message.d-block"), () => target.removeAttr("disabled"), () => target.attr("disabled", "true"));

    }

    window.blurCheckUser = function (e) {
        const thisElement = $(e.target);
        const thisElementNextSmallElement = thisElement.next();
        const query = thisElement.val();

        thisElementNextSmallElement.removeClass("d-block").addClass("d-none");

        if (query == "") return;

        const payload = {};
        payload[thisElement.attr("name")] = query;

        checkUserAjax(payload, function (data) {
            if (data.isExists) {
                thisElementNextSmallElement.removeClass("d-none").addClass("d-block").text(__local("x-name Already taken").replace("x-name" , thisElement.val()));
            }

            userPanelCheckTerm({
                target: acceptDOM
            })
        });

    }

    window.keyupCheckPinCode = function (e) {
        const thisElement = $(e.target);
        const idDOM = $("input#id");

        if (submitDOM.attr("type") === "submit") {
            submitDOM.attr("type", "button");
        }

        const id = idDOM.val();
        const code = thisElement.val();

        if (code == "") return;

        if (4 < code.length) {
            submitDOM.removeAttr("disabled")
        } else {
            submitDOM.attr("disabled", "true");
        }

        submitDOM.off("click");
        submitDOM.on("click", function () {
            checkUserAjax({
                "client_code": code,
                "id": id,
            }, function (data) {
                const options = sweetAlertOptions.info;
                if (data.isExists) {
                    const seconds = 3 * 1000;
                    setTimeout(() => {
                        const URL = mainFormDOM.attr('data-success-redirect');
                        location.assign(URL);
                    }, seconds)

                    options.title = __local("Successful");
                    options.text = __local(`You Will Redirect To Sign-in Page to login in x-seconds second/s`).replace("x-seconds" , (seconds / 1000));

                } else {
                    options.title = __local("Invalid Code");
                    options.text = __local("Code MisMatch Enter Valid Code");
                }
                Swal.fire(options);
            }, null, "/check/otp/verify_email");
        });

    }

    window.checkUserResetCredentials = function (e) {
        const thisElement = $(e.target);
        const value = thisElement.val();

        if(value == "") return;
        
        const payload = {};
        payload["username"] = value;
        payload["email"] = value;
        submitDOM.removeAttr("disabled");
    }


    window.trueCallbackEmptyString = function (target , form) {
        submitDOM.removeAttr("disabled");
    }

    window.falseCallbackEmptyString = function (target , form) {
        submitDOM.attr("disabled" , "true");
    }

    /* ================> END Functions */


    /* ================> EVENTS */



    /* ================> END EVENTS */

    /* ================> INIT */

    userPanelCheckTerm({
        target: acceptDOM
    })

    // add home page link
	$(".user-panel-wrapper").prepend(`<a class="btn btn-success mb-2" href="/">${__local('Go to Home page')}</a>`)

    /* ================> END INIT */
});