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

            if(file_exists($d)){
                echo "file found";
            }else{
                echo "file not found";
            }
        }
    }catch(PDOException $e){
        echo 'ERROR: ' . $e->getMessage();
    }finally{
        $oConn = null;
    }
}