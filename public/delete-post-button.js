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

        // jQuery('body div:first-child').addClass('half-opacity');

        var screen = document.createElement('div');
        jQuery(screen).addClass('magic-screen');
        document.body.prepend(screen);

        jQuery('body').addClass('no-scroll');


        this.confirmMessage = document.createElement('div');
        this.confirmMessage.classList.add('confirm-message');
        this.confirmMessage.innerHTML="Are you sure? This may not be undone.";
        document.body.append(this.confirmMessage);

        this.buttonsHolder = document.createElement('div');
        this.buttonsHolder.classList.add('confirm-buttons-holder');
        this.confirmMessage.append(this.buttonsHolder);

        this.confirmButton = document.createElement('div');
        this.confirmButton.classList.add('button');
        this.confirmButton.classList.add('button_primary');
        this.confirmButton.classList.add('button_middle');
        this.confirmButton.innerHTML="Sure!";
        this.buttonsHolder.append(this.confirmButton);

        this.cancelButton = document.createElement('div');
        this.cancelButton.classList.add('button');
        this.cancelButton.classList.add('button_primary');
        this.cancelButton.classList.add('button_middle');
        this.cancelButton.innerHTML="No, cancel.";
        this.buttonsHolder.append(this.cancelButton);

        this.cancelButton.addEventListener('click',(e)=>{
            e.target.parentNode.parentNode.remove();
            // jQuery('body div:first-child').removeClass('half-opacity');
            screen.remove();
        });

        const deletePost = function(e){

            e.target.parentNode.parentNode.remove();
            screen.remove();

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
        this.confirmButton.addEventListener('click', deletePost);
    });
}));
