$(document).ready(function(){

    var qrStr = window.location.search;

    if (qrStr) {
        qrStr = qrStr.split("?")[1].split("=")[1];

        $("#preview").html("");

        var adventureId = qrStr;
        var pictureCount = 0;

        $.ajax({
            type: "POST",
            url: 'getPictures.php',
            data: {adventure:adventureId},
            dataType: 'json',
            success: function(data){
                for(var key in data) {

                    var adventurePicture = data[key];

                    pictureCount++;

                    var pictureId = "picture" + pictureCount;

                    var li = document.createElement("li");
                    li.classList.add("col-lg-2");
                    li.classList.add("col-md-2");
                    li.classList.add("col-sm-3");
                    li.classList.add("col-xs-4");
                    li.id = pictureId

                    var img = document.createElement("img");
                    img.classList.add("img-thumbnail");
                    img.src = adventurePicture;

                    var preview = document.getElementById("preview");
                    preview.appendChild(li); // Assuming that "preview" is the div output where the content will be displayed.

                    var newPicture = document.getElementById(pictureId)
                    newPicture.appendChild(img);
                }
            },error: function(error){
                console.log("Error: " + JSON.stringify(error));
            }
        });
    }
});
