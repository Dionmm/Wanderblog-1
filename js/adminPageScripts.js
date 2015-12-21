$(document).ready(function () {

    //-------------------------------------------------------------------
    //User filters
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
    //Adventure filters

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

    var clicked7 = false;
    $('input[by="adventures-pictures"]').on('click', function(){
        if(!clicked7){
            sortAdventures(7, true);
            setClicksToFalse();
            clicked7 = true;
        } else{
            sortAdventures(7, false);
            clicked7 = false;
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
        clicked7 = false;
    }

    //-------------------------------------------------------------------
    //Change user type on admin page
    $(document.body).on('click', '.change-adventure-upvotes-icon', function() {
        var alreadyFired = false;
        var pTag = $(this).parent().clone();
        var postId = $(this).parent().parent().attr("post-id");
        var parentDiv = $(this).parent().parent();
        var upvotes = $.trim(pTag.text());
        var inputTag = '<input type="number" name="upvotes" min="0" max="999" value="' + upvotes + '">';
        var okChange = '<span class="glyphicon glyphicon-ok change-adventure-upvotes-action-icon ' + postId + '"></span>';
        var cancelChange = '<span class="glyphicon glyphicon-remove change-adventure-upvotes-action-icon ' + postId + '"></span>';
        $(this).closest('p').replaceWith(inputTag + '<div class="ok-cancel-icons">' + okChange + cancelChange + '</div>');

        $(document.body).on('click', '.glyphicon.glyphicon-ok.change-adventure-upvotes-action-icon.' + postId, function() {
            var upvotes = $('input[name="upvotes"]').val();
            changeUpvotes(postId, upvotes, parentDiv);
            alreadyFired = true;
        });

        $(document.body).on('click', '.glyphicon.glyphicon-remove.change-adventure-upvotes-action-icon.' + postId, function() {
            parentDiv.empty();
            parentDiv.append(pTag);
        });
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
    //Delete Adventure
    $(document.body).on('click', ".remove-my-adventure-link", function() {
        postID = $(this).attr('adventure-id');
        console.log("inside 1st click");
    });

    $(".adventure-remove-confirm").on("click", function() {
        if(postID.length > 0) {console.log('inside 2nd click');
            deleteAdventureFromAdminPage(postID);

        }
    });

    //-------------------------------------------------------------------
    //Delete Adventure Pictures
    var postID = "";
    $(document.body).on('click', ".remove-my-adventure-pictures", function() {
        postID = $(this).attr('adventure-id');
    });

    $(".adventure-pictures-remove-confirm").on("click", function() {
        if(postID.length > 0) {
            deletePictures(postID);
        }
    });

    //-------------------------------------------------------------------
    //Delete Adventure Comments
    $(document.body).on('click', ".remove-my-adventure-comments", function() {
        postID = $(this).attr('adventure-id');
    });

    $(".adventure-comments-remove-confirm").on("click", function() {
        if(postID.length > 0) {
            deleteAllComments(postID);
        }
    });

});

function deleteAdventureFromAdminPage(postID) {

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
                console.log("inside ajax success");
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

function deleteAllComments(postId){
    //Show processing modal
    $('#adventures-removing-loading-modal').modal({
        show: true,
        backdrop: 'static',
        keyboard: true
    });

    $.ajax({
        type: 'POST',
        url: 'admin.php?postid=' + postId,
        dataType: 'json'
    })
        .done(function (success) {
            $('tr[post-id=' + postId + ']').find('.adventure-comment-amount-row').text("0");

            //Hide processing modal
            $('#adventures-removing-loading-modal').modal('hide');

        })
        .fail(function (fail) {
            console.log(fail.responseText);
            console.log("Error happened while trying to remove Adventure Comments with PostID[" + postId + "]");
            $('#adventures-removing-loading-modal').modal('hide');
        });
}

function deletePictures(postId){
    //Show processing modal
    $('#adventures-removing-loading-modal').modal({
        show: true,
        backdrop: 'static',
        keyboard: true
    });

    $.ajax({
        type: 'POST',
        url: 'deletePictures.php?postid=' + postId,
        dataType: 'json'
    })
        .done(function (success) {
            $('tr[post-id=' + postId + ']').find('.adventure-pictures-row').text("0");

            //Hide processing modal
            $('#adventures-removing-loading-modal').modal('hide');

        })
        .fail(function (fail) {
            console.log(fail.responseText);
            console.log("Error happened while trying to remove Adventure Pictures with PostID[" + postId + "]");
            $('#adventures-removing-loading-modal').modal('hide');
        });
}

function changeUpvotes(postId, upvotes, parentDiv){
    //Show processing modal
    $('#adventure-upvotes-changing-loading-modal').modal({
        show: true,
        backdrop: 'static',
        keyboard: true
    });

    $.ajax({
        type: 'POST',
        url: 'admin.php?postid=' + postId + '&upvotes=' + upvotes,
        dataType: 'json'
    })
        .done(function (success) {

            var pTag = $("<p>", {class: 'text-primary'});
            pTag.append(upvotes);
            pTag.append($('<span>', {class:"glyphicon glyphicon-pencil change-adventure-upvotes-icon"}));
            parentDiv.empty();
            parentDiv.append(pTag);

            //Hide processing modal
            $('#adventure-upvotes-changing-loading-modal').modal('hide');

        })
       .fail(function (fail) {
            console.log(fail);
            console.log("Error happened while trying to upvote Adventure with PostID[" + postId + "] by [" + upvotes + "] amount");
            $('#adventure-upvotes-changing-loading-modal').modal('hide');
        });

}

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
            break;
        case 2:
            jsStringSorter(rows, 'title', doInvert);
            break;
        case 3:
            jsIntSorter(rows, 'upvotes', doInvert);
            break;
        case 4:
            jsStringSorter(rows, 'city-country', doInvert);
            break;
        case 5:
            jsStringSorter(rows, 'date-posted', doInvert);
            break;
        case 6:
            jsIntSorter(rows, 'comments', doInvert);
            break;
        case 7:
            jsIntSorter(rows, 'pictures', doInvert);
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