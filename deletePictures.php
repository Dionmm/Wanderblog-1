<?php
/**
 * Created by PhpStorm.
 * User: Sean
 * Date: 12/15/2015
 * Time: 10:09 PM
 */
require_once 'functions.php'; //Grabs any extra functions

$data = json_decode(stripslashes($_POST['data']));

if($data){
    try{
        $oConn = loginToDB();

        foreach($data as $d){

            //If file exists on server
            if(file_exists($d)){
                //remove the file
                unlink($d);
                echo "Picture deleted from server";

                //query to remove from database
                //Prepare statement, substitute :username with username field input
                $query = $oConn->prepare("DELETE FROM pictures WHERE pictures.Path = '$d'");
                if($query->execute()){
                    echo "Picture deleted from database";
                }
            }else{
                echo "file not found";
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