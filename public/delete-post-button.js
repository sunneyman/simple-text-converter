var clicker = jQuery('.delete-post-button>i');

clicker.removeClass('invisible'); // if plugin is not available, trash icon will not be displayed

    clicker.mouseenter((event)=>{
        this.dropdown = document.createElement('div');
        this.dropdown.innerHTML = "Remove Document";
        this.dropdown.classList.add('delete-dropdown');
        event.target.append(this.dropdown);
    });

    clicker.mouseleave((event)=>{
        this.dropdown.remove();
    });

jQuery('.delete-post-button').click(function(event) {

    var postId = event.target.attributes['data-name'].value;
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
        console.log('Delete confirmed!');
        e.target.parentNode.parentNode.remove();

        var data = {
            action: 'delete_post',
            postId
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
                console.log(response);
                confirmDelete(elemHolder);
            },
        });
    };

    this.confirmButton.addEventListener('click', deletePost);

});
