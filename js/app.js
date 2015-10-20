function validateForm() {
    var formData = $('form').serializeArray(); //Grab the form input

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
            var logon = $('#logon');
            if (data.success) {
                logon.html('Successfully Logged In as ' + data.name);
                logon.append('<br><a href="../logout.php">Logout here</a>');
            } else {
                logon.append('Something went wrong, please try again');
            }
        }
    });
}
