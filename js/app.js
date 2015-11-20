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

function loadComments(postID, username, user_group, canEdit) {
    $.ajax({ //send off request for comments
        type: 'POST',
        url: 'adventure.php?id=' + postID,
        data: {loadComment: 1},
        dataType: 'json'
    })
        .done(function (data) { //on successful response displays comments
            //@formatter:off

            if(user_group >= 1){
                console.log('This worked');
                var commentActionsString = '<div class="comment-actions"><p class="replyButton"><i class="pe-7s-back"></i> Reply</p></div>';
            } else{
                var commentActionsString = '';
            }

            for (comment of data){
                if(comment.InReplyTo){
                    var commentSelector = $('[data-comment-id="'+ comment.InReplyTo +'"]');
                } else{
                    var commentSelector = $('.comment-container');
                }
                if(canEdit === 1 || username === comment.Username){
                    var commentEditingString = '<p>You can edit this</p>'
                }
                var commentString = '<div class="comment" data-comment-id="' + comment.CommentID + '">' +
                    '<h4 class="comment-author">' + comment.Username + '</h4>' +
                    '<h5 class="comment-timestamp">' + comment.DatePosted + '</h5>' +
                    '<p class="comment-content">' + comment.Content + '</p>';

                if(canEdit === 1 || username === comment.Username){
                    commentString = commentString + '<p>You can edit this</p>'
                }

                commentSelector.append(commentString + commentActionsString + '</div>');
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

                //location.reload();
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

$('.comment-container').on('click', '.replyButton', function () {
    $(this).parent().html(
        '<div class="comment-box reply">' +
        '<div contenteditable="true" placeholder="Your reply..." id="comment-reply"></div>' +
        '<button id="save-reply-button">submit</button>' +
        '</div>'
    );
});

$('.comment-container').on('click', '#save-reply-button', function () {
    var commentContent = $(this).siblings().html(); //has to be html to grab the line breaks
    var replyingTo = $(this).parent().parent().parent().data('commentId');

    $.ajax({
        type: 'POST',
        url: 'adventure.php?id=' + PostID,
        data: {comment: commentContent, replyingTo: replyingTo},
        dataType: 'json'
    })
        .done(function (data) {
            console.log(data);
            console.log(data.success);
            location.reload();
        })
        .fail(function (data) { //on unsuccessful response output error
            console.log("Error happened");
            console.log(data);
            console.log(data.responseText);
        });

});

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
            $.each(data, function(){
                $('.card-container').append('<div class="card">' +
                    '<div class="card-text-container">' +
                    '<h3>' + this.Title + '</h3>' +
                    '<p>by: ' + this.Username + '</p>' +
                    '<a href="/adventure.php?id='+ this.PostID +'"><span class="adventureLink"></span></a>' +
                    '</div>' +
                    '<div class="card-footer">' +
                    '<i class="pe-7s-like pe-2x likeButton" data-post-id="'+ this.PostID +'"></i>' +
                    '<p>' + this.Upvotes + ' Likes</p>' +
                    '</div>' +
                    '</div>');
            });
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

    //-------------------------------------------------------------------
    //Search bar slide in on hover
    var inputText = $('input.form-control.input-md');
    var inputGroup = $('div.input-group');
    var navbar = $(".navbar");

    inputGroup.hover(function(){
        if(inputText.css("display") === "none"){
            inputText.animate({width:'toggle'}, 200);
        }
    });

    //-------------------------------------------------------------------
    //Search bar slide out when loss of focus occurs on the bar or user accidentally shows searching option
    inputGroup.focusout(function(){
        if($("div.input-group input").val().length == 0){
            slideBackSearchBar();
            mouseLeaveEvent();
        }
    });

    mouseLeaveEvent();

    //If user typed anything, remove mouseleave event from the navbar to prevent search bar hiding
    $("div.input-group input").keyup(function(){
        navbar.off("mouseleave");
    });
    $("div.input-group input").focus(function(){
        navbar.off("mouseleave");
    });

    //Hide search bar if mouse leaves the navbar area and user typed nothing
    function mouseLeaveEvent(){
        navbar.mouseleave(function(){
            slideBackSearchBar()
        });
    }

    function slideBackSearchBar(){
        if(inputText.css("display") !== "none"){
            inputText.animate({width:'toggle'}, 400);
        }
    }

    //-------------------------------------------------------------------
    //Text ellipsis function
    $(function () {
        var text = $(".text-ellipsis");
        if (text.text().length > 250) {
            text.html(text.text().substring(0, 247) + "...");
        }
    });

    //-------------------------------------------------------------------
    //Remove extra border if no Adventures were found on the search page
    if ( $('.first-section-container').children().length === 0 ) {
        $('.first-container').css("border","0");
    }

    //-------------------------------------------------------------------
    //Changes navbar's opacity based on the pixels scrolled. Uses .scrolled class styled in main.css
    function checkScroll(){
        var startY = $('.navbar').height() * 1.2; //The point where the navbar changes in px

        if($(window).scrollTop() > startY){
            $('.navbar').addClass("scrolled");
        }else{
            $('.navbar').removeClass("scrolled");
        }
    }

    if($('.navbar').length > 0){
        $(window).on("scroll load resize", function(){
            checkScroll();
        });
    }

    //-------------------------------------------------------------------

});

// Adds the little tooltip onto the password field
$('input[name="password"]').tooltip();
