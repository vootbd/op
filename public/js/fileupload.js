
var RITO = null;
jQuery(function () {
    'use strict',
        RITO = {
            url: 'error',
            placeholdervalue: '',
            type: 'GET',
            data: 'Jones=1',
            dataType: 'JSON',
            init: function () {
                this.bindEvents();
            },
            bindEvents: function () {
                /**
                 * Upload image by drag and drop
                 */
                $(".upload-btn-block").on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }).on('dragover dragenter', function () {
                    $(this).addClass('is-dragover');
                }).on('dragleave dragend drop', function () {
                    $(this).removeClass('is-dragover');
                }).on('drop', function (e) {
                    var droppedFiles = e.originalEvent.dataTransfer.files;
                    RITO.AjaxImageUpload($(this), droppedFiles);
                });

                /**
                 * Upload image by button  click
                 */
                $(".upload-btn-block input[type=file]").on('change', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var input = this;
                    var droppedFiles = input.files;
                    RITO.AjaxImageUpload($(this), droppedFiles);
                });

                /**
                 * Delete file
                 */

                $(".upload-btn-block .delete-btn").on('click', function () {
                    var wrapper = $(this).parents(".drop-container");
                    var preview = $(this).data('noimage');
                    wrapper.find(".image-name").text('選択されていません');
                    wrapper.find(".delete-btn").css('display', 'none');
                    wrapper.find(".product-image").val("");
                    wrapper.find(".upload-btn-block input[type=file]").val(null);
                    if (preview == 'show') {
                        wrapper.find(".product-image img").attr("src", noImage);
                    } else {
                        wrapper.find(".product-image img").attr("src", noImage).hide();
                    }
                });
            },
            AjaxImageUpload: function (el, droppedFiles) {
                var $form = el.closest('form');
                var wrapperId = el.parents(".drop-container").data("id");
                RITO.data = new FormData($form.get(0));
                if (droppedFiles) {
                    $.each(droppedFiles, function (i, file) {
                        RITO.data.append("image", file);
                    });
                }
                RITO.data.append("image_id",wrapperId);
                RITO.url = "/image-upload-single";
                RITO.type = "POST";
                RITO.AjaxUpload(el, 'AjaxUploadResponse');

            },
            AjaxUpload: function (el, responseHandler) {
                var request = $.ajax({
                    url: RITO.url,
                    type: RITO.type,
                    dataType: RITO.dataType,
                    data: RITO.data,
                    cache: false,
                    contentType: false,
                    processData: false
                });
                request.done(function (response) {
                    RITO[responseHandler].apply(this, [{
                        "el": el,
                        "response": response
                    }]);
                });
                request.fail(function (jqXHR, textStatus) {
                    
                    /*if (jqXHR.status === 403 && textStatus === "error") {
                        location.reload();
                    }*/
                });
            },
            AjaxUploadResponse: function (data) {
                var wrapper = data.el.parents(".drop-container");
                var res = data.response;
                wrapper.find(".image-name").text(res.original_name);
                wrapper.find(".product-image img").attr("src", res.s3).show();
                wrapper.find(".delete-btn").css('display', 'flex');
                wrapper.find(".delete-btn").removeClass('d-none');
                wrapper.find(".product-image").val(res.file_name);
            }
           
        };
    RITO.init();
});
