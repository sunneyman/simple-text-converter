jQuery('.delete-post-button').click(function(event) {

    var postId = event.target.attributes['data-name'].value;

    console.log(event.target.parentNode);

    var elemHolder = event.target.parentNode;

    var confirm = prompt('You are about to delete this post! Click OK to confirm.', "Delete");

    console.log(confirm);

    var data = {
        action: 'delete_post',
        postId
    };

    if(confirm === 'Delete') {

        jQuery.ajax({
            method: 'POST',
            url: delBtnAjax.url,
            data: data,
            beforeSend: function() {
                elemHolder.classList.add( 'js-loading' );
                console.log('hi!');
            },
            complete: function (response) {
                console.log(response);
                window.location.reload(true);
                alert('Your post have been successfully deleted!');
            },
        });
    }
});
