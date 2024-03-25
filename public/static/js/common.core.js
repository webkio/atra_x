$(document).ready(function () {

    /* ================> Functions */

    // #### Translate

    window.__local = function (label) {
        let result_label = label;

        if (typeof i18n_system != "undefined" && typeof i18n_system[label] != "undefined") {
            result_label = i18n_system[label];
        }

        return result_label;
    }


    // #### End Translate

    // #### AJAX
    window.ajaxErrorHandler = function (error) {
        const options = { ...sweetAlertOptions.info };
        let disableSwal = false;
        if (error.statusText != "abort") {
            if (error.statusText == "error") {
                options.title = __local("Network Error");
                options.text = __local("Please Check Your Network Connection");

            } else if (error.statusText == "Internal Server Error") {
                options.title = __local("Server Error");
                options.text = __local("Some Problem Happened From Server Try out 1 minute later");
            } else {
                disableSwal = true;
            }
            if (!disableSwal) {
                Swal.fire(options);
            }
        }
    }

    window.getRequest = function (options) {
        if (typeof window['getRequestXhr'] != "undefined" && window['getRequestXhr'] !== false) {
            window['getRequestXhr'].abort();
            return false;
        }

        window['getRequestXhr'] = $.get(options);
    }

    window.postRequest = function (options) {
        if (typeof window['postRequestXhr'] != "undefined" && window['postRequestXhr'] !== false) {
            window['postRequestXhr'].abort();
            return false;
        }

        window['postRequestXhr'] = $.post(options);
    }

    // #### END AJAX

    // #### Date

    window.getDateByTimeZone = function (date, timezone = null) {
        timezone = timezone ? timezone : "Asia/Tehran";
        return (new Date(typeof date === "string" ? date : date.toString())).toLocaleString("en-US", {
            timeZone: timezone
        });
    }

    window.getFormatList = function () {
        const list = {
            "Y": "year",
            "m": "month",
            "d": "day",
            "H": "hours",
            "i": "minutes",
            "s": "seconds",
            "x": "miliSeconds",
            "u": "timestamp",
        }

        return list;
    }

    window.baseConvertDateFormat = function (dateObject, strFormat = "Y-m-d H:i:s") {
        let theFormat = strFormat;
        const formatList = getFormatList();
        for (const formatKey of Object.keys(formatList)) {
            const currentItem = formatList[formatKey];
            const regex = new RegExp(formatKey, "gi");

            if (dateObject[currentItem])
                theFormat = theFormat.replace(regex, dateObject[currentItem]);
        }

        return theFormat;
    }

    window.dateToFa = function (str_date, format = "") {

        const date = new Date(str_date);

        let the_date = {
            "year": parseInt(getTheDate(date, { "year": "numeric" }).convertDigits("en")),
            "month": parseInt(getTheDate(date, { "month": "numeric" }).convertDigits("en")),
            "day": parseInt(getTheDate(date, { "day": "2-digit" }).convertDigits("en")),
            "hours": date.getHours(),
            "minutes": date.getMinutes(),
            "seconds": date.getSeconds(),
            "miliSeconds": date.getMilliseconds(),
            "timestamp": date.getTime() / 1000
        }


        function getTheDate(date, option) {
            const unix = date.getTime();
            let the_date = new Intl.DateTimeFormat('fa-IR', option).format(unix);
            return the_date;
        }

        if (format) {
            the_date = baseConvertDateFormat(the_date, format);
        }

        return the_date;
    }

    window.dateLocalizeElement = function (element) {
        const date = element.attr("data-text") ? element.attr("data-text") : element.text();
        const finalDate = dateToFa(date, "Y-m-d").convertDigits("fa");

        element.text(finalDate);
    }

    window.dateGetDiff = function (date1, date2) {

        if (typeof date1 == "string" || typeof date1 == "number") date1 = new Date(date1);
        if (typeof date2 == "string" || typeof date2 == "number") date2 = new Date(date2);

        const timestamp = (date1.getTime() - date2.getTime()) / 1000;
        const days = Math.floor(timestamp / 86400);

        return days;
    }

    // #### END Date

    // ####  Number to Local

    window.convertToFaDigi = function (str, to = "fa-IR") {
        const value = seperateNumber(str, false, to);
        return value['seperatedValue'];
    }

    window.seperateNumber = function (val, preserveZero = false, to = "en-US") {
        if (val.search(/[a-zA-Z]/gi) != -1)
            return val;

        let value = val.toString().replace(/,/gi, "").replace(/[^0-9\.]/gi, "");

        if (isNaN(Number(value))) return false;

        let seperatedValue = new Intl.NumberFormat(to, { style: "decimal" }).format(value);


        if (seperatedValue == 0 && !preserveZero) seperatedValue = '';

        return {
            seperatedValue: seperatedValue,
            value: value
        };
    }

    window.cbkFaNumber = function (i, element) {
        element = $(element);
        const currentText = element.text();

        if (!element.text()) return;

        const number = element.hasClass("just-num") ? currentText.toString().convertDigits("fa") : convertToFaDigi(currentText);
        element.text(number);
        element.attr("data-text", String(currentText).convertDigits("en"));
    }

    String.prototype.getBaseConversionNumber = function (label) {
        const faDigits = ['۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۰'];
        const enDigits = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
        const arDigits = ['٠', '٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١']

        var whichDigit = {};

        switch (label) {
            case 'fa':
                whichDigit[label] = faDigits;
                break;
            case 'en':
                whichDigit[label] = enDigits;
                break;
            case 'ar':
                whichDigit[label] = arDigits;
                break;
            case 'all':
                whichDigit = { "fa": faDigits, "en": enDigits, "ar": arDigits };
                break;
            default:
                whichDigit = [];
        }

        return whichDigit;
    }


    String.prototype.CvnFromTo = function (fromDigits, toDigits, str) {
        var str = str == undefined ? this : str;
        for (var i = 0; i < toDigits.length; i++) {
            const currentFromDigit = fromDigits[i];
            const currentToDigit = toDigits[i];
            const regex = new RegExp(currentFromDigit, 'g');
            str = str.replace(regex, currentToDigit);
        }
        return str;
    }

    String.prototype.convertDigits = function (to) {
        let str = this;
        const toCvn = (this.getBaseConversionNumber(to))[to];
        const allDigits = this.getBaseConversionNumber("all");

        delete allDigits[to];

        const Objkeys = Object.keys(allDigits);
        for (var i = 0; i < Objkeys.length; i++) {
            const currentKey = Objkeys[i];
            const fromCvn = allDigits[currentKey];
            str = this.CvnFromTo(fromCvn, toCvn, str)
        }
        return str;
    }

    // #### END Number to Local

    window.showErrorServer = function (overloadedData = null) {
        if (overloadedData) {
            window.jsonServerMessage = overloadedData;
        }

        if (typeof jsonServerMessage === "undefined") return false;

        if (typeof jsonServerMessage === "string") {
            jsonServerMessage = {
                "message": jsonServerMessage,
                "data": [],
            }
        }

        const message = jsonServerMessage['message'];
        const data = jsonServerMessage['data'] ? Object.values(jsonServerMessage['data']) : [];

        const labels = [];

        for (const d of data) {
            let theDOM = $(`#${d}[data-label]`);

            if (!theDOM.length) theDOM = $(`[data-group-id='${d}']`);

            if (theDOM.length && theDOM.get(0).clientHeight != 0) {
                const pureTheDOM = theDOM.get(0);
                pureTheDOM.scrollIntoView(true);
            }

            const label = theDOM.attr('data-label');
            labels.push(label)
        }

        let text = message.replace(/x-field/gi, labels.join(" , "));

        const substrLength = text.search(emptyStringSign());
        if (substrLength != -1)
            text = text.substr(0, substrLength);

        if (typeof swal == "undefined") {
            alert(text)
        } else {
            const options = { ...sweetAlertOptions.info };
            options.title = __local("Server Message");
            options.html = text;
            Swal.fire(options);
        }
    }

    window.parseUrlParametersToRealUrl = function(urlSchema , urlParamerts , wrapperTemplate = ['{' , "}"]){
        let result = urlSchema;

        for (const currentUrlKey of Object.keys(urlParamerts)) {
            const currentUrlValue = urlParamerts[currentUrlKey];

            result = result.replaceAll(`${wrapperTemplate[0]}${currentUrlKey}${wrapperTemplate[1]}` , currentUrlValue);
        }

        return result;
    }

    window.makeQueryByObject = function (obj) {
        var i = 0;
        const keys = Object.keys(obj);
        let query = "";

        if (keys.length == 0) return query;

        for (const key of keys) {
            let value = obj[key];

            if (typeof value == "object") value = JSON.stringify(value);

            let prefix = i == 0 ? "?" : "";

            if (keys.length != 1 && !prefix) {
                prefix = "&";
            }

            query += prefix + key + "=" + value;

            i++;
        }

        return query;
    }

    window.getCurrentQuery = function (link = null) {
        link = link ?? document.location.href;

        const params = new URL(link).searchParams;
        return params;
    }

    window.getExactQuery = function (queryName, link = null) {
        const queryList = Array.from(getCurrentQuery(link));
        for (const query of queryList) {
            if (query[0].toLowerCase() == queryName.toLowerCase()) {
                return query[1];
            }
        }

        return undefined;
    }

    window.getAllQuery = function (link = null) {
        link = link ?? document.location.href

        const queryList = Array.from(getCurrentQuery(link));
        const queryListObj = {};
        for (const query of queryList) {
            queryListObj[query[0]] = query[1];
        }

        return queryListObj;
    }

    window.getRootUrl = function () {
        const rootUrl = location.origin + location.pathname;

        return rootUrl;
    }

    window.addQueryToUrl = function (url, query_list) {
        let theUrl = url + "?";
        let counter = 0;
        for (const queryKey of Object.keys(query_list)) {
            const queryValue = query_list[queryKey];
            const prefix = counter === 0 ? "" : "&";
            theUrl += `${prefix}${queryKey}=${queryValue}`

            counter++;
        }

        return theUrl;
    }

    window.addFloatPoint = function (element , stuffix = ".0") {
        let value = String(element.val());

        if (!value.length) value = "0";

        if (value.search("\\.") == -1) {
            element.val(value + stuffix);
        }
    }

    window.runFragmentCommand = function () {
        const queryList = getAllQuery();
        for (const queryKey of Object.keys(queryList)) {
            const queryValue = queryList[queryKey];
            const cbk = window[queryKey];
            if (typeof cbk != "undefined" && queryKey.search("cfrag_") == 0) {
                cbk(queryKey, queryValue);
            }
        }
    }

    window.focusElementByUrl = function (focusElement) {
        const theElement = $("#" + focusElement);
        if (!theElement.length) return false;

        setTimeout(function () {
            theElement.css({
                "border": "10px solid red",
            });
            setTimeout(function () {
                theElement.css({
                    "border": "",
                });
            }, 500);
            theElement.get(0).scrollIntoView(true)
        }, 1000)
    }

    window.getCsrf = function (selector = "[name=_token]") {
        return $(selector).val() ? $(selector).val() : $(selector).attr("content");
    }

    window.instanceJson = function (theJson) {
        const tmpJson = { ...theJson };
        return tmpJson;
    }

    window.onCaptchaReloadBtnClick = function (event) {
        const currentElement = $(event.target);

        const imageDOM = currentElement.parent().parent().find(".captcha-image");

        const mAjaxOptions = instanceJson(ajaxOptions);
        mAjaxOptions['url'] = "/check/data/get/generateCaptcha";

        const formData = {
            "_token": getCsrf()
        }
        mAjaxOptions['data'] = formData;

        mAjaxOptions['success'] = function (res) {
            imageDOM.attr("src", res.data);
        }

        mAjaxOptions['error'] = function (e) {
            ajaxErrorHandler(e);
        }

        window['postRequestXhr'] = false;
        postRequest(mAjaxOptions);
    }

    window.getDataOptionPlugin = function (element, options) {
        const cbk = element.attr("data-cbk-options");
        if (cbk) {
            window[cbk](element);
        }

        const dataOptions = element.attr('data-options') ? JSON.parse(element.attr('data-options')) : {};
        const mOptions = Object.assign({}, options);

        for (const key of Object.keys(dataOptions)) {
            const currentElement = dataOptions[key];
            mOptions[key] = currentElement;
        }

        return mOptions;
    }

    window.addRateByTotal = function (total, cbk, divide = 100) {
        const rate = total / divide;
        const timeRate = 1;
        let sum = 0;

        const idInterval = setInterval(function () {
            cbk(rate);
            sum += rate;
            if (!(sum < total)) {
                clearInterval(idInterval);
            }
        }, timeRate)
    }

    // get number from format ex: 15.66px → 16
    window.removePx = function (str) {
        return str.replace("px", "")
    }

    window.pxToNumber = function (str, round = true) {
        const number = removePx(str);
        return round ? Math.round(number) : parseFloat(number);
    }

    window.getMaxScrollX = function (element) {
        const maxX = element.get(0).scrollWidth - pxToNumber(element.css("width"), false);
        return maxX == 0 ? maxX : Math.ceil(maxX);
    }

    window.getMaxScrollYWindow = function () {
        return $("body").height() - window.innerHeight;
    }

    window.calcToPercent = function (part, total, percentTotal = 100) {
        const divide = divideNumbersBigJs([part, total]);
        const result = multiplyNumbersBigJs([divide, percentTotal]).roundNumberByPrecision(2);

        return result;
    }

    window.percentToNumber = function (number, percent) {
        let result = calcToPercent(number, 100, percent);
        return result;
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

    window.emptyStringSign = function () {
        return "####";
    }

    window.capitalizeWord = function (str) {
        const result = str.charAt(0).toUpperCase() + str.slice(1);

        return result;
    }

    window.callColumnCallback = function (e = null) {
        const callbackList = window["columnCallbackList"][currentBootstrapColumn];

        if (!callbackList) return;

        for (const theCallback of callbackList) {
            window[theCallback](e, currentBootstrapColumn);
        }
    }

    window.pushToGlobal = function (globalScope, currentScope) {
        for (const theItem in window[globalScope]) {
            for (const item of window[currentScope][theItem]) {
                window[globalScope][theItem].push(item);
            }
        }
    }

    window.getUniqueIdDOM = function (DOM, id, seperator = "_t_") {
        let theID = id ? id : DOM.attr('id');
        theID = theID.split(seperator);
        theID = theID[0];

        const finallID = theID + seperator + generateTimestampLastNumbers();
        DOM.attr('id', finallID);
        return finallID;
    }

    window.generateRandomCharacter = function (chrNumber = 5, seperator = "_") {
        let text = "";
        let randomInt = 0;
        let randomChar = '';
        let baseCharInt = 97;
        while (text.length < chrNumber) {
            randomInt = Math.floor(Math.random() * 26) + baseCharInt;
            randomChar = String.fromCharCode(randomInt);
            text += randomChar;
        }

        const timestamp = Date.now();

        text += seperator + (timestamp.toString().substring(timestamp.toString().length - chrNumber));

        return text;
    }

    window.generateElementElements = function (element, removeAfterClick = true) {
        const enableAttr = element.attr("data-enable");
        const target = $(element.attr("data-target"));

        const enableAttrPrefix = "data-old-";
        const theOldPrefix = enableAttrPrefix + enableAttr;

        if (target.length != 1) return false;
        if (target.attr(theOldPrefix)) return false;

        const targetAttr = target.attr(enableAttr);
        target.attr(theOldPrefix, targetAttr);

        target.attr(enableAttr, null);


        element.on("click", function () {
            target.attr(enableAttr, target.attr(theOldPrefix));

            if (removeAfterClick)
                element.remove();
        });
    }

    window.generateTimestampLastNumbers = function (number = -4) {
        return Date.now().toString().slice(number);
    }

    window.initQuantity = function (element) {
        const attrLoaded = "data-qu-loaded";

        if (element.attr(attrLoaded)) {
            return false;
        }

        const numberDOM = element.find("[type=number]:first()");
        const min = numberDOM.attr("min") ? parseInt(numberDOM.attr("min")) : 1;
        const max = numberDOM.attr("max") ? parseInt(numberDOM.attr("max")) : -1;
        const step = numberDOM.attr("step") ? parseInt(numberDOM.attr("step")) : 1;

        numberDOM.attr("readonly", true);

        let targetOne = element.attr("data-qu-target") ?? null;
        if (targetOne) {
            targetOne = $(targetOne).first();
            if (!targetOne.length) {
                targetOne = null;
            }
        }

        if (targetOne) {
            targetOne.val() ? targetOne.attr("data-qu-tag", "input") : targetOne.attr("data-qu-tag", "element");
            const quValue = elementDataSetOrGet(targetOne, targetOne.attr("data-qu-tag"));
            if (!element.attr("data-qu-value")) {
                element.attr("data-qu-value", quValue);
            }

            element.attr("data-qu-value", commaToDot(element.attr("data-qu-value")));

            unitValue = element.attr("data-qu-value");
        }

        let nav = $(`<span class="quantity-actions btn no-radius border border-danger text-danger float-start" id="decrease">-</span>`);

        numberDOM.before(nav);

        nav = nav.clone().attr("id", "increase").text("+").removeClass("border-danger").removeClass("text-danger").addClass("border-success").addClass("text-success");
        numberDOM.after(nav);

        element.find(".quantity-actions").on("mousedown", function (e) {
            const thisElement = $(e.target);
            const action = thisElement.attr("id");

            const numberDOM = thisElement.parent().find("[type=number]:first()");
            const currentValue = parseInt(numberDOM.val());
            let tmpValue = currentValue;

            if (action === "increase") {
                if (currentValue < max || max === -1) {
                    tmpValue = currentValue + step;
                }
            } else if (action === "decrease") {

                if (min < currentValue) {
                    tmpValue = currentValue - step;
                }
            }

            numberDOM.val(tmpValue).trigger("input");

            // using element.attr("data-qu-value") REQUIRE maybe in runtime qu-value change 
            if (targetOne && element.attr("data-qu-value")) {
                let theValue = multiplyNumbersBigJs([tmpValue, element.attr("data-qu-value")]).roundNumberByPrecision();
                elementDataSetOrGet(targetOne, targetOne.attr("data-qu-tag"), commaToDot(String(theValue), false), "set");
                targetOne.attr('data-value', theValue);
            }

            if (!window.theInterval)
                window.theInterval = setInterval(function () {
                    thisElement.trigger("mousedown");
                }, 150)
        })

        element.find(".quantity-actions").on("mouseup", function (e) {
            clearInterval(window.theInterval);
            window.theInterval = null;
        });


        // load qunatity action trigger
        element.find(".quantity-actions#increase").trigger("mousedown");
        element.find(".quantity-actions#increase").trigger("mouseup");

        element.find(".quantity-actions#decrease").trigger("mousedown");
        element.find(".quantity-actions#decrease").trigger("mouseup");

        element.attr(attrLoaded, "true");
    }

    window.getTag = function (element) {
        return element.get(0).tagName.toLowerCase();
    }

    window.elementDataSetOrGet = function (element, dataTag, value = null, action = "get") {
        if (dataTag == "input") {
            return action == "get" ? element.val() : element.val(value);
        } else if (dataTag == "element") {
            return action == "get" ? element.text() : element.text(value);
        }
    }

    window.commaToDot = function (str, fromComma = true) {
        str = String(str);
        return fromComma ? str.replace(/,/gi, ".") : str.replace(/\./gi, ",");
    }

    window.sanitizeForInt = function (str) {
        if ((typeof (str)).toLocaleLowerCase() !== "string") return str;

        const seperator = "_";

        let listOfIntRaw = str.replace(/\D/gi, seperator).split(seperator);
        let listOfInt = [];

        for (let intData of listOfIntRaw) {
            intData = parseInt(intData);

            if (isNaN(intData)) {
                continue;
            }

            listOfInt.push(intData);
        }

        return listOfInt;
    }

    window.getAllAttributes = function (nodeOne) {
        var attrs = {};
        $.each(nodeOne[0].attributes, function (index, attribute) {
            attrs[attribute.name] = attribute.value;
        });

        return attrs;
    }

    window.addAttrGrouply = function(element , objList){
        if(!element.length) return;

        for (const attrKey of Object.keys(objList)) {
            const attrValue = objList[attrKey];
            element.attr(attrKey , attrValue);
        }

    }

    window.removeAttrGrouply = function(element , list){
        if(!element.length) return;

        for (const attrKey of list) {
            element.removeAttr(attrKey);
        }
    }

    window.get_select_TextAndValue = function (element) {
        const value = element.val();
        let text = "";

        let selectedOption = null;

        const options = element.find("option");
        for (let option of options) {
            option = $(option);
            if (option.val() == value) {
                text = option.text();
                selectedOption = option;
                break;
            }
        }

        return {
            text: text,
            value: value,
            extra: getAllAttributes(selectedOption)
        }
    }

    window.whichElementisViewing = function (elements) {
        const list = [];
        for (let element of elements) {
            element = $(element);
            list.push(Math.round(Math.abs(getOffsetElement(element).top)));
        }

        let min = Math.min(...list);

        if (!(min < 250)) {
            min = null;
        }

        const index = list.findIndex((element) => element == min)

        let theElement = null;

        theElement = elements[index];

        return theElement;
    }

    window.chunkArray = function (list, chunkSize) {
        const newList = [];
        for (let i = 0; i < list.length; i += chunkSize) {
            const chunk = list.slice(i, i + chunkSize);
            newList.push(chunk);
        }

        return newList;
    }

    window.isIterable = function (obj) {
        if (obj == null) {
            return false;
        }
        return typeof obj[Symbol.iterator] === 'function';
    }

    window.loadInputValueByJs = function (parentName = null, selectorPrefix = ":") {

        if (!jsonDataServer.keymap_data_page) return false;

        let data = jsonDataServer.keymap_data_page;
        if (parentName) {
            data = data[parentName] ?? [];
        }

        if (data) {
            const keys = Object.keys(data);
            for (const theKey of keys) {
                const element = data[theKey];

                if (!element) continue;

                const childName = (element.key ?? false) ? element.key : theKey;
                const prefix = parentName ? `${parentName}${selectorPrefix}` : "";
                const finalName = `${prefix}${childName}`;
                const value = (element.value ?? false) ? element.value : element;

                const selectorDOM = `[data-group-id="${finalName}"]`;

                let theDOM = $(selectorDOM);

                if (1 < theDOM.length) {
                    theDOM = $(selectorDOM + `[value='${value}']`);
                }

                if (theDOM.length) {
                    theDOM.val(value).trigger("input");

                    if (theDOM.attr("type") == "checkbox" || theDOM.attr("type") == "radio") {
                        const labelDOM = theDOM.next();

                        if (getTag(labelDOM) == "label") {
                            labelDOM.trigger("click");
                        }

                    }
                }

            }
        }
    }

    window.inputShowValue = function (e) {
        const thisElement = $(this);
        const showValueType = thisElement.attr("data-show-type") ? thisElement.attr("data-show-type") : "value";

        if (showValueType == "value") {
            thisElement.attr("value", thisElement.val());
        } else if (showValueType == "html") {
            thisElement.html(thisElement.val());
        }

    }

    window.dataFromDomToAnother = function (attrs) {
        return attrs['to'](attrs['from']);
    }

    window.makeElementLoading = function (dom, reverse = false) {
        return !reverse ? dom.addClass("mask-loading") : dom.removeClass("mask-loading");
    }

    window.base64DecodeFixed = function (text) {
        return decodeURIComponent(atob(text).split('').map(function (c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
    }

    window.htmlentities = function (str) {
        return str.replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    window.encodeUrlEncodePlusBase64 = function (str, reverse = false) {
        return !reverse ? btoa(encodeURI(str)) : decodeURI(atob(encodeURI(str)));
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

    window.getCurrentDirectionY = function () {
        if (window.scrollYWindow < window.scrollY) {
            window.scrollYDirection = "bottom";
        } else {
            window.scrollYDirection = "top";
        }

        window.scrollYWindow = window.scrollY;
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

    Number.prototype.roundNumberByPrecision = function (n = 2) {
        const reg = new RegExp("^-?\\d+(?:\\.\\d{0," + n + "})?", "g")
        const a = this.toString().match(reg)[0];
        const dot = a.indexOf(".");
        if (dot === -1) {
            return a + "." + "0".repeat(n);
        }
        const b = n - (a.length - dot) + 1;
        return b > 0 ? (a + "0".repeat(b)) : a;
    }

    window.twoPrecisionFloat = function (number) {
        if (String(number).search(/\D/gi) != -1) return number;

        return Number(number).roundNumberByPrecision();
    }

    window.timerFeature = function (element) {
        const listActions = {
            "reload": () => { window.location.reload() },
        }
        const action = element.attr('data-action') == "lesser" ? -1 : 1;
        const onEnd = element.attr("data-on-end");

        if (!listActions.hasOwnProperty(onEnd)) {
            listActions[onEnd] = function () { window[onEnd](element) };
        }

        var interval = setInterval(function () {
            let seconds = Number(element.text());

            if (element.data("pause")) {
                clearInterval(interval);
            }

            const new_second = action == -1 ? seconds - 1 : seconds + 1;

            element.text(new_second);

            if (new_second <= 0 && action == -1) {
                if (onEnd && listActions.hasOwnProperty(onEnd)) {
                    listActions[onEnd](element);
                    clearInterval(interval);
                }

                return false;
            }


        }, 1000)
    }

    window.runUntilExists = function (mVar, cbk) {
        setTimeout(function () {
            const a = mVar[0];
            const b = mVar[1];

            if (!a[b]) {
                console.log("searching for " + b);
                runUntilExists(mVar, cbk);
            } else {
                cbk['action'](...cbk['args']);
            }
        }, 200);
    }

    window.leadingTimeWithZeroCore = function (value, seperator = ":") {
        let valTimeFinall = "";

        if (!value) return valTimeFinall;

        const valTime = value.split(seperator);
        for (let i = 0; i < valTime.length; i++) {
            const valTimeItem = Number(valTime[i]);
            if (valTimeItem < 10) {
                valTime[i] = "0" + valTimeItem;
            }
        }

        if (valTime)
            valTimeFinall = valTime.join(seperator);

        return valTimeFinall
    }

    window.leadingTimeWithZero = function (element) {
        const theVal = leadingTimeWithZeroCore(element.val());
        element.val(theVal).attr("value", theVal);
    }

    window.leadTheTimeWithZero = function (unix, e) {
        // related to persian date picker
        const thisElement = $(e.model.inputElement);

        let time = new persianDate(unix).format(e.format);
        time = leadingTimeWithZeroCore(time);

        return time;
    }

    window.generateMapMarker = function (element) {
        // GOOGLE MAP MAP MAKER
        const attrLocation = element.attr("data-coordinate");
        const attrZoom = element.attr("data-zoom") ?? 15;
        const attrScale = element.attr("data-scale") ?? 1;

        if (!attrLocation) return false;

        const width = window.innerWidth * attrScale;
        const height = parseInt(width / 1.333333); // ratio like 800,600

        const html = `<div class="wrapper text-center p-3"><iframe src="https://maps.google.com/maps?q=${attrLocation}&z=${attrZoom}&output=embed" class="border border-0 w-100" width="${width}" height="${height}" allowfullscreen="true" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>`;
        const newElement = $(html);

        element.replaceWith(newElement);

        return newElement;
    }

    window.createMapFromInputs = function (element) {

        const mOptions = getDataOptionPlugin(element);

        if(!mOptions || !Object.keys(mOptions).length) return;

        const result = {};

        element.on("click" , function(){
            for (const selector of Object.keys(mOptions)) {
                const details = mOptions[selector];
                const itemElement = $(selector);

                if(!itemElement.length) continue;

                result[selector] = null;

                if(details['get'] == "val"){
                    result[selector] = itemElement.val(); 
                }else if(details['get'] == "text"){
                    result[selector] = itemElement.text(); 
                }else if(details['get'] == "prop"){
                    result[selector] = itemElement.prop(details['type']); 
                }
            }

            const jsonMap = JSON.stringify(result);
            element.attr('data-result' , jsonMap);
            element.trigger("loadedKeyMap");

            const clipboardBtn = $("#" + element.attr("id") + "-copy");
            if(clipboardBtn.length){
                clipboardBtn.attr('data-clipboard-text' , jsonMap);
                clipboardBtn.trigger("click");

                const oldText = element.text();
                element.text(__local("Copied"));
                setTimeout(() => element.text(oldText) , 2000);
            }
        });

    }

    window.initStartRating = function (element) {
        const parent = element.parent();
        const starElmentDOM = element.find(".stars-shape:first()").get(0);
        const starDOM = element.find(".star").get();
        const currentScoreDOM = parent.find("#current");

        const selectorRatingInput = "[name=rating]:first()";
        const absoluteRatingInput = $(selectorRatingInput);

        // reset value to sync `STARS` and `INPUT`
        if (absoluteRatingInput.val()) {
            absoluteRatingInput.attr("value", null);
        }

        starElmentDOM.addEventListener("mouseleave", handlerEmptyStars);

        for (var i = 0; i < starDOM.length; i++) {
            const currentElement = starDOM[i];
            currentElement.addEventListener("mouseover", handlerFillStars)

            currentElement.addEventListener("click", function (event) {
                const score = parseInt(currentElement.getAttribute("data-item-number"));
                // set score
                parent.find(selectorRatingInput).val(score);
                currentScoreDOM.text(score);
            })
        }

        registerMouseLeaveStar();

        function registerMouseLeaveStar() {
            for (var i = 0; i < starElmentDOM.length; i++) {
                const currentElement = starElmentDOM[i];
                currentElement.addEventListener("mouseleave", handlerEmptyStars)
            }
        }

        function handlerFillStars(event) {
            const currentElement = event.fakeTarget ? event.fakeTarget : this;
            emptyTheStars();

            const currentElementParent = currentElement.parentElement;
            const currentElementItemNumber = currentElement.getAttribute("data-item-number");
            for (var i = 0; i < currentElementItemNumber; i++) {
                const currentElement = currentElementParent.querySelector("#star" + (i + 1));
                if (currentElement == null) continue;
                currentElement.style.fill = "#ffc107";
            }

        }

        function emptyTheStars() {
            for (var j = 0; j < starDOM.length; j++) {
                const currentElement = starDOM[j];
                currentElement.removeAttribute("style");
            }
        }

        function handlerEmptyStars(event) {
            emptyTheStars();
            if (currentScoreDOM.text() != "0") {
                const index = parseInt(currentScoreDOM.text()) - 1;
                handlerFillStars({
                    fakeTarget: starDOM[index]
                });

            }
        }
    }

    window.scrollToTopHandler = function (selector = "scroll-top", animationScroll = true, cbk = "") {
        // DOM
        const scrollToTopDOM = document.getElementById(selector);

        if (!scrollToTopDOM) return false;

        $(scrollToTopDOM).addClass("bi bi-arrow-down-circle-fill fs-2 text-warning");

        let scrollToTopDOMActive = null;

        // property
        const maxDegreeRotate = 180;
        let isScrolling = false;


        // set handlers
        window.onscroll = windowOnScrollHandler;
        scrollToTopDOM.onclick = scrollToTopDOMClickHandler;


        // handlers
        function windowOnScrollHandler(event) {
            const thisElement = this;
            const offsetScrollY = parseInt(thisElement.scrollY);

            let finallDegreeRotate = offsetScrollY / 2;

            if (maxDegreeRotate < finallDegreeRotate) {
                finallDegreeRotate = maxDegreeRotate;
                if (!isScrolling) {
                    scrollToTopDOM.classList.add('active');
                }

            } else {
                scrollToTopDOM.classList.remove('active');
            }

            scrollToTopDOM.style.transform = `rotate(${finallDegreeRotate}deg)`;
        }

        function scrollToTopDOMClickHandler(event) {

            if (!isScrolling && scrollToTopDOM.classList.contains("active") && 0 < window.scrollY) {
                isScrolling = true;
                scrollToTopDOM.classList.remove('active');
                if (animationScroll)
                    scrollToTopDOMScrollAnimation();
                else {
                    cbk();
                    isScrolling = false;
                }
            }

        }

        function scrollToTopDOMScrollAnimation() {

            if (window.scrollY <= 0) {
                isScrolling = false;
                return false;
            }


            window.scrollBy(0, -50);
            setTimeout(scrollToTopDOMScrollAnimation, 1);
        }
    }



    window.generatePersianDatepickerFeature = function (element, options = {}) {
        if (typeof persianDatepicker === "undefined") return false;

        const mOptions = getDataOptionPlugin(element, options);

        // should before init
        if (mOptions["format"].search("H:m:s") != -1 && element.val()) {
            leadingTimeWithZero(element);
        }

        // has formatter cbk
        if (mOptions["formatter"]) {
            const cbk = mOptions['formatter'];

            mOptions['formatter'] = function (unix) {
                return typeof window[cbk] != "undefined" ? window[cbk](unix, this) : new persianDate(unix).format(this.format);
            }

        }




        const res = element.persianDatepicker(mOptions);

        // set timestamp for X format
        if (mOptions["format"] == "X") {
            element.on('input', res, function (e) {
                const pd = e.data;
                const thisElement = $(e.target)
                let val = thisElement.val();

                if (val) {
                    val = val * 1000;
                }
                pd.setDate(val);
            });
            element.trigger("input");
        }

        return res;
    }

    window.generateZebraDatepickerFeature = function (element, options = {}) {
        if (typeof $().Zebra_DatePicker === "undefined") return false;

        const mOptions = getDataOptionPlugin(element, options);

        const res = element.Zebra_DatePicker(mOptions);

        element.attr("data-zebra-id", "zbr-" + generateRandomCharacter());

        return res;
    }

    window.generatePriceSeperator = function (element, options = {}) {

        if (element.attr("prc-loaded")) return -1;

        const cbkBefore = element.attr("data-cbk-before");
        if (window[cbkBefore]) {
            window[cbkBefore](element);
        }

        const mOptions = getDataOptionPlugin(element, options);
        mOptions["seperatorSign"] = mOptions["seperatorSign"] ?? ".";

        const resultDomVal = element.find(".result.value");
        const resultDomText = element.find(".result.text");

        const fullDOM = element.find(".full");
        const dotDOM = element.find(".dot");

        function inputHandler(e) {
            const thisElement = $(this);

            if (dotDOM.val() == "") {
                dotDOM.val("0");
            }

            const fullDOMVal = fullDOM.val()
            const dotDOMVal = dotDOM.val();

            let value = Number(fullDOMVal + "." + dotDOMVal).roundNumberByPrecision();
            value = value.replace(/\./gi, mOptions["seperatorSign"]);


            resultDomVal.val(value).trigger("input");
            resultDomText.text(value);
        }


        fullDOM.on("input", inputHandler);
        dotDOM.on("input", inputHandler);

        // to get old value on load
        fullDOM.trigger("input");


        function changeCurrentValue() {
            const thisElement = $(this);
            thisElement.attr("data-old-value", thisElement.val());

            thisElement.val("");
        }

        fullDOM.on("focus", changeCurrentValue);
        dotDOM.on("focus", changeCurrentValue);

        function changeOldValue() {
            const thisElement = $(this);
            const oldValue = thisElement.attr("data-old-value");

            if (thisElement.val() == "") {
                thisElement.val(oldValue);
            } else {
                thisElement.attr("data-old-value", thisElement.val())
            }
        }

        fullDOM.on("blur", changeOldValue);
        dotDOM.on("blur", changeOldValue);

        element.attr("prc-loaded", true);
        return element;
    }

    window.arrayFill = function (_length, defaultValue = null) {
        const _array = Array.from({
            length: _length
        }, () => defaultValue);

        return _array;
    }

    window.sumTimes = function (times) {

        if (!times || !times.length) return null;

        if (typeof (times) != "object") times = [times];

        const basePrefixTime = "00";
        let totalSeconds = 0;

        const convertToSeconds = function (h, m, s) {
            const seconds = h * 3600 + m * 60 + s;
            return seconds;
        };

        const convertToTime = function (seconds) {
            let hours = Math.floor(seconds / 3600);
            let minutes = Math.floor((seconds / 60) % 60);
            seconds = (seconds % 60);

            if (hours < 10) hours = `0${hours}`;
            if (minutes < 10) minutes = `0${minutes}`;
            if (seconds < 10) seconds = `0${seconds}`;

            return `${hours}:${minutes}:${seconds}`;
        }

        for (let i = 0; i < times.length; i++) {
            let time = times[i];

            if (!time) continue;

            let timeList = time.split(":");

            // polyfill hours:seconds
            if (!timeList.length) continue;
            else if (3 < timeList.length) timeList = timeList.slice(timeList, 0, 3);
            else if (timeList.length < 3) {
                const polyfill = arrayFill(3 - timeList.length, basePrefixTime);
                timeList = [...polyfill, ...timeList];
            }


            // cast integer
            timeList = timeList.map((element) => parseInt(element));
            seconds = convertToSeconds(timeList[0], timeList[1], timeList[2]);

            totalSeconds += seconds;
        }



        let result = convertToTime(totalSeconds);

        return result;
    }

    window.getPartOfTime = function (time, maxIndex = 3, seperator = ":") {
        let result = time.split(seperator).slice(0, maxIndex).join(seperator);

        return result;
    }

    window.getTimeFormat = function (timeArray, reset = []) {

        for (const element of reset) {
            timeArray[element] = "00";
        }

        timeArray = timeArray.join(":");

        return timeArray;
    }

    window.getTime = function (exportTo = "string", year = null, month = null, day = null) {
        const date = !year ? new Date() : new Date([year, month, day]);

        const str = leadingTimeWithZeroCore(`${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`);

        return exportTo == "string" ? str : str.split(":");
    }

    window.getDate = function (tmpDate = null, exportTo = "string", seperator = "-", leadingZero = false) {
        const date = tmpDate ? tmpDate : new Date();
        const leadingStrZero = leadingZero ? "0" : "";

        let year = date.getFullYear();
        let month = date.getMonth() + 1;
        let day = date.getDate();

        if (leadingStrZero != "") {
            if (month < 10) month = "0" + month;
            if (day < 10) day = "0" + day;
        }

        const str = `${year}${seperator}${month}${seperator}${day}`;

        return exportTo == "string" ? str : str.split(seperator, "-");
    }

    window.getDateParts = function (strDate, seperator = "-") {
        let strList = strDate.split(seperator);

        let isRtl = 31 < strList[2];

        if (isRtl) {
            strList = strList.reverse();
        }

        const theList = strList;

        return theList;
    }

    window.getMonthName = function (monthNumber) {
        const date = new Date();
        date.setMonth(monthNumber - 1);

        return date.toLocaleString('en-US', { month: 'long' });
    }

    window.addDays = function (date, days) {
        var result = new Date(date.join("-"));
        result.setDate(result.getDate() + days);
        return result;
    }

    window.plusNumbersBigJs = function (list) {
        // REQUIRED js.big
        let result = 0;
        let instance = Big(result);
        for (const item of list) {
            result = instance.plus(item).toNumber();
            instance = Big(result);
        }

        return instance.toNumber();
    }

    window.subNumbersBigJs = function (list) {
        // REQUIRED js.big
        const [a, b] = list;
        let result = a;
        let instance = Big(result);

        result = instance.minus(b).toNumber();

        return result;
    }

    window.multiplyNumbersBigJs = function (list) {
        // REQUIRED js.big
        let result = 1;
        let instance = Big(result);
        for (const item of list) {
            result = instance.times(item).toNumber();

            instance = Big(result);
        }

        return instance.toNumber();
    }

    window.powNumbersBigJs = function (list) {
        // REQUIRED js.big
        const [a, b] = list;
        let result = a;
        let instance = Big(result);

        result = instance.pow(b).toNumber();

        return result;
    }

    window.divideNumbersBigJs = function (list) {
        // REQUIRED js.big
        const [a, b] = list;
        let result = new Big(a);
        let instance = new Big(b);

        result = result.div(instance).toFixed();

        return result;
    }

    window.clipboardIT = function (element) {
        let elementID = element.attr('id');

        if (!elementID) {
            element.attr('id', generateRandomCharacter());
            return clipboardIT(element);
        }

        elementID = "_" + elementID;

        if (window[elementID]) {
            return;
        }

        window[elementID] = element.get(0).outerHTML;

        return true;
    }

    window.generateHtmlDynamically = function (list, html) {
        let dynamicHTML = html;
        for (const keyItem of Object.keys(list)) {
            const element = list[keyItem];
            dynamicHTML = dynamicHTML.replaceAll(keyItem, element);
        }

        return dynamicHTML;
    }

    window.changeColorMode = function(){
        const theForm = $('#color_mode_form');

        finalAction = parseUrlParametersToRealUrl(theForm.attr("data-action") , {
            "mode" : theForm.find("#color_mode").val()
        }); 

        theForm.attr("action" , finalAction)

        theForm.submit();
    }

    /* ================> END Functions */

    /* ================> options */

    window.zebraDatePickerOptions = {
        onChange: function (view, elements) {

            const thisElement = $(this);
            thisElement.trigger("input");

            window[thisElement.attr('data-zebra-id')] = thisElement.val();

            var interval = setInterval(function () {
                if (thisElement.val() && thisElement.val() != window[thisElement.attr('data-zebra-id')]) {
                    clearInterval(interval);

                }
            })

        }
    }

    window.shamsiDatePickerOptions = {
        calendarType: 'gregorian',
        toolbox: {
            calendarSwitch: {
                enabled: false
            }
        },
        onSelect: function () {

            const thisElement = $(this.model.inputElement);

            if (this.format.toLowerCase() === "x" && (!this.default_ts && !thisElement.attr("data-no-range"))) {

                const timestamp = thisElement.val();
                const now = new persianDate(timestamp * 1000);
                const dateObj = now.startOf('day').ON;

                const date = dateObj.gregorian;
                let finalTimestamp = 0;
                let dateArray = [];

                if (thisElement.attr("id") == "from-date" || thisElement.hasClass("from-date")) {
                    dateArray = [date.year, date.month + 1, date.day, 0, 0, 0, 0];
                } else if (thisElement.attr("id") == "to-date" || thisElement.hasClass("to-date")) {
                    dateArray = [date.year, date.month + 1, date.day, 23, 59, 59, 0];
                }

                finalTimestamp = new persianDate(dateArray);

                finalTimestamp = finalTimestamp.unix();
                thisElement.val(finalTimestamp);
            }

            thisElement.trigger('input');
        },
        format: 'X',
        timePicker: {
            enabled: true
        },
        initialValue: false,
        navigator: {
            scroll: {
                enabled: true
            }
        }
    }

    window.sweetAlertOptions = {
        question: {
            title: "title",
            text: "",
            type: "question",
            showCancelButton: true,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: __local('YES'),
            cancelButtonText: __local('NO')
        },
        info: {
            title: __local("Server Message"),
            text: '',
            type: 'info',
            confirmButtonColor: '#3b5de7',
            confirmButtonText: __local('OK')
        },
        success: {
            title: "title",
            text: '',
            type: 'success',
            confirmButtonColor: '#4caf50',
            confirmButtonText: __local('OK'),
        }

    }

    window.ajaxOptions = {
        url: "",
        data: {},
        dataType: "json",
        error: function (e) {
            ajaxErrorHandler(e);
        },
        errorExtra: function () {

        },
        success: function (res) { },
        beforeSend: function () { },
    }

    window.columns = {
        xs: 0,
        sm: 576,
        md: 768,
        lg: 992,
        xl: 1200,
    }

    window.columnCallbackList = {
        xs: [],
        sm: [],
        md: [],
        lg: [],
        xl: [],
    }

    window.scrollYWindow = 0;

    /* ================> END options */

    /* ================> INIT */

    // execute reload page which send from server
    if (typeof jsonDataServer != "undefined" && jsonDataServer['reload_page_required']) {
        location.reload();
    }

    // get current column state (sm,md,lg,xl)
    window.currentBootstrapColumn = getCurrentBootstrapColumn();

    // call function on every column
    setTimeout(callColumnCallback, 200);

    // get current column state
    $(window).on("resize", () => {
        window.currentBootstrapColumn = getCurrentBootstrapColumn();
    });

    // call function on every column
    $(window).on("resize", callColumnCallback);

    // get direction window
    $(window).on("scroll", getCurrentDirectionY);
    getCurrentDirectionY();

    // focus element
    const the_focusElement = getExactQuery("focus_element");
    if (typeof the_focusElement != "undefined") {
        focusElementByUrl(the_focusElement);
    }

    // get value and put in html attr VALUE
    $(document).on("input", ".input-show-value", inputShowValue)

    $('.clipboardIT').each(function (index, element) {
        element = $(element);
        clipboardIT(element);
    });

    // init timer
    $('.timer').each(function (index, element) {
        element = $(element);
        timerFeature(element);
    });

    // re shape number to fa number
    $(".fa-number").each(cbkFaNumber);

     // execute fragment command
     setTimeout(runFragmentCommand, 1000);

    // reload captcha button
    $('.captcha-reload').each(function (index, element) {
        element = $(element);
        element.on("click", onCaptchaReloadBtnClick);
    });


    $(".create-map-from-inputs").each(function(i , element){
        createMapFromInputs($(element));
    });


    /* ================> END INIT */
});