<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 25/10/2015
 * Time: 15:49
 */
require_once 'config.php'; //Grabs the database details

$usernamePOST = $_POST['username']; //grab the username from the post
$passwordPOST = $_POST['password']; //grab the password from the post
$emailPOST = $_POST['email']; //grab the username from the post
$firstNamePOST = $_POST['fName']; //grab the password from the post
$lastNamePOST = $_POST['lName']; //grab the username from the post
$countryPOST = $_POST['country']; //grab the password from the post

//Check if post was properly sent and contains data
if(isset($usernamePOST) && isset($passwordPOST) && isset($emailPOST) && isset($firstNamePOST) && isset($lastNamePOST) && isset($countryPOST)){

    $input_is_valid = validate_input($usernamePOST,$passwordPOST,$emailPOST,$firstNamePOST,$lastNamePOST,$countryPOST);

    if($input_is_valid === true){

        $hashPWord = password_hash($passwordPOST, PASSWORD_BCRYPT); //Hash and salt the password

        echo addToDB($usernamePOST,$hashPWord,$emailPOST,$firstNamePOST,$lastNamePOST,$countryPOST);

    } else{
        echo json_encode(array('error' => $input_is_valid));
    }

} else {
    $returnMessage = json_encode(array('error' => 'Server error, 500'));
    echo $returnMessage;
}

function validate_input($username,$password,$email,$first_name,$last_name,$country){
    if(strlen($username) < 4 ||  strlen($username) > 20 || !preg_match("/^[a-zA-Z_-]+$/",$username) || check_for_username($username)){ //ADD CHECK FOR DUPLICATE USERNAME
        return 'Username not available';
    } else if(strlen($password) < 8){
        return 'Password is not strong enough';
    } else if(!filter_var($email, FILTER_VALIDATE_EMAIL) ||  strlen($email) > 255){
        return 'Email address is not valid';
    } else if(!preg_match("/^[a-zA-Z '-]+$/",$first_name) || !preg_match("/^[a-zA-Z '-]+$/",$last_name) ||  strlen($first_name) > 35 ||  strlen($last_name) > 35){
        return 'Please ensure your name is spelled correctly';
    } else if(!preg_match("/^[a-zA-Z ()'-]+$/",$country) ||  strlen($country) > 85){
        return 'Invalid country selected';
    } else{
        return true;
    }
}

function check_for_username($username){

    global $sUsername, $sPassword, $sHost, $sDb;
    //Create connection to database, query for username and verify password
    try {
        //Set persistent connection
        $oConn = new PDO('mysql:host='.$sHost.';dbname='.$sDb, $sUsername, $sPassword, array(
            PDO::ATTR_PERSISTENT => true
        ));
        $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT * FROM User WHERE username = :username');
        $query->bindValue(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match

        if($rows){
            return true;
        } else{
            return false;
        }

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        return true;
    }
}


function addToDB($username,$password,$email,$first_name,$last_name,$country){

    global $sUsername, $sPassword, $sHost, $sDb;

    //Create connection to database, query for username and verify password
    try {
        //Set persistent connection
        $oConn = new PDO('mysql:host='.$sHost.';dbname='.$sDb, $sUsername, $sPassword, array(
            PDO::ATTR_PERSISTENT => true
        ));
        $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling


        //Prepare statement, substitute :username with username field input
        $addUser = $oConn->prepare("INSERT INTO User VALUES (:username, :password, 'unverified', :email, :fName, :lName, :country)");

        $addUser->bindValue(':username', $username, PDO::PARAM_STR);
        $addUser->bindValue(':password', $password, PDO::PARAM_STR);
        $addUser->bindValue(':email', $email, PDO::PARAM_STR);
        $addUser->bindValue(':fName', $first_name, PDO::PARAM_STR);
        $addUser->bindValue(':lName', $last_name, PDO::PARAM_STR);
        $addUser->bindValue(':country', $country, PDO::PARAM_STR);

        if($addUser->execute()){

            $success = 'successfully added user to database';

            session_start();
            $_SESSION['name'] = $first_name;
            $returnMessage = json_encode(array('success' => $success, 'name' => $_SESSION['name']));
        } else{
            $returnMessage = json_encode(array('error' => 'Failed to add to database'));
        }
        return $returnMessage;

    } catch(PDOException $e) {
        return 'ERROR: ' . $e->getMessage();
    }
}
