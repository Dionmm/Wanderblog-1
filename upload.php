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
        $postId = $_POST['postId'];

        if($postId){
            $uploadDir = 'uploads\\' . $postId . '\\';

            if(!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            for ($i = 0; $i < count($_POST['files']); $i++) {
                $file = $_FILES["files"]["tmp_name"][$i];
                //Check file is an image
                $imageSizeData = getimagesize($file[$i]);
                if ($imageSizeData === FALSE)
                {
                    //not an image and don't add to database
                }else {
                    $fileName = $_FILES["files"][$i]["name"];
                    $tmpName = $_FILES["files"][$i]["tmp_name"];

                    $ext = substr(strrchr($fileName, "."), 1);

                    $randName = md5(rand() * time()); //could use sha256

                    $filePath = $uploadDir . $randName . '.' . $ext;

                    $result = move_uploaded_file($tmpName, $filePath);

                    if($result){

                        $imageName = addslashes($fileName);

                        $imagePath = addslashes($filePath);

                        $oConn = loginToDB();

                        $query = $oConn->prepare('INSERT INTO pictures VALUES(NULL, :PostID, :imagename, :path, NOW())'); //Prepare query to check for existing postID

                        $saveAdventure->bindValue(':PostID', $PostID, PDO::PARAM_STR);
                        $saveAdventure->bindValue(':imagename', imageName, PDO::PARAM_STR);
                        $saveAdventure->bindValue(':path', imagePath, PDO::PARAM_STR);

                        if ($saveAdventure->execute()) {
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





