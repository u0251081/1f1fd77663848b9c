function SetCookie(name = '', value = '', expires = '', domain = '', path = '', secure = false) {
    if (typeof name !== 'string') return false;
    if (typeof value !== 'string' && typeof value !== 'number') return false;
    if (typeof expires !== 'string' && typeof expires !== 'number') return false;
    if (typeof domain !== 'string') return false;
    if (typeof path !== 'string') return false;
    if (typeof secure !== 'boolean') return false;
    let para = new Array();
    para.push(name + ' = ' + value);
    if (typeof expires === 'string') expires = Number(expires);
    if (!isNaN(expires) && expires !== 0) {
        let dateObj = new Date();
        dateObj.setTime(dateObj.getTime() + (expires * 24 * 60 * 60 * 1000));
        para.push('expires = ' + dateObj.toGMTString());
    }
    if (typeof domain === 'string' && domain.length > 0) para.push('domain = ' + domain);
    if (typeof path === 'string' && path.length > 0) para.push('path = ' + path);
    if (typeof secure === 'boolean' && secure !== false) para.push('secure = ' + secure);
    let command = para.join('; ');
    document.cookie = command;
}

function ListCookie() {
    let firstArray = document.cookie.split('; ');
    let resultArray = {};
    firstArray.forEach(function (element, index) {
        let tmp = element.split('=');
        resultArray[tmp[0]] = tmp[1];
    });
    return resultArray;
}

function GetCookie(name = '') {
    if (typeof name === 'string' && name.length > 0) {
        let AllCookie = ListCookie();
        if (typeof AllCookie[name] === 'undefined') return false;
        else return AllCookie[name];
    } else {
        return false;
    }
}

function DelCookie(name = '') {
    if (typeof name === 'string' && name.length > 0) {
        SetCookie(name,'',-1);
    } else {
        return false;
    }
}

function CleanCookie() {
    let AllCookie = ListCookie();
    for (let i in AllCookie) {
        DelCookie(i);
    }
}