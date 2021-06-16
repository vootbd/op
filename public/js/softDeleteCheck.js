function softDeleteCheck() {
    var email = $("#email").val();
    if(email != '') {
        $(".loader-email-check").show();
        $.ajax( {
            type: "GET",
            url: SITEURL + "/soft/delete/check/" + email,
            success: function ( data ) {
                $( ".loader-email-check" ).hide();
                if (data.status === null) {
                    $( "#form-user-create" ).submit();
                } else {
                    $( '#softdelete_check' ).modal( 'show' );
                }
            },
            error: function ( data ) {
                console.log( 'Error:', data );
            }
        } );
    } else {
        $("#form-user-create").submit();
    }
}

function userActive() {
    var email = $( "#email" ).val();
    $.ajax( {
        type: "GET",
        url: SITEURL + "/user/active/" + email,
        success: function ( data ) {
            $( '#softdelete_check' ).modal( 'hide' );
            window.location.href = SITEURL + '/users/' + data.id + '/edit';
        },
        error: function ( data ) {
            console.log( 'Error:', data );
        }
    } );
}