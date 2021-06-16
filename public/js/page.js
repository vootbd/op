var _URL = window.URL || window.webkitURL;
var directory_id = 2;
var page_edit = false;
var page_id = 0;
$(document).ready(function () {
    $('#ajax-loader').hide();
    $('#publishing_status').removeClass('fa-rotate-270');
    // $(".area-block").hide();
    $('#directory-section').hide();
    $("#area-1").show();

    $('.url-map-edit').hide();
    $(document.body).on("click", ".btn-url-map", function () {
        $('.url-map-edit').fadeIn();
        $('.btn-check-url').removeClass('d-none');
        $(this).fadeOut();
        $('.page-url').fadeOut();
    }); 

    /*************************************
    /Publication End Date js start
    *************************************/     
    $("#publishing-end-date").hide(); 
    $(document.body).on("click", "#set", function () { 
        $("#set").hide();
        $("#publishing-end-date").fadeIn();        
    });
 
    /*************************************
    /Datepicker js start
    *************************************/
    var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
    $('#startDate').datepicker({
        format: 'yyyy/mm/dd',
        uiLibrary: 'bootstrap4',
        locale: 'ja-jp',
        iconsLibrary: 'fontawesome',
        icons: {
            rightIcon: '<i class="fa fa-calendar-alt"></i>'
        },
        // minDate: today,
        maxDate: function () {
            return $('#endDate').val();
        }
    });
    $('#endDate').datepicker({
        format: 'yyyy/mm/dd',
        uiLibrary: 'bootstrap4',
        locale: 'ja-jp',
        iconsLibrary: 'fontawesome',
        icons: {
            rightIcon: '<i class="fa fa-calendar-alt"></i>'
        },
        minDate: today,
        minDate: function () {
            return $('#startDate').val();
        }
    });
    /*************************************
    /Datepicker js end
    *************************************/

    $(".accordion-arrow-button").click(function () {
        $(this).toggleClass("fa-rotate-90");
    });

    $(".accordion-arrow-button-two").click(function () {
        $(this).toggleClass("fa-rotate-90");
    });

    $('.accordion-arrow').toggleClass("fa-rotate-90");
    $('.inner-ul').toggle();
    $('input[type=checkbox]').click(function () {
        var target = this;
        var categoryId = $(target).attr("id").split('gridCheck')[1];
        if ($(target).hasClass('parent')) {
            var childCheckboxes = $('#inner-ul-' + categoryId).find('input[type=checkbox]')
            $.each(childCheckboxes, function (i, val) {
                $(val).prop('checked', target.checked)
            })
        }
        else {
            var categoryId = $(target).parents('ul').attr('id').split('inner-ul-')[0];
            $('#gridCheck' + categoryId).prop('checked', target.checked)
        }
    });
    /*************************************
    /Directory faq fade in/out start 
    *************************************/
    $(document.body).on("click", "#change-dir", function () {
        $(this).hide();
        $('#directory-section').show();
    });

    $(".dir-radio").on("click", function (e) {
        let dirName = $(this).parent().find(".checked-dir-name").text();
        $(".directory-label").text(dirName);
        $(".directory-label-url").text(PAGE_URL_STATIC+dirName);
        $("#directory-section").hide();
        $("#change-dir").show();
    });
    /*************************************
    /Submit Form
    *************************************/
    $("#form-page-create").on("submit", function (e) {
        e.preventDefault();
        ajaxCreate('form-page-create');
    });
    $("#form-page-update").on("submit", function (e) {
        e.preventDefault();
        ajaxUpdate('form-page-update');
    });
    $(document.body).on("click", ".dir-radio", function () {
        directory_id = $(this).val();
        var directory = $(this).parent().find('.checked-dir-name').text();
        if (directory != '/') {
            directory = "/" + directory + "/";
        }
        $(".directory-label").text(directory);
        $(".directory-label-url").text(PAGE_URL_STATIC+directory);
    });

    /**
     * Validate page url by clicking on button
     */

    $(document.body).on("click", "#btn-check-url", function () {        
        var action = $("#page_url_map").data('type');
        var url = $("#page_url_map").data('url')+'?directory_id='+directory_id+'&page_edit='+page_edit+'&page_id='+page_id;
        if (action == 'update') {
            var copyField = $("#url_map_copy").val();
            if (copyField == $("#page_url_map").val()) {
                return false;
            }
        }
        checkPageURL("page_url_map", url);
    });
});
