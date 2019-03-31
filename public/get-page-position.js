(function(factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports !== 'undefined') {
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }

}(function($) {


    checkCookie();

    var scroll;

    jQuery(window).scroll(()=>{
        if (scroll) {clearTimeout(scroll)}
        scroll = setTimeout(()=>{
            var top = jQuery(window).scrollTop();
            var path = window.location.pathname;
            var cookieName = path.replace(/\//g, "");
            setCookie(cookieName, top, 365);
        }, 500);
    });

    function setCookie(cname,cvalue,exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function checkCookie() {
        var path = window.location.pathname;
        var cookieName = path.replace(/\//g, "");
        var position=getCookie(cookieName);
        if (position != "") {
            jQuery( window ).on( "load", function() {
                jQuery(window).scrollTop(position);
            });
        }
    }

}));
