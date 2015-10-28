function loginForm() {
    var formData = $('#loginForm').serializeArray(); //Grab the form input

    console.log('Form submitted'); //debugging
    console.log(formData); //debugging

    event.preventDefault(); //stop page reload on submit

    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'login.php',
        data: {username: formData[0].value, password: formData[1].value},
        dataType: 'json',
        success: function (data) { //on response log user in if successful or prompt try again
            console.log(data.success);
            console.log(data.name);

            var logon = $('#logon');
            if (data.success) {
                logon.html('Successfully Logged In as ' + data.name);
                logon.append('<br><a href="../logout.php">Logout here</a>');
            } else {
                logon.append('Something went wrong, please try again');
            }
        },
        error: function(data){
            console.log("Error happened");
            console.log(data.responseText);
        }
    });
}

$('#register').click(function(){
    $('#registerForm').submit();

});

function registerForm(){
    var formData = $('#registerForm').serializeArray(); //Grab the form input

    console.log('Form submitted'); //debugging
    console.log(formData); //debugging

    event.preventDefault(); //stop page reload on submit

    if(formData[1].value === formData[2].value){
        $('#register-modal').append('<i class="pe-7s-config pe-spin pe-5x"></i>');
        $.ajax({ //send username/password and await response
            type: 'POST',
            url: 'register.php',
            data: {username: formData[0].value, password: formData[1].value, email: formData[3].value, fName: formData[4].value, lName: formData[5].value, country: formData[6].value},
            dataType: 'json'
        })
        .done(function (data) { //on response log user in if successful or prompt try again
                console.log(data);

                if (data.success) {
                    console.log(data.success);
                    $('#register-modal').prepend('Successfully Logged In as ' + data.name);
                } else if(data.error){
                    $('#register-modal').append(data.error);
                } else {
                    $('#register-modal').append('Something went wrong, please try again');
                }
            })
        .fail(function(data){
            console.log("Error happened");
            console.log(data.responseText);
        });
    } else{
        alert("Passwords don't match");
    }

}