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
    $postID = $_POST['adventureId'];

    if ($postID) {

        $oConn = loginToDB();

        $pictures = array();

        $query = $oConn->prepare('SELECT FROM pictures WHERE PostID = :PostID');

        $query->bindValue(':PostID', $PostID, PDO::PARAM_STR);

        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($rows); $i++) {
            $pictures[] = $rows['Path'][$i];
        }

        echo json_encode($pictures, $rows);

    }else{
        echo 'Adventure not found';
    }
}catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
} finally{
    $oConn = null;
}