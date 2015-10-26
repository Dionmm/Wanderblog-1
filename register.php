<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 25/10/2015
 * Time: 15:49
 */

$uname = $_POST['username']; //grab the username from the post
$pword = $_POST['password']; //grab the password from the post

//Check if post was properly sent and contains data
if(isset($uname) && isset($pword) && !empty($uname) && !empty($pword)){

    require_once 'config.php'; //Grabs the database details

    //Create connection to database, query for username and verify password
    try {
        //Set persistent connection
        $oConn = new PDO('mysql:host='.$sHost.';dbname='.$sDb, $sUsername, $sPassword, array(
            PDO::ATTR_PERSISTENT => true
        ));
        $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling

        $hashPWord = password_hash($pword, PASSWORD_BCRYPT);

        //Prepare statement, substitute :username with username field input
        $addUser = $oConn->prepare('INSERT INTO users VALUES (:username, :password)');

        $addUser->bindValue(':username', $uname, PDO::PARAM_STR);
        $addUser->bindValue(':password', $hashPWord, PDO::PARAM_STR);

        if($addUser->execute()){

            $success = 'success';

            session_start();
            $_SESSION['name'] = 'Dion';
            $returnMessage = json_encode(array('success' => $success, 'name' => $_SESSION['name']));
        } else{
            $returnMessage = 'Failed';
        }
        echo $returnMessage;

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }


} else {
    echo '500'; //return server error
}