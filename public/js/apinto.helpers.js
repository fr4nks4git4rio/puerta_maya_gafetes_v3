$(document).ready(function () {

    // Dropzone.autoDiscover = false;

    //Enviamos el token de laravel en las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //Evitamos peticiones duplicadas
    ajaxRunnig = false;
    var ajaxlastUrl = false;


    $(document).ajaxSend(function (event, request, settings) {

        if (ajaxRunnig == true && ajaxlastUrl == settings.url && settings.allowMultipleRequests !== true) {
            APAlerts.info('Se esta procesando tu petición.');
            request.abort();
        }

        ajaxRunnig  = true;
        ajaxlastUrl = settings.url;
    });


    $(document).ajaxError(function (event, xhr, settings, thrownError) {
        APAlerts.handleServerError(thrownError, xhr,event,settings);
    });

    $(document).ajaxComplete(function (event, xhr, settings) {
        ajaxRunnig = false;
    });
    //opciones del plugin iCheck
    // $('input').iCheck({
    //     checkboxClass: 'icheckbox_square-blue',
    //     radioClass: 'iradio_square-blue',
    //     increaseArea: '20%' // optional
    // });

});

//Realiza una peticion ajax y reemplaza el contenido de un elemento con la respuesta
function ajax_update(_url, _target, callback, postData, method = 'post') {
    if (_target == undefined)
        _target = '#page-content';

    return $.ajax({
        url: _url,
        data: postData,
        type: method,

        success: function (res) {

            if (res.success == false) {
                APAlerts.error(res.message);

            } else {
                $(_target).hide().html(res).fadeIn();

                if(callback){
                    callback();
                }
            }

        },
        error: function (res) {
            if (res.statusText !== 'abort') {
                APAlerts.error(res.statusText + '<br/>' + res.responseText);
            }
        },
        complete: function (xhr) {

        }
    });
}

//Realiza una peticion ajax
function ajax_call(url, params, allowMultipleRequests) {
    if (allowMultipleRequests !== true)
        allowMultipleRequests = false;

    var overlayElement = "#content-container";

    return $.ajax({
        type: "POST",
        allowMultipleRequests: allowMultipleRequests,
        url: url,
        data: params,
        beforeSend: function (xhr) {
            // $(overlayElement).niftyOverlay('show');
        },

        success: function (res) {

            if (res.javascript != undefined) {
                eval(res.javascript);
            }

            if (res.success == true) {
                APAlerts.success(res.message);
            } else {
                APAlerts.warning(res.message);
            }

        },
        error: function (res) {

            if (res.statusText !== 'abort') {
                APAlerts.error(res.responseText);
            }
        },
        complete: function (res) {
            // $(overlayElement).niftyOverlay('hide');
        }
    });
}


function handleFormErrors(formSelector, errors, alertOnFirstError = false) {

    if (alertOnFirstError == true) {
        if (errors.length > 0) {
            $.each(errors, function (key, value) {
                APAlerts.warning(value);
                return true;
            });
        }

    }

    $.each(errors, function (key, value) {
        var e_string = '<span class="input-error text-danger small">' + value + '</span>';

        if ($(formSelector + ' input[name="' + key + '"]').length == 1) {
            $(formSelector + ' input[name="' + key + '"]').parent().append(e_string);

        } else if ($(formSelector + ' textarea[name="' + key + '"]').length == 1) {

            $(formSelector + ' textarea[name="' + key + '"]').parent().append(e_string);

        } else if ($(formSelector + ' select[name="' + key + '"]').length == 1) {
            $(formSelector + ' select[name="' + key + '"]').parent().append(e_string);
        }

        else {
            APAlerts.warning(value);
        }



    });
}

function ajaxLoader( type ) {

    if(type == 'panel'){
        return '<div class="text-center h4 panel" style="padding: 50px"> <i class="fa fa-gear fa-spin"></i> Cargando... <div>';
    }

    return '<div class="text-center h4" style="padding: 50px; width: 100%"> <i class="fa fa-gear fa-spin"></i> Cargando... <div>';
}


/*
 * LOAD SCRIPTS
 * Usage:
 * Define function = myPrettyCode ()...
 * loadScript("js/my_lovely_script.js", myPrettyCode);
 */
jsArray = {};

function loadScript(scriptName, callback) {

    if (!jsArray[scriptName]) {
        jsArray[scriptName] = true;

        // adding the script tag to the head as suggested before
        var body = document.getElementsByTagName('body')[0], script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = scriptName;

        // then bind the event to the callback function
        // there are several events for cross browser compatibility
        script.onload = callback;

        // fire the loading
        body.appendChild(script);

        // clear DOM reference
        //body = null;
        //script = null;

    } else if (callback) {
        // changed else to else if(callback)

        //execute function
        callback();
    }

}
/* ~ END: LOAD SCRIPTS */

/********* FUNCIONES GLOBALES ****************/

//funcion para filtrar en .map y .filter
function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}

//Capitaliza un string
String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

//Elmina los comentarios entre parentesis de un string
String.prototype.removeCommentParenthesis = function () {
    return this.replace(/\(.*?\)/g, '');
}


Number.prototype.toMoney = function (c, d, t) {
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};
