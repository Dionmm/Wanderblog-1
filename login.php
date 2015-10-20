<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 19/10/2015
 * Time: 21:59
 */
$uname = $_POST['username'];
$pword = $_POST['password'];

$testUName = 'dionmm';
$testPWord = 'password';

if(isset($uname) && isset($pword) && !empty($uname) && !empty($pword)){
    if($uname == $testUName && $pword == $testPWord){
        session_start();
        $_SESSION['name'] = 'Dion';
        $success = 'success';
        echo json_encode(array('success' => $success, 'name' => $_SESSION['name']));
    } else{
        echo '503';
    }
} else{
    echo '500';
}









//
//
//
//
//
//session_start();
//
//$_SESSION['username'] = 'Dion';
//
//echo $_SESSION['username'];