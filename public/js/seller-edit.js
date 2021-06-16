$(document).ready(function(){
    $('#user-edit-form').submit( function(e){
        let elems = document.querySelectorAll("div[class^='prefecture_']");
        if(elems.length == 0){
            $('.error_msg.description').html('少なくとも一つの離島を選択してください');
            return false;
        }else{
            return true;
        }
    });

    /*************************************
    /onclick appent remote island function
    *************************************/
    var island_id_array = [];
    var islands_id = $("#island_hide input[name='hidden']")
    $.each(islands_id,function(key,val){
        island_id_array.push($(val).val())
    })    
    var prefectures_id_array = [];
    var prefectures_id = $("#prefecture_hide input[type='hidden']")
    $.each(prefectures_id,function(key,val){
        prefectures_id_array.push($(val).val())
    })
    $("#island_id").select2({
        placeholder: "離島名を選択してください",
        allowClear: true
    });  

    $('#button-isalnd').on('click', function (e) {
        e.preventDefault();
        var island_id = $("#island_id").val();  
        var prefectures_id = $('#island_id :selected').closest('optgroup').prop('label');  
        var count = 0;
        if(island_id){    
            var test_select = $("#island_id option:selected").text(); 
            var idx = $.inArray(island_id, island_id_array);  
            var prefecturex = $.inArray(prefectures_id, prefectures_id_array);   
            if(prefecturex == -1){
                if (idx == -1) {                    
                    island_id_array.push(island_id); 
                    prefectures_id_array.push(prefectures_id); 
                    $("#append-data").append('<h5>' + prefectures_id + '</h5>');
                    var ppp = "prefecture_"+prefectures_id;
                    $("#append-data").append('<div class="'+ppp+'"><button type="button" class="remote-island-data-block">' + test_select + '</button></div>' ); 
                    $("#append-data").append('<input class="d-none" type="hidden" name="island_ids[]" value="'+island_id+'" >');
                    $("#island_id_exist_megess").text("");   
                    count++;
                } else {
                    island_id_array.splice(idx, 0);
                    $("#island_id_exist_megess").text("離島はすでに存在しています。"); 
                }
            }  
            else { 
                if (idx == -1) { 
                    var island_val = $("#island_id").val();   
                    var island_name = '.island_'+island_val;  
                    var x = $(island_name)[0];
                    if(x){
                        $("#island_id_exist_megess").text("離島はすでに存在しています。");  
                    } else{
                        island_id_array.push(island_id);  
                        $(".prefecture_"+prefectures_id).append('<button type="button" class="remote-island-data-block">' + test_select + '</button>'); 
                        $("#append-data").append('<input class="d-none" type="hidden" name="island_ids[]" value="'+island_id+'" >');
                        $("#island_id_exist_megess").text("");  
                    }                     
                }else {
                    island_id_array.splice(idx, 0);
                    $("#island_id_exist_megess").text("離島はすでに存在しています。");  
                }
            }             
        }
    });
    $(document.body).on("click", ".remove-text-block", function () {
        $('#append-data').html('');
        $('#island_hide').html('');
        $('#prefecture_hide').html('');
        island_id_array = [];
        prefectures_id_array = [];
        $("#island_id_exist_megess").text(""); 
    }); 
});