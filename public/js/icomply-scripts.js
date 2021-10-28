document.addEventListener('DOMContentLoaded', function () {
    "use strict";

    var hide_after_closing  = true,
        cookie_notice       = document.querySelectorAll(".simple-cookies"),
        cookie_notice_id    = document.querySelector("#simple-cookies"),
        hide_cookie         = document.querySelector(".sc-closer"),
        cookie_closer       = document.querySelectorAll(".js-close"),
        cookie_timeout_days = 30;

    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)===' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

    function eraseCookie(name) {
        document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }

    function deleteCookie(cookieName){
        if( cookieName.length && getCookie(cookieName) ) eraseCookie(cookieName);
    }

    if(hide_cookie){
        hide_cookie.onclick = function (e) {
            e.preventDefault();
            cookie_notice_id.classList.add("hidden");
        };
    }

    if(cookie_closer){
        for (var c=0; c<cookie_closer.length; c++){
            var closer = cookie_closer[c];

            if( closer ){
                closer.onclick = function (e) {
                    var href = e.target.getAttribute("href");
                    if( typeof href !== typeof undefined){
                        if( href === "#" || href.length < 1){
                            e.preventDefault();
                        }
                    }

                    if(hide_after_closing) {
                        setCookie("icomply-cookie-accepted", "yes", cookie_timeout_days);
                    }

                    cookie_notice_id.classList.add("hidden");
                };
            }
        }
    }

    if( !getCookie("icomply-cookie-accepted") ){
        if( cookie_notice){
            for (var g=0; g<cookie_notice.length; g++){
                var notice = cookie_notice[g];
                notice.classList.add("active");
            }
        }
    }
});