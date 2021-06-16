var getMaxMinData = $("#maximum-minimum").val();
var result = getMaxMinData.split('/');

var maximum = result[0];
var minimum = result[1];
if(typeof result[0] === 'undefined'){
    maximum = '';
}
if(typeof result[1] === 'undefined'){
    minimum = '';
}

var getSizeData = $("#size-section").val();
var splitResult = getSizeData.split('/');

var vertical = splitResult[0];
var side = splitResult[1];
var height = splitResult[2];
var weight = splitResult[3];

if(typeof splitResult[0] === 'undefined'){
    vertical = '';
}
if(typeof splitResult[1] === 'undefined'){
    side = '';
}
if(typeof splitResult[2] === 'undefined'){
    height = '';
}
if(typeof splitResult[3] === 'undefined'){
    weight = '';
}

$(document).ready(function() {
    $(".detail-with-checkbox-btn").click(function(){
        $(".detail-with-checkbox").slideToggle(700, function() {
            $('.detail-with-checkbox-btn').toggleClass('rotate', $(this).is(':visible'));
        });
    });

    $(".detail-info-btn").click(function(){
        $(".detail-info").slideToggle(700, function() {
            $('.detail-info-btn').toggleClass('rotate', $(this).is(':visible'));
            if ($(this).is(':visible')) {
                $('.form-submission').removeClass('no-border');
            } else {
                $('.form-submission').addClass('no-border');
            }
        });
    });
    if($('#islandValue').val()) {
        $('#users').removeAttr('disabled', 'disabled');
    }

    pricePercentage();

    //only one preservation checkbox for prodcut edit page
    $('.preservation-method').on('change', function() {
        $('.preservation-method').not(this).prop('checked', false);
    });
    $('#startDate').datepicker({
        uiLibrary: 'bootstrap4',
        locale: 'ja-jp',
        iconsLibrary: 'fontawesome'
    });

    //maximum and minimum input set
    $("#maximum-minimum").attr('type','hidden');
    $(".maximum-minimum-input").html('<span class="maximum-span">最⼤</span><input class="product-name max-min maximum" autocomplete="off" name="maximum" type="text" value="'+ maximum +'" title=""> <span class="maximum-span">最⼩</span> <input class="product-name max-min minimum" autocomplete="off" value="'+ minimum +'" name="minimum" type="text" title="">');

    //maximum and minimum input set
    $("#size-section").attr('type','hidden');
    $(".size-section-input").html('<span class="common-hight-side-span"> 縦</span><input class="product-name common-hight vertical" autocomplete="off" name="vertical" type="text" value="'+ vertical +'" title=""> <span class="common-hight-side-span"> 横</span> <input class="product-name common-hight side" autocomplete="off" value="'+ side +'" name="side" type="text" title=""> <span class="common-hight-side-span"> 高さ</span> <input class="product-name common-hight height" autocomplete="off" value="'+ height +'" name="height" type="text" title=""> <span class="common-hight-side-span"> 重量 </span> <input class="product-name common-hight weight" autocomplete="off" value="'+ weight +'" name="weight" type="text" title="">');

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
});

function setByMaxMinInputData(){
    var fainalDate = maximum+'/'+ minimum;
    $('#maximum-minimum').val(fainalDate);
}

function setBySizeData(){
    var fainalResult = vertical+'/'+ side+'/'+height+'/'+ weight;
    $('#size-section').val(fainalResult);
}

function islandSelect() {
    var islandId = $('#islandValue').val();
    $.ajax({
        type: "GET",
        url: SITEURL + "/island/users/" + islandId,
        success: function (data) {
            if(data.length > 0) {
                var dataOptions;
                $('#users').removeAttr('disabled', 'disabled');
                data.map(function(val) {
                    return dataOptions += '<option value="' + val.id + '">' + val.name + '</option>';
                });
                $('#users').html(dataOptions);
                $('.btn-submit').prop("disabled", false);
            } else {
                $('#users').html('<option selected="selected" value="">選択してください</option>');
                $('#users').attr('disabled', 'disabled');
                $('.btn-submit').prop("disabled", true);
            }
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}

function pricePercentage() {
    var price = Number($('.price').val());
    var tax = Number($('input[name=tax]:checked').val());
    var total = (price + ((tax/100) * price)).toFixed(2);
    $('#total-price').html(total);
    $('input[name=sell_price]').val(total);
}

// image upload and preview js
function imageUpload(e) {
    var imgPath = e.value;
    var ext = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
    if (ext == "gif" || ext == "png" || ext == "jpg" || ext == "jpeg") {
        readURL(e, e.id);
        $('.' + e.id + 'error').hide()
        $('.btn-submit').prop("disabled", false);
    } else {
        $('.' + e.id + 'error').html('画像ファイルjpg、jpeg、pngを選択').show();
        $('.btn-submit').prop("disabled", true);
    }
}
var imageName;
function readURL(input, id) {
    if (input.files && input.files[0]) {
        imageName = input.files[0].name;
        var reader = new FileReader();
        reader.readAsDataURL(input.files[0]);
        reader.onload = function (e) {
            $('#' + id + 'Preview').attr('src', e.target.result).show();
            $('#' + id + 'Delete').css('display', 'flex');
            $('#' + id + 'Name').html(imageName);
            if(id === 'coverImage') {
                $('#coverImageVal').val(imageName);
            }
        };
    }
}

function removeImage(id, noPreview,imageId) {
    $("#" + id).val(null);
    if(noPreview) {
        $('#' + id + 'Preview').attr('src', noImage).hide();
    } else {
        $('#' + id + 'Preview').attr('src', noImage);
    }
    if(id === 'coverImage') {
        $('#coverImageVal').val('');
        $('.btn-submit').prop("disabled", true);
        $('.' + id + 'error').html('この項目は必須です。').show();
    }
    $('#' + id + 'Delete').hide();
    $('#' + id + 'Name').html('選択されていません');
    /**Create delete string */
    if(imageId=='' || typeof imageId == 'undefined'){
        return false;
    }
    var deletedImages = $("#deleted-images").val();
    var newDeleteImageArray = [];
    if(deletedImages == ""){
        newDeleteImageArray.push(imageId);
    }else{
        var deletedImageArray = deletedImages.split(',');
        deletedImageArray.push(imageId);
        newDeleteImageArray= deletedImageArray;
    }
    var uniqueImageName = [];
    $.each(newDeleteImageArray, function(i, el){
        if($.inArray(el, uniqueImageName) === -1){
            uniqueImageName.push(el);
        } 
    });
    var newImageIdList = uniqueImageName.toString();
    $("#deleted-images").val(newImageIdList);
}

// Youtube valid URL check
function validateYouTubeUrl() {
    var url = $('#youtubeUrl').val();
    if (url != undefined || url != '') {
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|\&v=|\?v=)([^#\&\?]*).*/;
        var match = url.match( regExp );
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