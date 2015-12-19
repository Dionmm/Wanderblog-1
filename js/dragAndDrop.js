$(document).ready(function(){

    $("input[type=file]").on('change',function(){
        handleFiles(this.files);
    });

    function handleFiles(files){

        document.getElementById("previewEdit").innerHTML = "";

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var imageType = /^image\//;

            if (!imageType.test(file.type)) {
                continue;
            }

            var picture  = "picture" + i;
            var li = document.createElement("li");
            $(li).addClass("col-lg-3 col-md-4 col-xs-6 thumb");
            li.id = picture;

            var img = document.createElement("img");
            $(img).addClass("img-thumbnail");
            img.file = file;

            var preview = document.getElementById("previewEdit");
            preview.appendChild(li); // Assuming that "preview" is the div output where the content will be displayed.

            var newPicture = document.getElementById(picture)
            newPicture.appendChild(img);

            var reader = new FileReader();
            reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
            reader.readAsDataURL(file);
        }
    }

    $('body').on('click','img',function(){
        if($(this).attr('id') != 'logo'){
            $('#imagepreview').attr('src', $(this).attr('src'));
            $('#imagemodal').modal('show');
        }
    });
});



