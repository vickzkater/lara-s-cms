/**
 * The Helper JS - a lot of JS helper functions that are ready to help in your project
 * Version: 1.0 (2020-07-04)
 *
 * Copyright (c) KINIDI Tech and other contributors
 * Released under the MIT license.
 * For more information, see https://kiniditech.com/ or https://github.com/vickzkater
 *
 * https://github.com/vickzkater/the-helper-js
 */

/**
 * for displaying image after browse it before uploaded
 * Example: implement onchange="readURL(this, 'before')" in input type file in form
 *
 * @param {element input file form} input
 * @param {string} position_image - (after/before)
 */
function readURL(input, position_image = "after") {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            if (position_image == "before") {
                $(input).prev("img").attr("src", e.target.result);
            } else {
                $(input).next("img").attr("src", e.target.result);
            }
        };

        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * for reset image preview to default image (no image)
 *
 * @param {element input file form} input
 * @param {string} no_image - image "no image" URL
 * @param {string} position_image - (after/before)
 */
function reset_img_preview(input, no_image, position_image = "after") {
    if (confirm("Are you sure to delete this uploaded image?")) {
        if (position_image == "before") {
            $(input).prev("img").attr("src", no_image);
        } else {
            $(input).next("img").attr("src", no_image);
        }
        $(input + "-delbtn").hide();
        $(input + "-delete").val("yes");
    }
}

/**
 * for replaces some characters with some other characters in a string.
 *
 * @param {element input file form} input
 * @param {string} no_image - image "no image" URL
 * @param {string} position_image - (after/before)
 */
function replace_all(search, replacement, target) {
    if (target !== null) {
        return target.split(search).join(replacement);
    }
    return "";
}

/**
 * formats a number with grouped thousands.
 *
 * @param {integer} number required
 * @param {char} separator optional
 */
function number_format(number, separator = ",") {
    // sanitizing number
    number = replace_all(" ", "", number);
    number = replace_all(",", "", number);
    number = replace_all(".", "", number);

    // check is negative number
    var negative = number.substring(0, 1);
    if (negative == "-") {
        number = number.substring(1);
    } else {
        negative = "";
    }

    number = "" + Math.round(number);
    if (number.length > 3) {
        var mod = number.length % 3;
        var output = mod > 0 ? number.substring(0, mod) : "";
        for (i = 0; i < Math.floor(number.length / 3); i++) {
            if (mod == 0 && i == 0)
                output += number.substring(mod + 3 * i, mod + 3 * i + 3);
            else
                output +=
                    separator + number.substring(mod + 3 * i, mod + 3 * i + 3);
        }
        return negative + output;
    } else return negative + number;
}

/**
 * copy text to the clipboard
 *
 * @param {element input id} element_id
 * @param {boolean} alert_copied
 * @param {string} alert_message
 */
function click_to_clipboard(
    element_id,
    alert_copied = true,
    alert_message = "Copied the text: "
) {
    /* Get the text field */
    var copyText = document.getElementById(element_id);

    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /*For mobile devices*/

    /* Copy the text inside the text field */
    document.execCommand("copy");

    if (alert_copied) {
        /* Alert the copied text */
        alert(alert_message + copyText.value);
    }
}

/**
 * to open a new window
 *
 * @param {string} params - URL
 * @param {integer} width
 * @param {integer} height
 * @param {string} name - window title
 */
function open_window(params, width, height, name) {
    var screenLeft = 0,
        screenTop = 0;

    if (!name) name = "MyWindow";
    if (!width) width = 600;
    if (!height) height = 600;

    var defaultParams = {};

    if (typeof window.screenLeft !== "undefined") {
        screenLeft = window.screenLeft;
        screenTop = window.screenTop;
    } else if (typeof window.screenX !== "undefined") {
        screenLeft = window.screenX;
        screenTop = window.screenY;
    }

    var features_dict = {
        toolbar: "no",
        location: "no",
        directories: "no",
        left: screenLeft + ($(window).width() - width) / 2,
        top: screenTop + ($(window).height() - height) / 2,
        status: "yes",
        menubar: "no",
        scrollbars: "yes",
        resizable: "no",
        width: width,
        height: height,
    };
    features_arr = [];
    for (var k in features_dict) {
        features_arr.push(k + "=" + features_dict[k]);
    }
    features_str = features_arr.join(",");

    // var qs = "?" + $.param($.extend({}, defaultParams, params));
    // var win = window.open(qs, name, features_str);
    var win = window.open(params, name, features_str);
    win.focus();
    return false;
}

/**
 * set URL parameters (request URI)
 * 
 * @param {string} key
 * @param {string} value 
 */
function set_param_url(key, value) {
    var uri = window.location.href;
    var result = uri;
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf("?") !== -1 ? "&" : "?";
    if (uri.match(re)) {
        result = uri.replace(re, "$1" + key + "=" + value + "$2");
    } else {
        result = uri + separator + key + "=" + value;
    }

    var arr_uri = result.split("?");

    window.history.pushState(
        $(document).find("title").text(),
        $(document).find("title").text(),
        "?" + arr_uri[1]
    );
}

/**
 * sanitizing value of number
 *
 * @param {element input text} elm
 */
function numbers_only(elm) {
    var code = elm.which ? elm.which : elm.keyCode;

    // 37 = left arrow, 39 = right arrow.
    if (code !== 37 && code !== 39) {
        elm.value = elm.value.replace(/[^0-9]/g, "");
    }
}

/**
 * sanitizing value of username - only allow alphanumerics, (.) dot, and (_) underscore
 * 
 * @param {element input form} elm 
 */
function username_only(elm) {
    var code = elm.which ? elm.which : elm.keyCode;

    // 37 = left arrow, 39 = right arrow.
    if (code !== 37 && code !== 39) {
        elm.value = elm.value.replace(/[^a-z0-9A-Z_.]/g, "");
    }
}

/**
 * sanitizing value of text - only allow alphanumerics and whitespace
 * 
 * @param {element input form} elm 
 */
function alphanumerics_only(elm) {
    var code = elm.which ? elm.which : elm.keyCode;

    // 37 = left arrow, 39 = right arrow.
    if (code !== 37 && code !== 39) {
        elm.value = elm.value.replace(/[^a-z0-9A-Z ]/g, "");
    }
}

/**
 * for remove uploaded file
 *
 * @param {element id} input
 */
function remove_uploaded_file(input) {
    if (confirm("Are you sure to delete this uploaded file?")) {
        $(input + "-file-preview").remove();
        $(input + "-delbtn").hide();
        $(input + "-delete").val("yes");
    }
}
