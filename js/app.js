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
    $('.register').append('<form onsubmit="registerForm()"  method="POST" id="registerForm">' +
        '<label for="username">Username:<input type="text" name="username"></label>' +
        '<label for="password">Password:<input type="password" name="password"></label>' +
        '<label for="passwordConf">Confirm Password:<input type="password" name="passwordConf"></label>' +
        '<input type="submit"></form>');
});

function registerForm(){
    var formData = $('#registerForm').serializeArray(); //Grab the form input

    console.log('Form submitted'); //debugging
    console.log(formData); //debugging

    event.preventDefault(); //stop page reload on submit

    if(formData[1].value === formData[2].value){
        $.ajax({ //send username/password and await response
            type: 'POST',
            url: 'register.php',
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
        alert('Form posted');
    } else{
        alert("Passwords don't match");
    }

}