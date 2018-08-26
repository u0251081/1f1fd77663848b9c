/**
 * this function is use for send ajax to 17mai class
 * if use not belong 17mai page it will be fault
 * @param G: Class name
 * @param U: class Method
 * @param GET: parameter of GET
 * @param POST: parameter of POST
 * @param callBack: callBack for receive data
 * @param debug: use for debug
 */
function ajax17mai(G = '', U = '', GET = {}, POST = {}, callBack = defaultAjaxCallBack, debug = false) {
    if (debug) {
        console.log('G ' + G);
        console.log('U ' + U);
        console.log('GET ' + GET);
        console.log('POST ' + POST);
    }
    let baseURL = '../newAjaxOP.php';
    let url = baseURL + '?' + objToStr(GET);
    if (typeof POST.G === 'undefined') POST.G = G;
    if (typeof POST.U === 'undefined') POST.U = U;
    $.ajax({
        url: url,
        method: 'post',
        data: POST,
        success: callBack
    });

    function objToStr(obj) {
        let result = '';
        if (typeof obj === 'object') {
            let vararr = [];
            for (let i in obj) {
                vararr.push(i + '=' + obj[i]);
            }
            result = vararr.join('&');
        }
        return result;
    }
}

function ajax17maiFile(G = '', U = '', GET = {}, extra = {}, source = document.createElement('form'), callBack = defaultAjaxCallBack, debug = false) {
    let formData = new FormData(source);
    formData.append('G', G);
    formData.append('U', U);
    $.each(extra, function (ind, ele) {
        formData.append(ind, ele);
    });
    if (debug) {
        console.log('G ' + G);
        console.log('U ' + U);
        console.log('extra ' + extra);
        console.log('Form ' + formData);
    }
    let baseURL = '../newAjaxOP.php';
    let url = baseURL + '?' + objToStr(GET);
    $.ajax({
        url: url,
        method: 'post',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: callBack
    });

    function objToStr(obj) {
        let result = '';
        if (typeof obj === 'object') {
            let vararr = [];
            for (let i in obj) {
                vararr.push(i + '=' + obj[i]);
            }
            result = vararr.join('&');
        }
        return result;
    }
}

function defaultAjaxCallBack(response) {
    let debug = false;
    try {
        let data = JSON.parse(response);
        if (typeof data.javascript !== 'undefined') eval(data.javascript);
        if (debug) console.log(data);
    } catch (e) {
        if (debug) console.log(response);
    }
}

function getFormData(form) {
    if (typeof form === 'string') form = $(form);
    let unindexed_array = form.serializeArray();
    let indexed_array = {};
    $.map(unindexed_array, function (n, i) {
        let inputName = n['name'];
        let dataName = n['name'].replace('[]', '');
        if (inputName.indexOf('[]') === -1)
            indexed_array[n['name']] = n['value'];
        else {
            if (typeof indexed_array[dataName] !== 'object') indexed_array[dataName] = [];
            indexed_array[dataName].push(n['value']);
        }
    });
    return indexed_array;
}