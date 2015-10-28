<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 19/10/2015
 * Time: 21:59
 */
$uname = $_POST['username']; //grab the username from the post
$pword = $_POST['password']; //grab the password from the post

//Check if post was properly sent and contains data
if(isset($uname) && isset($pword) && !empty($uname) && !empty($pword)){

//To be used later
//    $hashPWord = password_hash('password', PASSWORD_BCRYPT);

    require_once 'config.php'; //Grabs the database details

    //Create connection to database, query for username and verify password
    try {
        //Set persistent connection
        $oConn = new PDO('mysql:host='.$sHost.';dbname='.$sDb, $sUsername, $sPassword, array(
            PDO::ATTR_PERSISTENT => true
        ));
        $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT * FROM User WHERE username = :username');
        $query->bindValue(':username', $uname, PDO::PARAM_STR);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match

        if(password_verify($pword, $rows[0]['Password'])){ //Verify the passwords match
            session_start(); //create a session
            $_SESSION['name'] = $rows[0]['FirstName']; //set session variable 'name'
            $success = 'success'; //set success message
            $returnMessage = json_encode(array('success' => $success, 'name' => $_SESSION['name']));
        } else{
            $returnMessage = '503 Forbidden'; //return forbidden error
        }
        echo $returnMessage; //return

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }


} else{
    echo '500'; //return server error
}