
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var dragDiv = $('#nestable').nestable({
    group: 1,
    maxDepth: 3,
    expandBtnHTML: '',
    collapseBtnHTML: ''
});
$(document).ready(function () {
    $('#ajax-loader').hide();
    var updateOutput = function (e) {
        $(".enable-ordering").attr("disabled", true);
        var list = e.length ? e : $(e.target), output = list.data('output');
        $(".enable-ordering").html('').prepend('<div class="spinner-border spinner-border-sm text-primary"></div>');
        if (window.JSON) {
            $.ajax({
                type: 'POST',
                url: '/directory/order',
                data: { pages: $('#nestable').nestable('serialize') },
                success: function (data) {
                    location.reload();
                }
            });
        } else {
            alert('JSON browser support required for this demo.');
        }
    };
    $('.enable-ordering').on('click', function () {
        $(dragDiv).toggleClass('drag_disabled')
        $(this).toggleClass('inverse')
        $(this).attr("id", "saveOrder");
        $("#text-change").text(function (i, v) {
            return v === 'ページの並び替え' ? '並び替えを保存する' : 'ページの並び替え'
        })
        $("#saveOrder").on('click', updateOutput);
    });
    /**
     * Show hide dir list
     */
    $(document.body).on("click", "#dir-toggle-btn", function () {
        dropDownToggle();
    });
    $(document.body).on("click", ".select-directory", function () {
        selectDirectory($(this));
    });

    /*************************************
    /Submit Form
    *************************************/
    $("#form-directory-create").on("submit", function (e) {
        e.preventDefault();
        ajaxCreate('form-directory-create');
    });

    $("#form-directory-update").on("submit", function (e) {
        e.preventDefault();
        ajaxUpdate('form-directory-update');
    });

    /*************************************
   / Clear form error
   *************************************/
    $(document.body).on("click", 'form input', function () {
        $(this).parent().find(".error_msg").html('');
    });

});

function deleteModalDisplay(current_span) {
    var li_id = $(current_span).parent().data('id')
    $('#delete-modal').modal('show');
    $('#modal-delete-button').attr('onclick', 'removePage(' + li_id + ')');
}
function removePage(li_id) {
    $('.dd-item').filter('[data-id=' + li_id + ']').remove();
    $.ajax({
        type: 'DELETE',
        url: 'directories' + '/' + li_id,
        data: null,
        success: function (data) {
            location.reload();
        }
    });
    $('#delete-modal').modal('hide');
}
function collapseForm(button) {
    var form = $('.form-block')
    if ($(form).css('display') == 'none') {
        $(form).show(500)
    }
    else {
        $(form).hide(500)
    }
}

/* When the user clicks on the button, 
toggle between hiding and showing the dropdown content */
function dropDownToggle() {
    document.getElementById("dir-dropdown").classList.toggle("show");
}


/**
 * 
 * @param {object} el 
 */
function selectDirectory(el) {
    var dirId = el.data('id');
    var dirName = el.data("name");
    $("#dir-toggle-btn").data("id", dirId);
    $("#dir-toggle-btn").text(dirName);
    $("#directory-id").val(dirId);
}

// Close the dropdown if the user clicks outside of it
window.onclick = function (event) {
    if (!event.target.matches('.dir-dropbtn')) {
        var dropdowns = document.getElementsByClassName("dir-dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}
