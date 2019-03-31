checkCookie();

var scroll, currentPosition;

jQuery(window).scroll(()=>{
    if (scroll) {clearTimeout(scroll)}
    scroll = setTimeout(()=>{
        let top = jQuery(window).scrollTop();
        let path = window.location.pathname;
        let cookieName = path.replace(/\//g, "");
        console.log('Top:', top, ', cookieName:', cookieName);
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
    let path = window.location.pathname;
    let cookieName = path.replace(/\//g, "");
    let position=getCookie(cookieName);
    if (position != "") {
        console.log("Last position was " + position, ', cookieName: ', cookieName);
        jQuery( window ).on( "load", function() {
            jQuery(window).scrollTop(position);
        });
    }
}
