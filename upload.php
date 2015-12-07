<?php
require_once 'functions.php'; //Grabs any extra functions

$PostID = $_GET['id'];

    if($PostID){
        //Check for logged in
        $loggedIn = loggedIn();

        if($loggedIn['user_group'] > 1){

            try {

                $oConn = loginToDB();

                $uploadDir = 'uploads\\' . $PostID . '\\';

                if(!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                for($i=0; $i<count($_FILES['files']['name']); $i++) {

                    $file = $_FILES["files"]["tmp_name"][$i];

                    //Check file is an image
                    $imageSizeData = getimagesize($file);
                    if ($imageSizeData === FALSE) {
                        //not an image and don't add to database
                    } else {
                        $fileName = $_FILES["files"]["name"][$i];
                        $tmpName = $_FILES["files"]["tmp_name"][$i];

                        $ext = substr(strrchr($fileName, "."), 1);
                        $randName = md5(rand() * time()); //could use sha256

                        $filePath = $uploadDir . $randName . '.' . $ext;

                        $result = move_uploaded_file($tmpName, $filePath);

                        if ($result) {
                            $imageName = addslashes($fileName);

                            $imagePath = addslashes($filePath);

                            $savePicture = $oConn->prepare("INSERT INTO Pictures VALUES (NULL, $PostID, '$imagePath', NOW())");
                        }
                        if ($savePicture->execute()) {
                            //Picture added to database
                        }

                        echo $returnMessage;
                    }
                }
            }catch(PDOException $e){
                echo 'ERROR: ' . $e->getMessage();

            }finally{
                $oConn = null;
            }
        }else {
            echo '500, Couldn\'t save'; //return server error
        }
    }else{
        echo 'No adventure created';
    }