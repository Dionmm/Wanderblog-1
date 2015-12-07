<?php
require_once 'functions.php'; //Grabs any extra functions

$adventure = $_POST["adventure"];

if($adventure){
    //Check for logged in
    $loggedIn = loggedIn();

    if($loggedIn['user_group'] > 1){

        try {

            $oConn = loginToDB();

            $sql_query_images = "SELECT * FROM pictures WHERE PostID = '$adventure'";

            $result = $oConn->query($sql_query_images);

            $pictureDetail = array();

            while($imageRow = $result->fetch_array()){

                $pictureDetail[] = $imageRow["Path"];
            }

            echo json_encode($pictureDetail);

        }catch(PDOException $e){
            echo 'ERROR: ' . $e->getMessage();

        }finally{
            $oConn = null;
        }
    }else {
        echo '500, Couldn\'t retrieve images'; //return server error
    }
}else{
    echo 'No adventure selected';
}
