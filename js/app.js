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
            var error = $('#loginForm').text().indexOf(data.error) > -1;
            if(!error){
                $('#loginForm').prepend(data.error);
            }
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
            data: {
                username: formData[0].value,
                password: formData[1].value,
                email: formData[3].value,
                fName: formData[4].value,
                lName: formData[5].value,
                twitter: formData[6].value,
                country: formData[7].value
            },
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

            var commentActionsString = '';

            $.each(data, function(){

                var commentSelector = $('.comment-container');

                if(this.InReplyTo){
                    commentSelector = $('[data-comment-id="'+ this.InReplyTo +'"]');
                }


                var commentString = '<div class="comment" data-comment-id="' + this.CommentID + '">' +
                    '<h4 class="comment-author">' + this.Username + '</h4>' +
                    '<h5 class="comment-timestamp">' + this.DatePosted + '</h5>' +
                    '<p class="comment-content">' + this.Content + '</p>';
                console.log(canEdit + ' ' + username + ' ' + this.Username);

                if (user_group >= 1) {
                    commentActionsString = '<div class="comment-actions"><p class="replyButton"><i class="pe-7s-back"></i> Reply</p>';
                    if (canEdit === 1 || username === this.Username) {
                        commentActionsString += '<p class="edit-comment-button"><i class="pe-7s-pen"></i> Edit</p>' +
                            '<p class="delete-comment-button"><i class="pe-7s-close-circle"></i> Delete</p>';
                    }
                    commentActionsString += '</div>';
                }

                commentSelector.append(commentString + commentActionsString + '</div>');
            });

        })
        .fail(function (data) { //on unsuccessful response output error
            console.log("Error happened");
            console.log(data);
            console.log(data.responseText);
        });
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

$('.comment-container').on('click', '.replyButton', function () {
    var replySection = $(this).parent();
    replySection.html(
        '<div class="comment-box reply">' +
        '<div contenteditable="true" placeholder="Your reply..." id="comment-reply"></div>' +
        '<button id="save-reply-button">submit</button>' +
        '</div>'
    );
});

$('.comment-container').on('click', '.edit-comment-button', function () {
    var commentContent = $(this).parent().prev();
    commentContent.attr('contenteditable', 'true');
    commentContent.focus();
    $(this).html('<i class="pe-7s-diskette"></i> Save');
    $(this).attr('class', 'save-comment-button');

});

$('.comment-container').on('click', '.delete-comment-button', function () {
    var commentDeleted = $(this).parent().parent().data('commentId');

    $.ajax({
        type: 'POST',
        url: 'adventure.php?id=' + PostID,
        data: {commentDeleted: commentDeleted},
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

$('.comment-container').on('click', '.save-comment-button', function () {
    var commentContent = $(this).parent().prev().html(); //has to be html to grab the line breaks
    var commentEdited = $(this).parent().parent().data('commentId');

    $.ajax({
        type: 'POST',
        url: 'adventure.php?id=' + PostID,
        data: {comment: commentContent, commentEdited: commentEdited},
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
    var keywordsInput = $('#keywordsInput').val();
    var city = $('#cityInput').val();
    var country = $('#countryInput').val();

    var keywords = keywordsInput.split(',');
    $.each(keywords, function (key, value) {
        keywords[key] = $.trim(value);
    });

    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'adventure.php',
        data: {
            save: true,
            title: adventureTitle,
            content: adventureContent,
            keywords: keywords,
            city: city,
            country: country
        },
        dataType: 'json',
    })
        .done(function (data) { //on successful response reload the page
            if(removedImages.length > 0){
                deletePictures(data.PostID);
                savePictures(data.PostID);
            } else {
                savePictures(data.PostID);
            }
        })
        .fail(function (data) { //on unsuccessful response output error
            console.log("Error happened");
            console.log(data);
            console.log(data.responseText);
        });
}

function savePictures(postId){

    var adventurePictures = document.getElementById('adventurePictures');

    var files = adventurePictures.files;

    if(files.length > 0){
        var formData = new FormData();

        formData.append('postId', postId);

        for(var i = 0; i < files.length; i++){
            var file = files[i];

            if(!file.type.match('image.*')){
                continue;
            }

            formData.append('files[]', file);
        }

        if(formData){
            $.ajax({
                type: 'POST',
                url: 'upload.php',
                data: formData,
                contentType: false,
                processData: false
            })
                .done(function (data) { //on successful response reload the page
                    console.log(data);
                    location.href = "adventure.php?id=" + postId;
                })
                .fail(function (data) { //on unsuccessful response output error
                    console.log("Error happened");
                    console.log(data);
                    console.log(data.responseText);
                });
        }
    } else {
        location.href = "adventure.php?id=" + postId;
    }
}


var removedImages = [];

$(document).on('click', '.removeImageIcon', function () {
    //$(this).parent().remove();
    if ($(this).parent().children().css('opacity') != '0.3') {
        $(this).parent().children().css('opacity', '0.3');
        var imageSource = $(this).parent().children("img").attr('src');
        if (imageSource) {
            removedImages.push(imageSource);
        }
    }
});

function deletePictures(postId){

    var jsonString = JSON.stringify(removedImages);

    $.ajax({
            type: 'POST',
            url: 'deletePictures.php',
            data: {data: jsonString},
            cache: false
        })
        .done(function (data) { //on successful response reload the page
            console.log(data);
            location.href = "adventure.php?id=" + postId;
            removedImages = [];
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
            console.log(data);
            $.each(data, function(){
                if (this.Voted === 'true') {
                    $('.card-container').append('<div class="card">' +
                        '<div class="card-image-container">' +
                        '<div id="' + this.PostID + '"></div>' +
                        '<a href="/adventure.php?id=' + this.PostID + '"><span class="adventureLink"></span></a>' +
                        '</div>' +
                        '<div class="card-text-container">' +
                        '<div class="adventure-title">' +
                        '<h3>' + this.Title + '</h3>' +
                        '<p>by: ' + this.Username + '</p>' +
                        '</div>' +
                        '<div class="card-footer">' +
                        '<i class="pe-7s-like pe-2x likeButton voted" data-post-id="' + this.PostID + '"></i>' +
                        '<p>' + this.Upvotes + ' Likes</p>' +
                        '</div>' +
                        '</div>' +
                        '</div>');
                } else {
                    $('.card-container').append('<div class="card">' +
                        '<div class="card-image-container">' +
                        '<div id="' + this.PostID + '"></div>' +
                        '<a href="/adventure.php?id=' + this.PostID + '"><span class="adventureLink"></span></a>' +
                        '</div>' +
                        '<div class="card-text-container">' +
                        '<div class="adventure-title">' +
                        '<h3>' + this.Title + '</h3>' +
                        '<p>by: ' + this.Username + '</p>' +
                        '</div>' +
                        '<div class="card-footer">' +
                        '<i class="pe-7s-like pe-2x likeButton" data-post-id="' + this.PostID + '"></i>' +
                        '<p>' + this.Upvotes + ' Likes</p>' +
                        '</div>' +
                        '</div>' +
                        '</div>');
                }
                $('#' + this.PostID).css('background-image', 'url("' + this.Path + '")');

            });
        })
        .fail(function (data) { //on unsuccessful response output error
            console.log("Error happened");
            console.log(data);
            console.log(data.responseText);
        });
}

$('#editButton').click(function () {
    location.href = "adventure.php?id=" + PostID + "&edit=1";
});

$('#publish-adventure-button').on('click', function(){
    savePost();
});

$('#keywordSubmit').on('click', function () {
    savePost();
});

$('#cancelButton').click(function () {
    location.href = "adventure.php?id=" + PostID;
});
$('#deleteButton').click(function () {
    $.ajax({
        type: 'GET',
        url: 'adventure.php?id=' + PostID + '&remove',
        data: {id: PostID},
        cache: false
    })
        .done(function (data) { //on successful response reload the page
            console.log(data);
            location.href = "index.php";
        })
        .fail(function (data) { //on unsuccessful response output error
            console.log("Error happened");
            console.log(data);
            console.log(data.responseText);
        });
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
        $(this).removeClass('voted');
    } else {
        $(this).addClass('voted');
    }
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

//Registers the like button click. Cannot be a simple .click() function due to the way jquery handles appended html
$('.adventure-footer').on('click', '.likeButton', function () {
    var colour = $(this).css('color');

    if (colour === 'rgb(217, 30, 24)') {
        $(this).removeClass('voted');
    } else {
        $(this).addClass('voted');
    }
    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'index.php',
        data: {liked: PostID}, //references the variable loaded in when adventure.php is loaded
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
$('#filter-by-likes-button').click(function () {
    location.href = "search.php?query=" + searchQuery + "&likes=1";
});

$('#show-all-authors-button').click(function () {
    location.href = "search.php?authors=1";
});

$(document).ready(function () {
    //When editing this will automatically focus on the article title
    $('#adventureTitle').focus();

    //-------------------------------------------------------------------
    //Search bar slide in on hover
    var navbar = $(".navbar");
    var inputGroupBtn = $("span.input-group-btn");
    var inputBar = $("#search-field");

    var clicked = false;

    inputGroupBtn.on("click", function(){
        if(!clicked){
            inputBar.animate({width:'toggle'}, 200);
            clicked = true;
        }
    });

    if(inputBar.css("display") === "block"){
        inputBar.css("padding","6px 12px");
    }

    //-------------------------------------------------------------------
    //Prevent submission of empty string
    $("#submit-search-btn").on("click", function(event){
        if(inputBar.val().length == 0 || inputBar.val().length > 250){
            event.preventDefault();
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
        var startY = navbar.height() * 1.9; //The point where the navbar changes in px

        if($(window).scrollTop() > startY){
            $('.navbar').addClass("scrolled");
        }else{
            $('.navbar').removeClass("scrolled");
        }
    }

    if(navbar.length > 0){
        $(window).on("scroll load resize", function(){
            checkScroll();
        });
    }

    //-------------------------------------------------------------------
    //Fix navbar's user dropdown weird hover event
    $(".dropdown-toggle.navbar-dropdown").css("background-color", "transparent");

    //-------------------------------------------------------------------
    //Sets focus to the username fields on the modals. Typical autofocus will not work on BS Modals
    $('#loginModal').on('shown.bs.modal', function () {
        $('input[name="username"]').focus()
    });

    $('#registerModal').on('shown.bs.modal', function () {
        $('input[name="username"]').focus()
    });

    //-------------------------------------------------------------------
    //Adds the little tooltip onto the password field
    $('input[name="password"]').tooltip();

    //-------------------------------------------------------------------
    //Delete Adventure
    var postID = "";
    $(".remove-my-adventure-link").on("click", function(){
        postID = $(this).attr('adventure-id');
    });

    $(".adventure-remove-confirm").on("click", function() {
        if(postID.length > 0) {
            deleteAdventure(postID);
        }
    });

});

function deleteAdventure(postID) {

    //Show processing modal
    $('#adventures-removing-loading-modal').modal({
        show: true,
        backdrop: 'static',
        keyboard: true
    });

    $.ajax({
        type: 'POST',
        url: 'adventure.php?id=' + postID + '&remove=1',
        dataType: 'json'
    })
        .done(function (result) {
            if(result){
                //Change counter amount in the title
                $('#adventure-amount').text(parseInt($('#adventure-amount').text())-1);

                var alreadyRemoved = false;

                //If last adventure on the page - do stuff
                if($('div[adventure-id=' + postID + ']').is(':last-child')){
                    //Remove adventure div
                    $('div[adventure-id=' + postID + ']').remove();
                    alreadyRemoved = true;

                    //Add br tags to the end
                    $(".col-sm-12 div:last-child").append("<br><br>");
                }

                //Remove adventure div
                if(!alreadyRemoved){
                    $('div[adventure-id=' + postID + ']').remove();
                }

                $('tr[post-id=' + postID + ']').remove();

                //Hide processing modal
                $('#adventures-removing-loading-modal').modal('hide');
            }
        })
        .fail(function () {
            console.log("Error happened while deleting the adventure, check Key Constraints and Permissions");
            $('#adventures-removing-loading-modal').modal('hide');
        });
}