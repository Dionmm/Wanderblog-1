function loginForm() {
    var formData = $('#loginForm').serializeArray(); //Grab the form input

    console.log('Form submitted'); //debugging
    console.log(formData); //debugging

    event.preventDefault(); //stop page reload on submit
    $('.modal-submit').html('<i class="pe-7s-config pe-spin pe-2x"></i>');

    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'login.php',
        data: {username: formData[0].value, password: formData[1].value},
        dataType: 'json'
    })
    .done(function (data) { //on response log user in if successful or prompt try again
        console.log(data.success);

        if (data.success) {
            location.reload();
        } else if (data.error) {
            $('#loginForm').prepend(data.error);
            $('.modal-submit').html('Sign In');

        } else {
            $('#loginForm').prepend('Something went wrong, please try again');
            $('.modal-submit').html('Sign In');

        }
    })
    .fail(function(data){
        console.log("Error happened");
        console.log(data);
        console.log(data.responseText);
            $('.modal-submit').html('Sign In');

        });
}

function registerForm(){
    var formData = $('#registerForm').serializeArray(); //Grab the form input

    console.log('Form submitted'); //debugging
    console.log(formData); //debugging

    event.preventDefault(); //stop page reload on submit

    if(formData[1].value === formData[2].value){
        $('.modal-submit').html('<i class="pe-7s-config pe-spin pe-2x"></i>');

        $.ajax({ //send username/password and await response
            type: 'POST',
            url: 'register.php',
            data: {username: formData[0].value, password: formData[1].value, email: formData[3].value, fName: formData[4].value, lName: formData[5].value, country: formData[6].value},
            dataType: 'json'
        })
        .done(function (data) { //on response log user in if successful or prompt try again
                console.log(data);

                if (data.success) {
                    location.reload();
                } else if(data.error){
                    //$('#registerForm.error-Message').html("");
                    $('#register-error-Message').html(data.error);
                    $('.modal-submit').html('Register');
                } else {
                    $('#registerForm').prepend('Something went wrong, please try again');
                    $('.modal-submit').html('Register');
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

function loadComments(postID) {
    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'adventure.php?id=' + PostID,
        data: {loadComment: 1},
        dataType: 'json'
    })
        .done(function (data) { //on successful response reload the page
            console.log(data);
            //@formatter:off

            for (comment of data){
                if(comment.InReplyTo){
                    var commentSelector = $('[data-comment-id="'+ comment.InReplyTo +'"]');
                } else{
                    var commentSelector = $('.comment-container');
                }
                commentSelector.append(
                    '<div class="comment" data-comment-id="' + comment.CommentID + '">' +
                    '<h4 class="comment-author">' + comment.Username + '</h4>' +
                    '<h5 class="comment-timestamp">' + comment.DatePosted + '</h5>' +
                    '<p class="comment-content">' + comment.Content + '</p>' +
                    '</div>'
                );
            }
            //@formatter:on
        })
        .fail(function (data) { //on unsuccessful response output error
            console.log("Error happened");
            console.log(data);
            console.log(data.responseText);
        });;
}

function commentForm() {
    var formData = $('#commentForm').serializeArray();

    console.log('Form submitted');
    console.log(formData);
    event.preventDefault(); //stop page reload on submit

    if (formData[0].value !== '') {
        $.ajax({ //send username/password and await response
            type: 'POST',
            url: 'adventure.php?id=' + PostID,
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


    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'adventure.php',
        data: {save: true, title: adventureTitle, content: adventureContent},
        dataType: 'json'
    })
        .done(function (data) { //on successful response reload the page
            location.href = "adventure.php?id=" + data.PostID;
            console.log(data);

        })
        .fail(function (data) { //on unsuccessful response output error
            console.log("Error happened");
            console.log(data);
            console.log(data.responseText);
        });

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
            //PHPStorm doesn't like this syntax and will automatically try to reset it, if you are getting random errors, ensure this is formatted correctly
            //@formatter:off
            for (adventures of data){
                $('.card-container').append('<div class="card">' +
                    '<div class="card-text-container">' +
                    '<h3>' + adventures.Title + '</h3>' +
                    '<p>by: ' + adventures.Username + '</p>' +
                    '<a href="/adventure.php?id='+ adventures.PostID +'"><span class="adventureLink"></span></a>' +
                    '</div>' +
                    '<div class="card-footer">' +
                    '<i class="pe-7s-like pe-2x likeButton" data-post-id="'+ adventures.PostID +'"></i>' +
                    '<p>' + adventures.Upvotes + ' Likes</p>' +
                    '</div>' +
                    '</div>');
            }//@formatter:on
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
//Registers the like button click. Cannot be a simple .click() function due to the way jquery handles appended html
$('.card-container').on('click', '.likeButton', function () {
    var postID = $(this).data('postId');
    var colour = $(this).css('color');

    if (colour === 'rgb(217, 30, 24)') {
        colour = 'rgb(51, 51, 51)';
    } else {
        colour = 'rgb(217, 30, 24)';
    }
    $(this).css('color', colour);
    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'index.php',
        data: {liked: postID},
        dataType: 'json'
    })
        .done(function (data) {
            console.log(data); //Increase and decrease the likes shown on the UI Here
        })
        .fail(function (data) { //on unsuccessful response output error
            console.log("Error happened");
            console.log(data);
            console.log(data.responseText);
        });
});


$(document).ready(function () {
    //When editing this will automatically focus on the article title
    $('#adventureTitle').focus();

    //Search bar slide in on hover
    var inputText = $('input.form-control.input-md');

    $('div.input-group').hover(function(){
        if(inputText.css("display") == "none"){
            inputText.animate({width:'toggle'}, 200);
        }
    });

});


/*
function searchQuery(){
    var formData = $('form[role="search"]').serializeArray(); //Grab the form input

    console.log(formData);

    //Do some integrity checks here..
    if(1==1){

    }

    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'search.php',
        data: {query: formData[0].value},
        dataType: 'json'
    });
}*/

// Adds the little tooltip onto the password field
$('input[name="password"]').tooltip();
