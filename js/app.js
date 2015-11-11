function loginForm() {
    var formData = $('#loginForm').serializeArray(); //Grab the form input

    console.log('Form submitted'); //debugging
    console.log(formData); //debugging

    event.preventDefault(); //stop page reload on submit
    $('#login-modal').append('<i class="pe-7s-config pe-spin pe-5x"></i>');

    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'login.php',
        data: {username: formData[0].value, password: formData[1].value},
        dataType: 'json'
    })
    .done(function (data) { //on response log user in if successful or prompt try again
        console.log(data.success);

        var logon = $('#logon');
        if (data.success) {
            location.reload();
        } else {
            logon.append('Something went wrong, please try again');
        }
    })
    .fail(function(data){
        console.log("Error happened");
        console.log(data);
        console.log(data.responseText);
    });
}

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
            console.log(data);
            console.log(data.responseText);
        });
    } else{
        alert("Passwords don't match");
    }

}

function commentForm() {
    var formData = $('#commentForm').serializeArray();

    console.log('Form submitted');
    console.log(formData);
    event.preventDefault(); //stop page reload on submit

    if (formData[0].value !== '') {
        $.ajax({ //send username/password and await response
            type: 'POST',
            url: 'adventure.php?id=' + PostID, //Dis ain't secure!!!
            data: {comment: formData[0].value},
            dataType: 'json'
        })
            .done(function (data) { //on successful response reload the page
                console.log(data);

                location.reload();
            })
            .fail(function (data) { //on unsuccessful response output error
                console.log("Error happened");
                console.log(data);
                console.log(data.responseText);
            });
    } else {
        alert("Comments cannot be empty");
    }
}

function savePost() {
    var adventureTitle = $('#adventureTitle').html();
    var adventureContent = $('#adventureContent').html();

    //Really rough version, but checks if there is a PostID and then either attaches it or saves adventure as new
    if (PostID !== '') {
        $.ajax({ //send username/password and await response
            type: 'POST',
            url: 'adventure.php?id=' + PostID,
            data: {save: true, title: adventureTitle, content: adventureContent},
            dataType: 'json'
        })
            .done(function (data) { //on successful response reload the page
                location.href = "adventure.php?id=" + data.PostID;
            })
            .fail(function (data) { //on unsuccessful response output error
                console.log("Error happened");
                console.log(data);
                console.log(data.responseText);
            });
    } else {
        $.ajax({ //send username/password and await response
            type: 'POST',
            url: 'adventure.php',
            data: {save: true, title: adventureTitle, content: adventureContent},
            dataType: 'json'
        })
            .done(function (data) { //on successful response reload the page
                location.href = "adventure.php?id=" + data.PostID;
            })
            .fail(function (data) { //on unsuccessful response output error
                console.log("Error happened");
                console.log(data);
                console.log(data.responseText);
            });
    }

}

function loadMoreAdventures() {
    if (typeof timesRequested == 'undefined') {
        timesRequested = 1;
    } else {
        timesRequested++;
    }
    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'index.php',
        data: {timesRequested: timesRequested},
        dataType: 'json'
    })
        .done(function (data) {
            console.log(data);
            for (adventures of
            data
            )
            {
                $('.card-container').append('<div class="card">' +
                    '<h3>' + adventures.Title + '</h3>' +
                    '<p>by: ' + adventures.Username + '</p>' +
                    '<p>' + adventures.Upvotes + '</p>' +
                    '<button type="button" class="btn btn-primary btn-sm" id="likeButton" data-postID="' + adventures.PostID + '">Like this</button>' +
                    '<a href="/adventure.php?id=' + adventures.PostID + '">View Adventure Here</a>' +
                    '</div>');
            }
        })
        .fail(function (data) { //on unsuccessful response output error
            console.log("Error happened");
            console.log(data);
            console.log(data.responseText);
        });;
}

$('#editButton').click(function () {
    location.href = "adventure.php?id=" + PostID + "&edit=1";
});
$('#saveButton').click(function () {
    savePost();
});
$('#newPostButton').click(function () {
    location.href = "adventure.php?create=1";
});
$('#loadMoreButton').click(function () {
    loadMoreAdventures();
});