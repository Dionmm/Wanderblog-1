<?php
/**
 * Created by PhpStorm.
 * User: Sean
 * Date: 12/8/2015
 * Time: 3:20 PM
 */
require_once 'functions.php'; //Grabs any extra functions
session_start();

//Check for logged in
$loggedIn = loggedIn();

if($loggedIn['user_group'] > 1){

    try {
        $postID = $_POST['postId'];

        if ($postID) {
            $uploadDir = 'uploads\\' . $postID . '\\';

            if(!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
                $file = $_FILES["files"]["tmp_name"][$i];
                echo 'Looped';
                //Check file is an image
                $imageSizeData = getimagesize($file);
                if ($imageSizeData === FALSE)
                {
                    echo 'False';
                    //not an image and don't add to database
                }else {
                    echo 'true';
                    $fileName = $_FILES["files"]["name"][$i];
                    $tmpName = $_FILES["files"]["tmp_name"][$i];

                    $ext = substr(strrchr($fileName, "."), 1);

                    $randName = md5(rand() * time()); //could use sha256

                    $filePath = $uploadDir . $randName . '.' . $ext;

                    $result = move_uploaded_file($tmpName, $filePath);

                    if($result){
                        echo 'DB';

                        $imageName = addslashes($fileName);

                        $imagePath = addslashes($filePath);

                        $oConn = loginToDB();

                        $savePicture = $oConn->prepare('INSERT INTO pictures VALUES(NULL, :PostID, :imagename, :path, NOW())'); //Prepare query to check for existing postID

                        $savePicture->bindValue(':PostID', $PostID, PDO::PARAM_STR);
                        $savePicture->bindValue(':imagename', $imageName, PDO::PARAM_STR);
                        $savePicture->bindValue(':path', $imagePath, PDO::PARAM_STR);

                        if ($savePicture->execute()) {
//                            Picture added successfully
                            echo 'Success';
                        }
                    }
                }
            }
            echo 'finished';
        }else{
            echo 'Adventure not found';
        }
    }catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    } finally{
        $oConn = null;
    }
    }





