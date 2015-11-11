<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 04/11/2015
 * Time: 16:06
 */
function logged_in(){
    //Initiate session and variables
    session_start();
    $loggedIn = false;
    $first_name = '';
    $last_name = '';
    $username = '';
    $user_group = '';

    //Check if logged in by checking session variable
    if(isset($_SESSION['first_name'])){
        //Set logged in to true and fill session variables
        $loggedIn = true;
        $first_name = $_SESSION['first_name'];
        $last_name = $_SESSION['last_name'];
        $user_group = $_SESSION['user_group'];
        $username = $_SESSION['username'];
    }

    //Check which user group the user belongs to and give corresponding number
    switch ($user_group) {
        case 'admin':
            $user_group = 3;
            break;
        case 'author':
            $user_group = 2;
            break;
        case 'reader':
            $user_group = 1;
            break;
        default:
            $user_group = 0;
    }


    return array(
        'loggedIn' => $loggedIn,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'user_group' => $user_group,
        'username' => $username
    );
}

function LoginToDB()
{
    require 'config.php';

    //Set persistent connection
    $oConn = new PDO('mysql:host=' . $sHost . ';dbname=' . $sDb, $sUsername, $sPassword, array(
        PDO::ATTR_PERSISTENT => true
    ));
    $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling
    return $oConn;
}