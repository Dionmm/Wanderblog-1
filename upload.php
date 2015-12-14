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

            for($i=0; $i<count($_FILES['files']['name']); $i++) {
                $file = $_FILES["files"]["tmp_name"][$i];

                //Check file is an image
                $imageSizeData = getimagesize($file);
                if ($imageSizeData === FALSE)
                {
                    //not an image and don't add to database
                }else {
                    $fileName = $_FILES["files"]["name"][$i];
                    $tmpName = $_FILES["files"]["tmp_name"][$i];

                    $ext = substr(strrchr($fileName, "."), 1);

                    $randName = md5(rand() * time()); //could use sha256

                    $filePath = $uploadDir . $randName . '.' . $ext;

                    $result = move_uploaded_file($tmpName, $filePath);

                    if($result){

                        //$oConn = loginToDB();

                        //$imageName = addslashes($fileName);

                        //$imagePath = addslashes($filePath);

                        //$sql_query = "INSERT INTO pictures VALUES(NULL,'$postID','$imageName','$imagePath', NOW())";

                        //if ($result = $oConn->query($sql_query)) {
                            //Picture added successfully
                        //}
                    }
                }
            }
        }else{
            echo 'Adventure not found';
        }
    }catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    } finally{
        $oConn = null;
    }
    }





