var maximum = '';
var minimum = '';
var vertical = '';
var side = '';
var height = '';
var weight = '';
$( document ).ready( function () {
    $(".detail-with-checkbox-btn").click( function () {
        $(".detail-with-checkbox").slideToggle(700,function () {
            $('.detail-with-checkbox-btn').toggleClass('rotate',$( this ).is( ':visible'));
        });
    });

    $(".detail-info-btn").click( function(){
        $(".detail-info").slideToggle(700, function () {
            $('.detail-info-btn').toggleClass('rotate', $( this ).is( ':visible' ));
            if ( $( this ).is( ':visible' ) ) {
                $('.form-submission').removeClass('no-border');
            } else {
                $('.form-submission').addClass('no-border');
            }
        } );
    } );
    if ($('#islandValue').val() ) {
        $('#users').removeAttr('disabled','disabled');
    }

    $('.preservation-method').on('change', function() {
        $('.preservation-method').not(this).prop('checked', false);
    });

    //onchange set value by maximum & minimum input filed
    $('.maximum, .minimum').on('change', function() {
        maximum = $(".maximum").val();
        minimum = $(".minimum").val();
        setByMaxMinInputData();
    });

    /***
     * onchange set value by vertical,side,hight,weight input filed
     * ***/
    $('.vertical, .side, .height, .weight').on('change', function() {
        vertical = $(".vertical").val();
        side = $(".side").val();
        height = $(".height").val();
        weight = $(".weight").val();
        setBySizeData();
    });

    $('#startDate').datepicker({
        uiLibrary: 'bootstrap4',
        locale: 'ja-jp',
        iconsLibrary: 'fontawesome',
        maxDate: function () {
            return $('#endDate').val();
        }
    });

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

    pricePercentage();

});


function setByMaxMinInputData(){
    var fainalDate = maximum+'/'+ minimum;
    $('#maximum-minimum').val(fainalDate);
}

function setBySizeData(){
    var fainalResult = vertical+'/'+ side+'/'+height+'/'+ weight;
    $('#size-section').val(fainalResult);
}

/***
 * on change island wise seller name and id set 
 * ***/
function islandSelect() {
    var islandId = $('#islandValue').val();
    $.ajax({
        type: "GET",
        url: SITEURL + "/island/users/" + islandId,
        success: function ( data ) {
            if(data.length > 0 ) {
                var dataOptions;
                $('#users').removeAttr('disabled', 'disabled');
                data.map(function (val) {
                    return dataOptions += '<option value="' + val.id + '">' + val.name + '</option>';
                } );
                $('#users').html(dataOptions);
            } else {
                $('#users').html('<option selected="selected" value="">選択してください</option>');
                $('#users').attr('disabled','disabled');
            }
        },
        error: function ( data ) {
            console.log( 'Error:', data );
        }
    } );
}

function pricePercentage() {
    var price = Number($('.price').val());
    var tax = Number($('input[name=tax]:checked').val());
    var total = (price + (( tax / 100 ) * price )).toFixed();
    $('#total-price' ).html( total );
    $( 'input[name=sell_price]' ).val( total );
}

// image upload and preview js
function imageUpload(e) {
    var imgPath = e.value;
    //console.log(imgPath);
    var ext = imgPath.substring(imgPath.lastIndexOf( '.' ) + 1 ).toLowerCase();
    if (ext=="gif" || ext=="png" || ext=="jpg" || ext=="jpeg") {
        readURL(e, e.id );
        $('.'+e.id+'error').hide()
        $('.btn-submit').prop("disabled",false);
    } else {
        $('.'+e.id+'error').html('jpg、jpeg、png タイプの画像ファイルを選択してください。').show();
        $('.btn-submit').prop("disabled",true);
    }
}

var imageName;
function readURL(input,id) {

    //$form.find('input[type="file"]').prop('files', droppedFiles);
    //var path = (window.URL || window.webkitURL).createObjectURL(file);
    //console.log(input.files[0]);
    if ( input.files && input.files[0]) {
        var tmppath = URL.createObjectURL(input.files[0]);
        //console.log(tmppath);
        imageName = input.files[0].name;
        var reader = new FileReader();
        reader.readAsDataURL(input.files[0]);
        //console.log(input.files[0]);
        reader.onload = function(e){
            var imgPath = $('#'+id).val();
            //console.log(imgPath);
            $('#'+id+'Preview').attr('src',e.target.result).show();
            $('#'+id+'Delete').css('display','flex');
            $('#'+id+'Name').html(imageName);
            $("#"+id).attr("value",imageName);
        };
    }
}

function removeImage( id, noPreview ) {
    $("#"+id).val(null);
   if(noPreview){
        $('#'+id+'Preview').attr('src',noImage).hide();
    } else {
        $('#'+id+'Preview').attr('src',noImage);
    }
    $('#'+id+'Name').html('選択されていません');
    $('#'+id+'Delete').css('display','none');
}

// Youtube valid URL check
function validateYouTubeUrl() {
    var url = $('#youtubeUrl').val();
    if (url != undefined || url != '') {
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|\&v=|\?v=)([^#\&\?]*).*/;
        var match = url.match(regExp);
        if (match && match[ 2 ].length == 11) {
            $('.video-url').fadeOut();
            $('.btn-submit').prop("disabled", false);
        } else {
            $('.video-url').fadeIn();
            $('.btn-submit').prop("disabled", true);
        }
    }
    if (url == '') {
        $('.video-url').fadeOut();
        $('.btn-submit').prop("disabled", false );
    }
}