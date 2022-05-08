//############################################################################################

function setMaxHeight(id) {
    let table = document.getElementById(id);
    if (table) {
        $(window).resize( function () {
            let height = window.outerHeight * 0.35;
            table.style = "height:" + height + "px;max-height:" + height + "px;overflow:auto;";
        }).trigger("resize") //to ensure that you do whatever you're going to do when the window is first loaded;
    }
}

function disconnect() {
    let url = "ajax.php?do=ajax&action=general&todo=disconnect"
    ajaxCall(url, null, function () {
        redirect('index.php');
    })
}

function ajaxCall(url, postData = null, callbackOrNodeId) {
    if (-1 === url.indexOf("ajax.php")) {
        url = 'ajax.php?' + url;
    }

    if (postData === false) {
        postData = null;
    }

    if (postData instanceof Array) {
        let formData = new FormData();
        for (let i = 0; i < postData.length; i++) {
            if (postData[i].name) {
                formData.append(postData[i].name, postData[i].value);
            }
        }
        postData = formData;
    } else if (postData instanceof Object || typeof postData === 'Object') {
        let formData = new FormData();
        if ((postData instanceof FormData === false) && (typeof postData === 'FormData' == false)) {
            for (const property in postData) {
                formData.append(property, postData[property]);
            }
        } else {
            formData = postData;
        }
        postData = formData;
    } else if (typeof postData === 'string' || postData instanceof String) {
        let formData = new FormData();
        let qsArray = postData.split('&');
        for (let i = 0; i < qsArray.length; i++) {
            let arg = qsArray[i];
            let details = arg.split('=');
            if (details[0].length) {
                formData.append(details[0], details[1]);
            }
        }
        postData = formData;
    }


    fetch(url, {
        method: "POST",
        body: postData
    })
        .then(response => {
            return response.text();
        })
        .then(data => {
            if (typeof callbackOrNodeId == 'function') {
                callbackOrNodeId(data);
            } else {
                let item = document.querySelector('#' + callbackOrNodeId);
                if (item) {
                    // item.innerHTML = data;
                    $(item).html(data);
                } else {
                    console.log(data);
                }
            }
        })
        .catch(error => {
            console.dir(url + '\nErreur: \n' + error);
        });
}
function generateFormData(formNodeOrId) {

    let form;
    if (formNodeOrId instanceof HTMLElement) {
        form = formNodeOrId;
    } else {
        form = document.getElementById(formNodeOrId);
    }

    if (!form) {
        return false;
    }

    return new FormData(form);
}
function formValidationAll() {
    let ok = true;
    document.querySelectorAll('[required]').forEach(e => {
        let formNode = e.closest('form');
        if (formNode) {
            formNode.setAttribute('novalidate', true);

            if(e.value.length == 0) {
                ok = false;
            }
        }
    });

    return ok;
}
function validateForm(formNodeOrId) {
    let form;
    if (formNodeOrId instanceof HTMLElement) {
        form = formNodeOrId;
    } else {
        form = document.getElementById(formNodeOrId);
    }

    if (!form) {
        return false;
    }

    let ok = true;
    if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()

        ok = false
    }

    form.classList.add('was-validated')

    return ok;
}
function confirme(htmlCode, callbackYes, callbackNo, confirmTitle = 'Confirmation', confirmBtnLabel = 'Confirmer', cancelBtnLabel = 'Annuler') {
    let template = `
      <div class="modal fade" style="z-index: 1050;" id="current-modal" tabindex="-1" role="dialog" aria-labelledby="confirmation" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="current-modal-title">
                    __TITLE__
                </h5>
            </div>
            <div class="modal-body">
            __CONTENT__
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-no" data-dismiss="modal">
                    __CANCEL_BTN__
                </button>
                <button type="button" class="btn btn-success btn-yes">
                    __CONFIRM_BTN__
                </button>
            </div>
          </div>
        </div>
      </div>`;

    // fermeture de tous les popovers
    $('[data-toggle="popover"]').popover('dispose');

    let currentModal = document.querySelector('#current-modal');
    if (currentModal) {
        // modal deja ouverte
        // $("#current-modal").parent('div').remove();
        // $(".modal-backdrop").remove();
        // return false;
        closeModal(currentModal);
    }

    let code = template.replace('__CONTENT__', htmlCode);
    code = code.replace('__TITLE__', confirmTitle);
    code = code.replace('__CANCEL_BTN__', cancelBtnLabel);
    code = code.replace('__CONFIRM_BTN__', confirmBtnLabel);

    let modal = document.createElement("div");
    modal.innerHTML = code;
    let div = document.body.appendChild(modal);

    // Event des boutons
    $(".btn-yes").click(function () {
        if (typeof callbackYes === 'function') {
            callbackYes(true);
        }
        $("#current-modal").modal("hide");
        // closeModal();
    });

    $(".btn-no").click(function () {
        if (typeof callbackNo === 'function') {
            callbackNo(false);
        }
        $("#current-modal").modal("hide");
        // closeModal();
    });

    if (!document.querySelector(".modal-backdrop")) {
        $(div.querySelector('#current-modal')).modal({
            backdrop: 'static'
        });
    } else {
        $(div.querySelector('#current-modal')).modal({
            backdrop: false
        });
    }

    // $(div.querySelector('#current-modal')).modal({
    //     backdrop: 'static'
    // });


    $('#current-modal').on('hidden.bs.modal', function (e) {
        // supprime du DOM
        div.parentNode.removeChild(div);
    })

}
function alerte(htmlCode, callbackClickBtn, confirmTitle = 'Alerte', confirmBtnLabel = 'OK') {
    let template = `
      <div class="modal fade" style="z-index: 1050;" id="current-modal" tabindex="-1" role="dialog" aria-labelledby="confirmation" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="current-modal-title">
                    __TITLE__
                </h5>
            </div>
            <div class="modal-body">
            __CONTENT__
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-no" style="min-width:100px;">
                    __CONFIRM_BTN__
                </button>
            </div>
          </div>
        </div>
      </div>`;

    // fermeture de tous les popovers
    $('[data-toggle="popover"]').popover('dispose');

    let currentModal = document.querySelector('#current-modal');
    if (currentModal) {
        // modal deja ouverte
        closeModal(currentModal);
    }

    let code = template.replace('__CONTENT__', htmlCode);
    code = code.replace('__TITLE__', confirmTitle);
    code = code.replace('__CONFIRM_BTN__', confirmBtnLabel);

    let modal = document.createElement("div");
    modal.innerHTML = code;
    let div = document.body.appendChild(modal);

    $(".btn-no").click(function () {
        if (typeof callbackClickBtn === 'function') {
            callbackClickBtn();
        }
        // $("#current-modal").modal("hide");
        closeModal();
    });

    if (!document.querySelector(".modal-backdrop")) {
        $(div.querySelector('#current-modal')).modal({
            backdrop: 'static'
        });
    } else {
        $(div.querySelector('#current-modal')).modal({
            backdrop: false
        });
    }

    $('#current-modal').on('hidden.bs.modal', function (e) {
        // supprime du DOM
        div.parentNode.removeChild(div);
    })

}
function showAlerte(type, message, options) {
    var flashDiv = '<div id="flashDiv" align="center" style="z-index:4000;position:absolute;width:100vw;top:50px;"></div>';
    $('body').append(flashDiv);
    let flashDivNode = $('#flashDiv');
    let html = '<div class="alert alert-' + type + ' alert-dismissible fade show" style="min-height: 45px;display: flex;align-items: center;justify-content: center;width: 30vw">' + message + '' +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
        '    <span aria-hidden="true">&times;</span>\n' +
        '  </button>' +
        '</div>';
    flashDivNode.html(html);

    setTimeout(function () {
        flashDivNode.empty();
        flashDivNode.html("");
        let toRemove = document.querySelector('#flashDiv');
        if (toRemove) {
            toRemove.parentNode.removeChild(toRemove);
        }
    }, 500);
}
function popup(url, postData = null, bootstrapCssWidth = 'modal-lg') {
    // fermeture de tous les popovers
    $('[data-toggle="popover"]').popover('dispose');

    let currentModal = document.querySelector('#current-modal');
    if (currentModal) {
        // modal deja ouverte
        closeModal(currentModal);
    }

    if (postData instanceof Array) {
        var formData = new FormData();
        for (var i = 0; i < postData.length; i++) {
            if (postData[i].name) {
                formData.append(postData.name, postData.value);
            }
        }
        postData = formData;
    }

    ajaxCall(url, postData, (htmlCode) => {
        let template = `
        <style>
            .ui-autocomplete { z-index:1100 !important;}
        </style>
        <div class="modal fade" style="z-index: 1050;" id="current-modal" tabindex="-1" role="dialog" aria-labelledby="confirmation" aria-hidden="true">
          <div class="modal-dialog-centered modal-dialog __CSS__" role="document">
            <div class="modal-content">
              __MODAL_CONTENT__
            </div>
          </div>
        </div>`;

        let code = template.replace('__MODAL_CONTENT__', htmlCode);
        code = code.replace('__CSS__', bootstrapCssWidth);

        let modal = document.createElement("div");
        let div = document.body.appendChild(modal);
        $(div).html(code);
        initializeJsFunction();

        if (!document.querySelector(".modal-backdrop")) {
            $(div.querySelector('#current-modal')).modal({
                backdrop: 'static'
            });
        } else {
            $(div.querySelector('#current-modal')).modal({
                backdrop: false
            });
        }
        $('#current-modal').on('hidden.bs.modal', function (e) {
            // supprime du DOM
            div.parentNode.removeChild(div);
        });
    });
}
function closeModal(exists) {
    // fermeture de tous les popovers
    $('[data-toggle="popover"]').popover('dispose');

    $("#current-modal").parent('div').remove();
    $('#current-modal').modal('dispose');

    let body = document.getElementsByTagName('body');
    body[0].classList.remove('modal-open');
    body[0].removeAttribute('style');

    if (!exists) {
        // $('#current-modal').modal('dispose');
        $(".modal-backdrop").remove();
    }
}

function setTooltips() {
    document.querySelectorAll('[data-value*="tooltip"]').forEach(elem => {
        $(elem).tooltip();
    });
}
function setLabelRequis(faGliph) {

    var symbol = 'fa-asterisk';
    if (faGliph) {
        symbol = faGliph;
    }

    // on enleve tout pour les remettre
    document.querySelectorAll('label>span[class*="redstar"]').forEach(span => {
        span.parentNode.removeChild(span);
    });

    document.querySelectorAll('[required]').forEach(e => {
        let label = null;
        if (e && e.labels && e.labels.length) {
            label = e.labels[0];
        }
        if (label) {
            if (label.querySelectorAll('span[class*="redstar"]').length == 0) {
                let asterisc = document.createElement('span');
                asterisc.classList.add('redstar');
                asterisc.classList.add('text-danger');
                asterisc.classList.add('pl-1');
                asterisc.classList.add('small');
                asterisc.title = 'Champ obligatoire';
                asterisc.innerHTML = '<i class="fa ' + symbol + '" aria-hidden="true"></i>';
                label.appendChild(asterisc);
            }
        }
    });

    return false;

}
function redirect(url, data) {
    if (!data) {
        document.location.href = url;
        return false;
    }

    var form = document.createElement("form");
    form.name = "RedirectDataForm";
    form.method = "POST";
    form.action = url;

    for (var pair of data.entries()) {
        let hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = pair[0];
        hidden.value = pair[1];
        form.appendChild(hidden);
    }
    document.body.appendChild(form);
    form.submit();
}
function reload() {
    document.location.reload();
}

let initializeJs = throttle(initializeJsFunction, 500);
function initializeJsFunction() {
    formValidationAll();
    setLabelRequis();
    setTooltips();
}

function debounce(func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) {
                func.apply(context, args);
            }
        };

        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) {
            func.apply(context, args);
        }
    };
}
function throttle(func, wait, options) {
    var timeout, context, args, result;
    var previous = 0;
    if (!options)
        options = {};

    var later = function () {
        previous = options.leading === false ? 0 : Date.now();
        timeout = null;
        result = func.apply(context, args);
        if (!timeout)
            context = args = null;
    };

    var throttled = function () {
        var _now = Date.now();
        if (!previous && options.leading === false)
            previous = _now;
        var remaining = wait - (_now - previous);
        context = this;
        args = arguments;
        if (remaining <= 0 || remaining > wait) {
            if (timeout) {
                clearTimeout(timeout);
                timeout = null;
            }
            previous = _now;
            result = func.apply(context, args);
            if (!timeout)
                context = args = null;
        } else if (!timeout && options.trailing !== false) {
            timeout = setTimeout(later, remaining);
        }
        return result;
    };

    throttled.cancel = function () {
        clearTimeout(timeout);
        previous = 0;
        timeout = context = args = null;
    };

    return throttled;
}