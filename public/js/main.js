var sidebarHeight = document.getElementById( 'sidebar' ).clientHeight;
var profileId;
var getRole;
$( document ).ready( function () {

    var Mobile = /Android|webOS|iPhone|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    var iPad = /iPad|iPod/i.test( navigator.userAgent );

    // user navbar toggle
    $("#toggle-user-navbar").on("click",function(){
        $(".user-navbar").toggleClass("show");
        $(".user").toggleClass("active");
    });

    // alert close
    $("#alertClose").on("click",function(){
        $(".custom-alert").fadeOut("slow");
    });
    setTimeout(function(){
        $(".custom-alert").fadeOut("slow");
    }, 10000 );

    if (!iPad||!Mobile){
        if(sidebarHeight < 740) {
            $('footer').css('position','absolute');
        }
    }

    // navbar toggle
    $("#menuToggle").on("click",function() {
        $("#sidebar").toggleClass("ipad");
        if ($("#sidebar").hasClass("ipad")) {
            $( ".account-block" ).addClass( "show" );
        }
        $('body').addClass('overflow-none');
    });

    $("#navbarClose").on("click",function() {
        $("#sidebar").removeClass("ipad");
        $( 'body' ).removeClass( 'overflow-none' );
    } );
    if (iPad) {
        $("#menuToggle").on("click",function() {
            $("#sidebar").toggleClass("ipad");
            if ($("#sidebar").hasClass("ipad")) {
                $( ".account-block" ).addClass( "show" );
            }
            $('body').addClass('overflow-none');
        });

        $("#navbarClose").on("click",function() {
            $("#sidebar").removeClass("ipad");
            $( 'body' ).removeClass( 'overflow-none' );
        } );
    }
    if (Mobile) {
        $( "#menuToggle" ).on( "click", function () {
            $("#sidebar").toggleClass("mobile");
           $('body').addClass('overflow-none');
        } );

        $("#navbarClose").on("click",function() {
            $("#sidebar").removeClass("mobile");
            $('body').removeClass('overflow-none');
        } );
    }

    // field required text change for submit & hover 
    var elements = document.getElementsByTagName( "input" );
    for ( var i = 0; i < elements.length; i++ ) {
        elements[ i ].oninvalid = function ( e ) {
            e.target.setCustomValidity( "" );
            if ( !e.target.validity.valid ) {
                //console.log( e.target.id );
                switch ( e.target.id ) {
                    case 'select':
                        e.target.setCustomValidity( "この項目は必須です。" ); break;
                    case 'textarea':
                        e.target.setCustomValidity( "この項目は必須です。" ); break;
                    default: e.target.setCustomValidity( "この項目は必須です。" ); break;
                }
            }
        };
        elements[ i ].oninput = function ( e ) {
            e.target.setCustomValidity( "" );
        };
    }

    //Text input title remove text
    $('input').attr('title','');

    $('.select2').select2({
        "language": {
            "noResults": function(){
                return "結果が見つかりません";
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    //onkeyup seller profile create get input vlaue
    $('#seller-profile-from-create input').keyup(function() {
        var inputVal = $(this).val();
        profileId = $('.get-seller-id').data('id');
        getRole = $("#seller-profile-from-create").data("id");
        ajaxRealTimeFormDataGet(profileId,inputVal,getRole);
    });

    $('#seller-profile-from-create textarea').keyup(function() {
        var inputVal = $(this).val();
        profileId = $('.get-seller-id').data('id');
        getRole = $("#seller-profile-from-create").data("id");
        ajaxRealTimeFormDataGet(profileId,inputVal,getRole);
    });

    //onkeyup seller profile edit get input vlaue
    $('#seller-profile-from-edit input').keyup(function() {
        var inputVal = $(this).val();
        profileId = $('.get-seller-id').data('id');
        getRole = $("#seller-profile-from-edit").data("id");
        ajaxRealTimeFormDataGet(profileId,inputVal,getRole);
    });

    $('#seller-profile-from-edit textarea').keyup(function() {
        var inputVal = $(this).val();
        profileId = $('.get-seller-id').data('id');
        getRole = $("#seller-profile-from-edit").data("id");
        ajaxRealTimeFormDataGet(profileId,inputVal,getRole);
    });

    $('#comment-form-create textarea').keyup(function() {
        var inputVal = $(this).val();
        profileId = $('.get-seller-id').data('id');
        getRole = $("#comment-form-create").data("id");
        ajaxRealTimeFormDataGet(profileId,inputVal,getRole,'is_comment');
    });

    $('#comment-form-update textarea').keyup(function() {
        var inputVal = $(this).val();
        profileId = $('.get-seller-id').data('id');
        getRole = $("#comment-form-update").data("id");
        ajaxRealTimeFormDataGet(profileId,inputVal,getRole,'is_comment');
    });

    //3 sec after check type form type user
    setInterval(function () {
        profileId = $('.get-seller-id').data('id');
        getTypeCheck();
    }, 3000);

    $(document.body).on("change focusout",'#invoice-calculate .quantity-in,#invoice-calculate .price-in,#invoice-calculate .tax-in',function(){
        var row = $(this).parents("#invoice-calculate .multi-item-header.data-row");
        calculatePrice(row);
    });
    
});


function ajaxRealTimeFormDataGet(profileId,inputVal,getRole,getComment){
    if(inputVal != ''){
        var is_type = 1;
    }else {
        var is_type = 0;
    }
    console.log(inputVal,is_type);
    // Fire off the request to /form.php
    var request = $.ajax({
        url: SITEURL + "/get/form/data?seller_id="+ profileId + '&is_type='+is_type+ '&type_role='+getRole+ '&is_comment='+getComment,
        type: 'GET',
        processData: false,
        contentType: false,
        cache: false,
        data: ''
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        $('#get_seller_id_for_type').val(response.seller_id);
        profileId = response.seller_id;
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // Log the error to the console
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
}

function getTypeCheck(profileId){
    var request = $.ajax({
        url: SITEURL + "/get/type/check?seller_id="+ profileId,
        type: 'GET',
        processData: false,
        contentType: false,
        cache: false,
        data: ''
    });
    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        $.map(response.data, function( val, i ) {
            if((val.is_type == 1) && (val.type_role == 'operator')){
                $('.profile-edit-time-'+val.id).removeClass('d-none');
                //$("#seller-profile-left-menu-"+val.id).removeAttr("href");
                var href = $("#seller-profile-left-menu-"+val.id).attr('href');
                $("#seller-profile-left-menu-"+val.id).attr( { 'data-toggle':"modal", 'data-target':"#profile-modal-id",'onclick':'getSellerIdByClick('+val.id+',\'' + href + '\')' } );
            }
            else {
                $('.profile-edit-time-'+val.id).addClass('d-none');
                $("#seller-profile-left-menu-"+val.id).removeAttr("data-target data-toggle onclick");
                
            }
            if((val.is_comment_type == 1) && (val.comment_type_role == 'operator')) {
                $('.comment-type-operator-'+val.id).removeClass('d-none');

                var href = $("#comment-type-seller-left-menu-"+val.id).attr("href");

                $('#confrim-button-for-type').attr('onclick', 'profileValueRemoveComment('+val.id+',\'' + href + '\')');
                
                $("#comment-type-seller-left-menu-"+val.id).attr( { 'data-toggle':"modal", 'data-target':"#profile-modal-id",'onclick':'getSellerIdByClick('+val.id+',\'' + href + '\')' } );

            }else {
                $('.comment-type-operator-'+val.id).addClass('d-none');
                $("#comment-type-seller-left-menu-"+val.id).removeAttr("data-target data-toggle onclick");
            }

            if((val.is_type == 1) && (val.type_role == 'seller')){
                $('#seller-list-type-user-'+val.id).removeClass('d-none');
                var href = $("#operator-type-profile-list-"+val.id).attr('href');

                $("#operator-type-profile-list-"+val.id).attr( { 'data-toggle':"modal", 'data-target':"#profile-modal-id",'onclick':'getSellerIdByClick('+val.id+',\'' + href + '\')' } );
            }
            else {
                $('#seller-list-type-user-'+val.id).addClass('d-none');
                $("#operator-type-profile-list-"+val.id).removeAttr("data-target data-toggle onclick");
            }

            if((val.is_comment_type == 1) && (val.comment_type_role == 'seller')){
                $('#comment-block-id-'+val.id).removeClass('d-none');

                //$("#operator-type-comment-list-"+val.id).removeAttr("href");
                var href = $("#operator-type-comment-list-"+val.id).attr('href');
                $("#operator-type-comment-list-"+val.id).attr( { 'data-toggle':"modal", 'data-target':"#profile-modal-id",'onclick':'getCommentSellerIdByClick('+val.id+',\'' + href + '\')' } );
            }
            else {
                $('#comment-block-id-'+val.id).addClass('d-none');
                $("#operator-type-comment-list-"+val.id).removeAttr("data-target data-toggle onclick");
            }
          });
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // Log the error to the console
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
}

function getSellerIdByClick(id,href){
    $('#confrim-button-for-type').attr('onclick', 'profileValueRemove('+id+',\'' + href + '\')');
}

function profileValueRemove(id,url){
    $('.seller-profile-from-reset-'+ id + ' input').val('');
    $("#profile-modal-id").modal("hide");
    ajaxRealTimeFormDataGet(id,0);
    window.location.href = url;
}

function getCommentSellerIdByClick(id,url){
    $('#confrim-button-for-type').attr('onclick', 'profileValueRemoveComment('+id+',\'' + url + '\')');
}

function profileValueRemoveComment(id,url){
    $('.seller-profile-from-reset-'+ id + ' input').val('');
    $("#profile-modal-id").modal("hide");
    ajaxRealTimeFormDataGet(id,0,false,'is_comment');
    window.location.href = url;
}

// Password Validation for onkeyup
function passwordValidation( password ) {
    $( ".error_msg" ).css( "display", "none" );
    var defaultSpan = $( password ).siblings( '.invalid-feedback' )[ 1 ];
    if ( defaultSpan ) {
        defaultSpan.innerHTML = "";
    }
    var span = $( password ).siblings( '.invalid-feedback' )[ 0 ];
    var result = '';
    if ( password.value.length < 8 ) {
        result += '<strong>パスワードは8文字以上である必要があります。</strong><br>'
    }
    if ( !password.value.match( '[a-z]' ) ) {
        result += '<strong>小文字を含める必要があります。</strong><br>'
    }
    if ( !password.value.match( '[A-Z]' ) ) {
        result += '<strong>大文字を含める必要があります。</strong><br>'
    }
    if ( !password.value.match( '[0-9]' ) ) {
        result += '<strong>数字を含める必要があります。</strong><br>'
    }
    if ( result.length > 1 ) {
        $( span ).css( 'display', 'block' );
        span.innerHTML = result;
    }
    else {
        span.innerHTML = "";
    }
    if ( password.value.length == 0 ) {
        span.innerHTML = "";
        $( ".error_msg" ).css( "display", "none" );
    }
}

// Language var start
var invalid_date = '日付が正しくありません。';
var no_data_found = 'データが見つかりませんでした。';
var invalid_number_max = '100 数で入力してください。';
// Language var end

function ajaxCreate(formId) {
    var formData = new FormData($('#' + formId)[0]);
    disableEnableForm(formId, true);
    // Fire off the request to /form.php
    var request = $.ajax({
        url: $('#' + formId).attr('action'),
        type: $('#' + formId).attr('method'),
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        cache: false,
        data: formData
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        // Log a message to the console
        if (response.success == false) {
            appendErrorMessage(formId, response.data);
        }else{
            window.location.href = response.redirects;
        }
    });

    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // Log the error to the console
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });

    // Callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        disableEnableForm(formId,false);
    });

}

function message(formId, data) {
    console.log(data);
    var html = '<div class="custom-alert ' + data.rs_class + '"><span class="rito rito-check"></span><p>' + data.message + '</p><span class="rito rito-x" id="alertClose"></span></div>';
    $(".ajax-response-" + formId).html(html);
    setTimeout(function () {
        $(".ajax-response-" + formId).html("");
    }, 5000);
}

function checkPageURL(id, url) {
    var fieldVal = $("#" + id).val();
    if (fieldVal !== "") {
        fieldVal = fieldVal.trim().replace(/\s\s+/g, ' ');
        fieldVal = fieldVal.toLowerCase().split(" ").join("-");
        $("#" + id).val(fieldVal);
        var formId = $("#" + id).parents('form').attr('id');
        var el = $('#' + formId);
        $.post(url, { 'url_map': fieldVal, '_token': el.find('input[name=_token]').val() }, function (response) {
            if (response.success == true) {
                $("#" + id).val(response.message);
                $("#" + formId).find(".error_msg.url_map").html('');
            }
            response.message = response.splash_message;
            message(formId, response);
        });
    }
}


function ajaxUpdate(formId) {
    var formData = new FormData($('#' + formId)[0]);
    disableEnableForm(formId, true);
    // Fire off the request to /form.php
    var request = $.ajax({
        url: $('#' + formId).attr('action'),
        type: $('#' + formId).attr('method'),
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        cache: false,
        data: formData
    });

    // Callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        // Log a message to the console
        if (response.success == false) {
            appendErrorMessage(formId, response.data);
        }else if (formId== 'form-media-update') {
            window.location.href = response.redirects;
        }else if (formId=='form-page-update'){
            window.location.href = response.redirects;
        }else if (formId=='form-directory-update'){
            window.location.href = response.redirects;
        }
    }); 
    // Callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // Log the error to the console
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });

    // Callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        disableEnableForm(formId,false);
    });

}

function appendErrorMessage(formId, errors) {
    var messages = errors;
    for (var key in messages) {
        if (messages.hasOwnProperty(key)) {
            $("#" + formId).find(".error_msg." + key).html(messages[key][0]);
            $("#" + formId).find(".formate-error." + key).html(messages[key][0]);
        }
    }
}

function disableEnableForm(formId, disable) {
    if (disable == true) {
        $('#ajax-loader').show();
        $("#" + formId).find("#btn-form-submit").attr('disabled', 'disabled');
        clearFormError(formId);
    } else {
        $('#ajax-loader').hide();
        $("#" + formId).find("#btn-form-submit").removeAttr('disabled');
    }
}

function clearFormError(formId) {
    $("#" + formId).find(".error_msg").html('');
    $("#" + formId).find(".formate-error").html('');
}

function calculatePrice(el){
    var taxRate = Number(el.find('.tax-in').val());
    var qty = Number(el.find('.quantity-in').val());
    var unitPrice =  Number(el.find('.price-in').val());
    if(qty != NaN && unitPrice != NaN){
        var total = unitPrice*(1+taxRate)*qty;
        el.find('.price-last-in').val(Math.round(total));
    }else{
        el.find('.price-last-in').val('');
    }
}