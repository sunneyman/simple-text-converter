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
    // var userId = delBtnAjax.userId;

    var clicker = jQuery('.delete-post-button>i');

    clicker.removeClass('invisible'); // if plugin is not available, trash icon will not be displayed

    jQuery('.delete-post-button').click(function(event) {

        var postId = event.target.attributes['data-name'].value;
        var nonce = event.target.attributes['data-nonce'].value;
        var elemHolder = event.target.parentNode;

        const deletePost = function(e){

            var data = {
                action: 'custom_delete_post',
                nonce: nonce,
                id: postId
            };

            jQuery.ajax({
                method: 'POST',
                url: delBtnAjax.url,
                data: data,
                beforeSend: function() {
                    elemHolder.classList.add( 'js-loading' );
                    elemHolder.parentNode.parentNode.classList.add( 'semi-opaque' );
                },
                complete: function (response) {
                    window.location.reload(true);
                },
            });
            return false;
        };
        deletePost();
    });
}));
