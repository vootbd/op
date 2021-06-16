var _URL = window.URL || window.webkitURL;
$(document).ready(function () {
    /*************************************
    /Submit Form
    *************************************/
    $('#ajax-loader').hide();
    $('.reset-btn').on("click", function (e){
        e.preventDefault();
        document.getElementById("alt_text").value = "";
    });
    $("#form-media-update").on("submit", function (e) {
        e.preventDefault();
        ajaxUpdate('form-media-update');
    });
});
