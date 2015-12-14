<?php
/**
 * Created by PhpStorm.
 * User: Sean
 * Date: 12/8/2015
 * Time: 4:05 PM
 */
require_once 'functions.php'; //Grabs any extra functions
session_start();

try {
    $postId = $_POST['adventureId'];

    if($postId){

        $oConn = loginToDB();

        $pictures = array();

        $sql_query = "SELECT * FROM pictures WHERE PostId = '$postId'";

        $result = $oConn->query($sql_query);

        while($row = $result->fetch_array()){
            $pictures[] = $row["Path"];
        }

        echo json_encode($pictures);

    }else{
        echo 'Adventure not found';
    }
}catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
} finally{
    $oConn = null;
}