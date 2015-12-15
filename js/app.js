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

    $.ajax({ //send username/password and await response
        type: 'POST',
        url: 'adventure.php',
        data: {save: true, title: adventureTitle, content: adventureContent },
        dataType: 'json',
    })
        .done(function (data) { //on successful response reload the page
            if(removedImages.length > 0){
                deletePictures(data.PostID);
                savePictures(data.PostID);
            }else{
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

function deletePictures(postId){

    var jsonString = JSON.stringify(removedImages);

    $.ajax({
            type: 'POST',
            url: 'deletePictures.php',
            data: {data: jsonString},
            cache: false,
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
        });
}

$('#editButton').click(function () {
    location.href = "adventure.php?id=" + PostID + "&edit=1";
});

$('#publish-adventure-button').on('click', function(){
    savePost();
});

$('#cancelButton').click(function () {
    location.href = "adventure.php?id=" + PostID;
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

var removedImages = new Array();



$(document).on('click', '.removeImageIcon', function(){
    //$(this).parent().remove();
    if($(this).parent().children().css('opacity') != '0.3') {
        $(this).parent().children().css('opacity', '0.3');
        var imageSource = $(this).parent().children("img").attr('src');
        if (imageSource) {
            removedImages.push(imageSource);
        }
    }
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
        if(inputBar.val().length == 0){
            event.preventDefault();
        }
    });

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

    //-------------------------------------------------------------------
    //Change user type on admin page
    $(document.body).on('click', '.change-user-type-icon', function() {
        var alreadyFired = false;
        var pTag = $(this).parent().clone();
        var username = $(this).parent().parent().attr("username");
        var parentDiv = $(this).parent().parent();
        var selectedValue = $.trim(pTag.text());
        var selectTag = '<select name="user-type">' +
            '<option value="unverified">Unverified</option>' +
            '<option value="reader">Reader</option>' +
            '<option value="author">Author</option>' +
            '<option value="admin">Admin</option>' +
            '</select>';
        selectTag = selectTag.replace(selectedValue + '"', selectedValue + '" selected');
        var okChange = '<span class="glyphicon glyphicon-ok change-user-type-action-icon ' + username + '"></span>';
        var cancelChange = '<span class="glyphicon glyphicon-remove change-user-type-action-icon ' + username + '"></span>';
        $(this).closest('p').replaceWith(selectTag + okChange + cancelChange);

        $(document.body).on('click', '.glyphicon.glyphicon-ok.change-user-type-action-icon.' + username, function() {
            var selectedValue = $('select[name="user-type"]').val();
            changeUserType(username, selectedValue, parentDiv);
            alreadyFired = true;
        });

        $(document.body).on('click', '.glyphicon.glyphicon-remove.change-user-type-action-icon.' + username, function() {
            parentDiv.empty();
            parentDiv.append(pTag);
        });
    });

    //-------------------------------------------------------------------
    //User filters on admin page
    var clicked11 = false;

    $('input[by="users-username"]').on('click', function(){
        if(!clicked11){
            sortUsers(true, false);
            setClicksToFalse();
            clicked11 = true;
        } else{
            sortUsers(true, true);
            clicked11 = false;
        }
    });

    var clicked22 = false;
    $('input[by="users-usertype"]').on('click', function(){
        if(!clicked22){
            sortUsers(false, false);
            setClicksToFalse();
            clicked22 = true;
        } else{
            sortUsers(false, true);
            clicked22 = false;
        }
    });

    //-------------------------------------------------------------------
    //Adventure filters on admin page

    var clicked1 = false;
    $('input[by="adventures-author"]').on('click', function(){
        if(!clicked1){
            sortAdventures(1, false);
            setClicksToFalse();
            clicked1 = true;
        } else{
            sortAdventures(1, true);
            clicked1 = false;
        }
    });

    var clicked2 = false;
    $('input[by="adventures-title"]').on('click', function(){
        if(!clicked2){
            sortAdventures(2, false);
            setClicksToFalse();
            clicked2 = true;
        } else{
            sortAdventures(2, true);
            clicked2 = false;
        }
    });

    var clicked3 = false;
    $('input[by="adventures-upvotes"]').on('click', function(){
        if(!clicked3){
            sortAdventures(3, true);
            setClicksToFalse();
            clicked3 = true;
        } else{
            sortAdventures(3, false);
            clicked3 = false;
        }
    });

    var clicked4 = false;
    $('input[by="adventures-city-country"]').on('click', function(){
        if(!clicked4){
            sortAdventures(4, false);
            setClicksToFalse();
            clicked4 = true;
        } else{
            sortAdventures(4, true);
            clicked4 = false;
        }
    });

    var clicked5 = false;
    $('input[by="adventures-date-posted"]').on('click', function(){
        if(!clicked5){
            sortAdventures(5, true);
            setClicksToFalse();
            clicked5 = true;
        } else{
            sortAdventures(5, false);
            clicked5 = false;
        }
    });

    var clicked6 = false;
    $('input[by="adventures-comments"]').on('click', function(){
        if(!clicked6){
            sortAdventures(6, true);
            setClicksToFalse();
            clicked6 = true;
        } else{
            sortAdventures(6, false);
            clicked6 = false;
        }
    });

    function setClicksToFalse(){
        clicked11 = false;
        clicked22 = false;
        clicked1 = false;
        clicked2 = false;
        clicked3 = false;
        clicked4 = false;
        clicked5 = false;
        clicked6 = false;
    }
});



function changeUserType(username, userType, parentDiv){
    //Show processing modal
    $('#users-changing-loading-modal').modal({
        show: true,
        backdrop: 'static',
        keyboard: true
    });

    $.ajax({
        type: 'POST',
        url: 'admin.php?username=' + username + '&usertype=' + userType,
        dataType: 'json'
    })
        .done(function (data) {

            var pTag = userType == 'unverified' ? $("<p>", {class: 'text-danger'}) : $("<p>");
            pTag.append(userType);
            pTag.append($('<span>', {class:"glyphicon glyphicon-pencil change-user-type-icon"}));
            parentDiv.empty();
            parentDiv.append(pTag);
            parentDiv.parent().parent().attr('usertype', userType);

            //Hide processing modal
            $('#users-changing-loading-modal').modal('hide');

            //You dare to demote yourself?! Well then...
            if(data.selfDestruction){
                location.reload();
            }

        })
        .fail(function () {
            console.log("Error happened while changing user type [" + userType + "] for username [" + username + "]");
            $('#users-changing-loading-modal').modal('hide');
        });

}

function deleteAdventure(postID) {

    //Show processing modal
    $('#loading-modal').modal({
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
                $('#loading-modal').modal('hide');
            }
        })
        .fail(function () {
            console.log("Error happened while deleting the adventure, check Key Constraints and Permissions");
            $('#loading-modal').modal('hide');
        });
}

//Below is the Roman's pride - super inefficient - memory wasting sorting algorithms
//Read at your own risk - you have been warned
//Big O notation - O(cat'sBack.glueWith(sandwich'sOpenSide))
function sortAdventures(sortingFilter, doInvert){
    //Show processing modal
    $('#adventures-sorting-loading-modal').modal({
        show: true,
        backdrop: 'static',
        keyboard: true
    });

    //Grab all the rows
    var rows = $('.adventures-table').children('tbody').children('tr');

    //Sort stuff according to the filter selected
    switch(sortingFilter){
        case 1:
            jsStringSorter(rows, 'author', doInvert);
            console.log("case 1");
            break;
        case 2:
            jsStringSorter(rows, 'title', doInvert);
            console.log("case 2");
            break;
        case 3:
            jsIntSorter(rows, 'upvotes', doInvert);
            console.log("case 3");
            break;
        case 4:
            jsStringSorter(rows, 'city-country', doInvert);
            console.log("case 4");
            break;
        case 5:
            jsStringSorter(rows, 'date-posted', doInvert);
            console.log("case 5");
            break;
        case 6:
            jsIntSorter(rows, 'comments', doInvert);
            console.log("case 6");
            break;
        default:
            console.log('Something went wrong.');
    }

    //Hide processing modal
    $('#adventures-sorting-loading-modal').modal('hide')
}


function sortUsers(sortByUsername, doInvert){

    //Show processing modal
    $('#users-sorting-loading-modal').modal({
        show: true,
        backdrop: 'static',
        keyboard: true
    });

    //Grab all the rows
    var rows = $('.users-table').children('tbody').children('tr');

    //Check if sorting set by username or usertype
    if(sortByUsername){
        jsStringSorter(rows, 'username', doInvert);
    } else{
        jsIntSorter(rows, 'usertype', doInvert);
    }

    //Hide processing modal
    $('#users-sorting-loading-modal').modal('hide')
}

function jsStringSorter(arr, filter, doInvert){
    var originalCopy = arr.clone();

    //Sort the copied array (alphabetically in an ascending order)
    originalCopy.sort(function(val1, val2){
        var nameA = $(val1).attr(filter).toLowerCase();
        var nameB = $(val2).attr(filter).toLowerCase();
        if (nameA < nameB){ return doInvert ? 1 : -1; }
        if (nameA > nameB){ return doInvert ? -1 : 1; }
        return 0;
    });

    replaceWithSortedElements(arr, originalCopy);
}

function jsIntSorter(arr, filter, doInvert){
    var originalCopy = arr.clone();

    if(filter == 'usertype'){
        var userTypes = {unverified: 0, reader: 1, author: 2, admin: 3};

        //Sorts by the usertype, from values above
        originalCopy.sort(function(val1, val2){
            return doInvert ? (userTypes[$(val2).attr(filter)] - userTypes[$(val1).attr(filter)]) :
                (userTypes[$(val1).attr(filter)] - userTypes[$(val2).attr(filter)]);
        });
    } else{
        originalCopy.sort(function(val1, val2){
            return doInvert ? (parseInt($(val2).attr(filter)) - parseInt($(val1).attr(filter))) :
                (parseInt($(val1).attr(filter)) - parseInt($(val2).attr(filter)));
        });
    }

    replaceWithSortedElements(arr, originalCopy);
}

function replaceWithSortedElements(arr, sortedArr){
    //Replace live rows with the sorted ones
    $.each(arr, function(index){
        $(this).replaceWith(sortedArr[index]);
    });
}