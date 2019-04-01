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
    var userId = delBtnAjax.userId;
    // jQuery(window).load(()=>addDeleteButton(userId));

    var clicker = jQuery('.delete-post-button>i');

    clicker.removeClass('invisible'); // if plugin is not available, trash icon will not be displayed

    jQuery('.delete-post-button').click(function(event) {

        var postId = event.target.attributes['data-name'].value;
        var nonce = event.target.attributes['data-nonce'].value;
        var elemHolder = event.target.parentNode;


        this.confirmMessage = document.createElement('div');
        this.confirmMessage.classList.add('confirm-message');
        this.confirmMessage.innerHTML="Are you sure? This may not be undone.";
        document.body.append(this.confirmMessage);

        this.buttonsHolder = document.createElement('div');
        this.buttonsHolder.classList.add('confirm-buttons-holder');
        this.confirmMessage.append(this.buttonsHolder);

        this.confirmButton = document.createElement('div');
        this.confirmButton.classList.add('confirm-button');
        this.confirmButton.innerHTML="Sure!";
        this.buttonsHolder.append(this.confirmButton);

        this.cancelButton = document.createElement('div');
        this.cancelButton.classList.add('cancel-button');
        this.cancelButton.innerHTML="No, cancel.";
        this.buttonsHolder.append(this.cancelButton);

        this.cancelButton.addEventListener('click',(e)=>{
            e.target.parentNode.parentNode.remove();
        });


        const confirmDelete = function(elemHolder) {

            this.confirmMessage = document.createElement('div');
            this.confirmMessage.classList.add('after-delete-message');
            this.confirmMessage.innerHTML="Your document has been deleted.";
            document.body.append(this.confirmMessage);

            this.buttonsHolder = document.createElement('div');
            this.buttonsHolder.classList.add('confirm-buttons-holder');
            this.confirmMessage.append(this.buttonsHolder);

            this.continueButton = document.createElement('div');
            this.continueButton.classList.add('cancel-button');
            this.continueButton.innerHTML="Press to continue";
            this.buttonsHolder.append(this.continueButton);

            elemHolder.parentNode.parentNode.parentNode.remove();

            this.continueButton.addEventListener('click', (e)=>{
                window.location.reload(true);
            });
        };





        const deletePost = function(e){

            e.target.parentNode.parentNode.remove();

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


    function addDeleteButton(userId) {
        var data = {
            action: 'custom_find_posts',
            id: userId
        };
        jQuery.ajax({
            method: 'GET',
            url: delBtnAjax.url,
            data: data,
            complete: function (response) {
                var respObj = JSON.parse(response.responseText);
                setTrashButtons(respObj);
            },
        });
    }

    function setTrashButtons(respObj){
        // console.log(respObj);
        var postsOnPage = jQuery('.entity-document');

        for(var i=0;i<postsOnPage.length; i++){
            var postLink = postsOnPage[i].querySelector('a').href;
            var postId = postLink.split("/");
            if(respObj['user_role']==="administrator" || respObj['user_role']==="editor" || respObj['users_posts'].includes(postId[5])) {
                var btnHolder = document.createElement('span');
                jQuery(btnHolder).addClass('delete-post-button-js');
                postsOnPage[i].append(btnHolder);

                var trashBtn = document.createElement('i');
                jQuery(trashBtn).addClass('fas');
                jQuery(trashBtn).addClass('fa-trash-alt');
                jQuery(trashBtn).attr("data-name",postId[5].split("-")[0]);
                btnHolder.append(trashBtn);

                getNonce(postId,trashBtn);
            }
        }
    }

    function getNonce(postId, element) {
        var data = {
            action: 'custom_get_nonce',
            id: postId
        };
        jQuery.ajax({
            method: 'GET',
            url: delBtnAjax.url,
            data: data,
            complete: function (response) {
                console.log(response.responseText);
                jQuery(element).attr("data-nonce",response.responseText);
                addClicker(element);
            },
        });
    }

    function addClicker(element) {

        jQuery(element).click(function(event) {

            var postId = event.target.attributes['data-name'].value;
            var nonce = event.target.attributes['data-nonce'].value;
            var elemHolder = event.target.parentNode;

            const deletePost = function(e){

                var data = {
                    action: 'custom_delete_post',
                    nonce: nonce,
                    id: postId
                };

                console.log(data);

                jQuery.ajax({
                    method: 'POST',
                    url: delBtnAjax.url,
                    data: data,
                    beforeSend: function() {
                        elemHolder.classList.add( 'js-loading' );
                        elemHolder.parentNode.parentNode.classList.add( 'semi-opaque' );
                    },
                    complete: function (response) {
                        console.log(response);
                        // window.location.reload(true);
                    },
                });
                return false;
            };
            deletePost();
        });
    }
}));
