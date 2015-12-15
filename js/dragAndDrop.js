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
            li.classList.add("col-lg-2");
            li.classList.add("col-md-2");
            li.classList.add("col-sm-3");
            li.classList.add("col-xs-4");
            li.id = picture;

            var img = document.createElement("img");
            img.classList.add("img-thumbnail");
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



