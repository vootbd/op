$("#file-upload").change(function (e) {
    console.log(this.files[0]);
    var type = this.files[0].type;
    $('.not-selected').text(this.files[0].name);
    $(".custom_error_msg.csv-upload").html('');
});