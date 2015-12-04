<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 19/10/2015
 * Time: 21:59
 */
require_once 'functions.php';

//Check if post was properly sent and contains data
if(isset($_POST['username'], $_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])){

    $username = $_POST['username']; //grab the username from the post
    $password = $_POST['password']; //grab the password from the post

    //Create connection to database, query for username and verify password
    try {
        //login to DB
        $oConn = loginToDB();

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT * FROM User WHERE username = :username');
        $query->bindValue(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match

        if ($rows && password_verify($password, $rows[0]['Password'])) { //Verify the passwords match
            session_start(); //create a session
            $_SESSION['username'] = $rows[0]['Username']; //set session variable
            $_SESSION['first_name'] = $rows[0]['FirstName']; //set session variable
            $_SESSION['last_name'] = $rows[0]['LastName']; //set session variable
            $_SESSION['email'] = $rows[0]['Email']; //set session variable
            $_SESSION['user_group'] = $rows[0]['UserType']; //set session variable
            $_SESSION['country'] = $rows[0]['Country']; //set session variable
            $success = 'success'; //set success message
            $returnMessage = json_encode(array('success' => $success));
        } else{
            $returnMessage = json_encode(array('error' => 'Username and/or password are incorrect')); //return forbidden error
        }
        echo $returnMessage; //return

    } catch(PDOException $e){
        echo 'ERROR: ' . $e->getMessage();
    }
    finally{
        $oConn = null;
    }


} else{
    echo '500'; //return server error
}