function validateForm() {
    var formData = $('form').serializeArray();
    console.log('Form submitted');
    console.log(formData);
    event.preventDefault();


//    $.post('login.php', {username:formData[0].value,password:formData[1].value},
//    function(data){
//        console.log(data.success);
//        if(data.success === 'success'){
//            $('#logon').html('Successfully Logged In');
//        } else{
//            $('#logon').append('Something went wrong, please try again');
//
//        }
//    });
//}

    $.ajax({
        type: 'POST',
        url: 'login.php',
        data: {username: formData[0].value, password: formData[1].value},
        dataType: 'json',
        success: function (data) {
            console.log(data.success);
            if (data.success) {
                $('#logon').html('Successfully Logged In as ' + data.name);
                $('#logon').append('<br><a href="logout.php">Logout here</a>');

            } else {
                $('#logon').append('Something went wrong, please try again');

            }
        }
    });
}
