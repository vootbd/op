    /*************************************
    /onclick appent remote island function
    *************************************/
   var seller_id_array = [];
   var sellers_id = $("#sellerIds input[type='hidden']")
   $.each(sellers_id,function(key,val){
       seller_id_array.push($(val).val())
   })
   console.log(seller_id_array);
   $('#button-localvendor').on('click', function (e) {
       e.preventDefault();
       var seller_id = $("#seller_id").val();
       if(seller_id){
           var test_select = $("#seller_id option:selected").text();
           var idx = $.inArray(seller_id, seller_id_array);
           if (idx == -1) {
               seller_id_array.push(seller_id);
               $("#append-data").append('<option class="seller-'+seller_id+'" value="'+seller_id+'">'+test_select+'</option>');
               $("#sellerIds").append('<input class="d-none seller-'+seller_id+'" type="hidden" name="seller_ids[]" value="'+seller_id+'" >');
               $("#island_id_exist_megess").text("");
           } else { 
               seller_id_array.splice(idx, 0);
               $("#island_id_exist_megess").text("離島はすでに存在しています。");
           }
       }
   });
   $(document.body).on("click","#button-localvendor-delete", function(){
        var items = [];
        $('#append-data option:selected').each(function(){
            items.push($(this).val()); 
            $('.seller-'+$(this).val()).remove();
            console.log("seller-"+$(this).val());
            console.log(seller_id_array.indexOf($(this).val()));
            seller_id_array.splice(seller_id_array.indexOf($(this).val()), 1);
            console.log(seller_id_array);
        });
        var result = items.join(', ');
        console.log(result);
   });
   $(document.body).on("click", ".remove-text-block", function () {
       $('#append-data').html('');
       island_id_array = [];
       $("#island_id_exist_megess").text("");
   });
   
    /*************************************
    / Vendor name validation
    *************************************/
$(document).ready(function() {
    $("#localvendor_name_id").keyup(function() {
      this.value = this.value.replace(/ /g, "_");
    });
    $("#localvendor_name_id").bind("keyup blur", function(e) {
      $(this).val(
        $(this)
          .val()
          .replace(/[^A-Za-z0-9-_ ]/g, "")
      );
      var keyCode = e.keyCode || e.which;
      // console.log(keyCode);
  
      // console.log($(this).val().length);
      $("#error_msg_id").html("");
      if (keyCode > 0) {
        if (!$(this).val()) {
          // console.log("Not Allow");
          $("#error_msg_id").html(
            "この項目は半角の英数字、ハイフンまたはアンダーバーのみ有効です。"
          );
          return false;
        }
      }
  
      if ($(this).val().length > 0) {
        if (keyCode == 16) {
          // console.log("Not Allow");
          $("#error_msg_id").html(
            "この項目は半角の英数字、ハイフンまたはアンダーバーのみ有効です。"
          );
          return false;
        }
      }
      return $(this).val();
    });
  
    $("#localvendor_name_id").on("paste", function(e) {
      e.preventDefault();
    });
  
   
  


  });