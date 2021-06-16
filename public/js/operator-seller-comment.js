$( document ).ready(function() {
    $('#ajax-loader').hide();
    $("#comment-form-create").on("submit",function(e){
        e.preventDefault();
        ajaxCreate('comment-form-create');
    });
    $("#comment-form-update").on("submit",function(e){
        e.preventDefault();
        ajaxUpdate('comment-form-update');
    });
});