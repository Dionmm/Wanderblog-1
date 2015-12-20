<?php
/**
 * Created by PhpStorm.
 * User: Sean
 * Date: 12/15/2015
 * Time: 10:09 PM
 */
require_once 'functions.php'; //Grabs any extra functions

if(isset($_POST['data'])){
    deletePictures($_POST['data']);
} else if(isset($_GET['postid'])){
    deleteAllPictures($_GET['postid']);
}

function deletePictures($data){

    $data = json_decode($_POST['data']);

    if($data){
        try{

            $loggedIn = loggedIn();

            $oConn = loginToDB();

            if ($loggedIn['user_group'] > 1) {
                //Gets all the pictures of the logged in author
                $query = $oConn->prepare("SELECT * FROM (SELECT p.*, a.Username FROM Adventures a, Pictures p WHERE a.PostID = p.PostID AND a.Username = :username) AS AdventureAuthorUsername;");
                $query->bindValue(':username', $loggedIn['username'], PDO::PARAM_STR);
                $query->execute();
                $pictures = $query->fetchAll(PDO::FETCH_ASSOC);
            }

            foreach ($data as $d) {
                if ($loggedIn['user_group'] === 3){
                    if(in_array($d, $pictures)){
                        //Code to remove pic or pics
                    }
                }
                //If file exists on server
                if (file_exists($d)) {
                    //remove the file
                    unlink($d);
                    echo "Picture deleted from server...";

                    //query to remove from database
                    $query = $oConn->prepare("DELETE FROM Pictures WHERE Pictures.Path = :path");
                    $query->bindValue(':path', $d, PDO::PARAM_STR);

                    if ($query->execute()) {
                        echo "Picture deleted from database...";
                    }
                } else {
                    echo "File not found";
                }
            }

        }catch(PDOException $e){
            echo 'ERROR: ' . $e->getMessage();
        }finally{
            $oConn = null;
        }
    }else{
        echo "No data";
    }
}

function deleteAllPictures($postId){
    $loggedIn = loggedIn();

    $oConn = loginToDB();

    try{
        if($loggedIn['user_group'] === 3){
            $query = $oConn->prepare("SELECT Path FROM Pictures WHERE PostID = :postId");
            $query->bindValue(':postId', $postId);
            $query->execute();
            $picturePaths = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach($picturePaths as $key => $p){
                unlink($picturePaths[$key]['Path']);
            }

            $query = $oConn->prepare("DELETE FROM Pictures WHERE PostID = :postId");
            $query->bindValue(':postId', $postId);
            $query->execute();
            echo json_encode(array('success'));
        }
    }
    catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        echo json_encode(array('fail'));
    }
    finally{
        $oConn = null;
    }
}