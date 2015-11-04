<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 04/11/2015
 * Time: 16:06
 */
function logged_in(){
    //Check for logged in
    session_start();
    $loggedIn = false;
    $first_name = '';
    $last_name = '';
    $user_group = '';
    if(isset($_SESSION['first_name'])){
        $loggedIn = true;
        $first_name = $_SESSION['first_name'];
        $last_name = $_SESSION['last_name'];
        $user_group = $_SESSION['user_group'];
    }


    return array(
        'loggedIn' => $loggedIn,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'user_group' => $user_group
    );
}