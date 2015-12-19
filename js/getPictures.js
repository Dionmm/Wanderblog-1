/**
 * Created by Sean on 12/8/2015.
 */
window.onload = function() {

    $("#preview").html("");
    var adventureId = PostID;
    var pictureCount = 0;

    $.ajax({
        type: "POST",
        url: 'picture.php',
        data: {adventureId: adventureId},
        dataType: 'json',
        success: function (data) {
            for (var key in data) {
                if(!data.error) {
                    var adventurePicture = data[key].Path;

                    pictureCount++;

                    var pictureId = "picture" + pictureCount;

                    var pictureRemoveId = "pictureRemove" + pictureCount;

                    var li = document.createElement("li");
                    $(li).addClass("col-lg-3 col-md-4 col-xs-6 thumb");
                    li.id = pictureId;

                    var liRemove = document.createElement("li");
                    $(liRemove).addClass("col-lg-3 col-md-4 col-xs-6 thumb");
                    liRemove.id = pictureRemoveId;

                    var img = document.createElement("img");
                    img.classList.add("img-thumbnail");
                    img.src = adventurePicture;

                    var preview = document.getElementById("preview");

                    if(preview){
                        preview.appendChild(li);
                        var newPicture = document.getElementById(pictureId);
                        newPicture.appendChild(img);
                    }

                    var previewRemove = document.getElementById("previewRemove");

                    if(previewRemove){
                        previewRemove.appendChild(liRemove);
                        var newPictureRemove = document.getElementById(pictureRemoveId);
                        newPictureRemove.appendChild(img);
                        var span = document.createElement("span");
                        span.classList.add("glyphicon");
                        span.classList.add("glyphicon-remove");
                        span.classList.add("removeImageIcon");
                        newPictureRemove.appendChild(span);

                    }
                }
                }
        }, error: function (error) {
            console.log("Error: " + JSON.stringify(error));
        }
    });
};

