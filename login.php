<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 19/10/2015
 * Time: 21:59
 */
$uname = $_POST['username']; //grab the username from the post
$pword = $_POST['password']; //grab the password from the post

$testUName = 'dionmm'; //Temp parameters, should
$testPWord = 'password';//connect to Database here

//Check if post was properly sent and contains data
if(isset($uname) && isset($pword) && !empty($uname) && !empty($pword)){

    if($uname == $testUName && $pword == $testPWord){ //test for username/password combination
        session_start(); //create a session
        $_SESSION['name'] = 'Dion'; //set session variable 'name'
        $success = 'success'; //set success message
        echo json_encode(array('success' => $success, 'name' => $_SESSION['name'])); //return json data
    } else{
        echo '503'; //return forbidden error
    }

} else{
    echo '500'; //return server error
}