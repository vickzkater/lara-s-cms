/**
 * @author Vicky Budiman vicky[at]kiniditech.com
 * * for displaying image after browse it before uploaded
 * * Example: implement onchange="readURL(this, 'before')" in input type file in form
 *
 * @param {element input file form} input
 * @param {string} position_image - (after/before)
 */
function readURL(input, position_image = "after") {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            if (position_image == "before") {
                $(input)
                    .prev("img")
                    .attr("src", e.target.result);
            } else {
                $(input)
                    .next("img")
                    .attr("src", e.target.result);
            }
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function reset_img_preview(input, no_image, position_image = "after") {
    if (confirm('Are you sure to delete this uploaded image?')) {
        if (position_image == "before") {
            $(input)
                .prev("img")
                .attr("src", no_image);
        } else {
            $(input)
                .next("img")
                .attr("src", no_image);
        }
        $(input+'-delbtn').hide();
        $(input+'-delete').val('yes');
    }
}

function replace_all(search, replacement, target) {
    if (target !== null) {
        return target.split(search).join(replacement);
    }
    return "";
}

/**
 * @author Vicky Budiman vicky[at]kiniditech.com
 * * for formatting number in thousand
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
 * @author Vicky Budiman vicky[at]kiniditech.com
 * * sanitizing value of number
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

/* WINDOWS OS ONLY - melakukan copy hanya dengan klik saja */
function clickToClipboard(element) {
    var $temp = $("<input>");
    var val = $(element).attr("data-original-title");
    $("body").append($temp);
    $temp.val(val).select();

    alert('"' + val + '" Text Copied');
    document.execCommand("copy");
    $temp.remove();
}

/* Untuk membuka window baru browser dengan mengatur panjang dan lebar window */
function OpenWindow(params, width, height, name) {
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
        height: height
    };
    features_arr = [];
    for (var k in features_dict) {
        features_arr.push(k + "=" + features_dict[k]);
    }
    features_str = features_arr.join(",");

    var qs = "?" + $.param($.extend({}, defaultParams, params));
    //var win = window.open(qs, name, features_str);
    var win = window.open(params, name, features_str);
    win.focus();
    return false;
}

function set_param_url(key, value) {
    var uri = window.location.href;
    var result = uri;
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        result = uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
        result = uri + separator + key + "=" + value;
    }

    var arr_uri = result.split('?');

    window.history.pushState($(document).find("title").text(), $(document).find("title").text(), "?"+arr_uri[1]);
}

function word_only(elm) {
    var code = elm.which ? elm.which : elm.keyCode;

    // 37 = left arrow, 39 = right arrow.
    if (code !== 37 && code !== 39) {
        elm.value = elm.value.replace(/[^a-z0-9A-Z_.]/g, "");
    }
}
