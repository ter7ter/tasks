(function () {
    'use strict';



});

const BACKEND_URL = "http://ter766ter2.temp.swtest.ru/counter.php";

const LOGGING = true;

function log(message){
    if (LOGGING){
        console.log(message);
    }
}

//Проверяет было ли уже посещение страницы
function checkVisitCookie() {
    let cookies = document.cookie.split("; ");
    let visitName = "visit" + window.location.href;
    for (const cookie of cookies) {
        const [cookieName, ...data] = cookie.split('=');
        if (cookieName === encodeURIComponent(visitName)) {
            return true;
        }
    }
    return false;
}

//Ставит cookie о посещении
function setVisitCookie() {
    let visitName = "visit" + window.location.href;

    const date = new Date();
    date.setTime(date.getTime() + 60 * 60 * 1000);
    document.cookie = encodeURIComponent(visitName) + '=1;expires=' + date.toUTCString() + ';';
}

//Получает город и страну пользователя
function getGeoData() {
    return fetch('https://ipinfo.io/json')
        .then(res => res.json())
        .then(data => {
            return {
                city: data.city,
                country: data.country,
            };
        })
        .catch(err => {
            log(`Error getting location info`, err.message );
            return {
                city: null,
                country: null,
            };
        });
}

//Уникальный ли посетитель
if (!checkVisitCookie()) {

    //Отправляем данные о посетителе
    getGeoData().then(
        function (geoInfo){
            let visitorData = {
                url: window.location.href,
                city: geoInfo.city,
                country: geoInfo.country,
            };
            log(visitorData);
            return fetch(BACKEND_URL,
            {
                    method:'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body:new URLSearchParams(visitorData).toString()
                })
                .then(res => res.json())
                .then(result => {
                    log('Server response:', result)
                    //Ставим cookie посещения
                    setVisitCookie();
                })
                .catch(err => log('Send failed:', err));
        }
    );
}
