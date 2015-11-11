<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 25/10/2015
 * Time: 15:49
 */

//Check if post was properly sent and contains data
if (isset($_POST['username'], $_POST['password'], $_POST['email'], $_POST['fName'], $_POST['lName'], $_POST['country'])) {

    $usernamePOST = $_POST['username']; //grab the username from the post
    $passwordPOST = $_POST['password']; //grab the password from the post
    $emailPOST = $_POST['email']; //grab the email from the post
    $firstNamePOST = $_POST['fName']; //grab the first name from the post
    $lastNamePOST = $_POST['lName']; //grab the last name from the post
    $countryPOST = $_POST['country']; //grab the country from the post

    //Validate the user's input
    $input_is_valid = validate_input($usernamePOST,$passwordPOST,$emailPOST,$firstNamePOST,$lastNamePOST,$countryPOST);

    //If it returns true then hash password and add user to DB
    if($input_is_valid === true){

        $hashPWord = password_hash($passwordPOST, PASSWORD_BCRYPT, array(
            'cost' => 13
        )); //Hash and salt the password

        echo addToDB($usernamePOST,$hashPWord,$emailPOST,$firstNamePOST,$lastNamePOST,$countryPOST);

    } else { //Return the error
        echo json_encode(array('error' => $input_is_valid));
    }

} else {
    $returnMessage = json_encode(array('error' => 'Server error, 500'));
    echo $returnMessage;
}

function validate_input($username,$password,$email,$first_name,$last_name,$country){
    //Username is longer than 4 characters, less than 20 characters, matches a set of permitted characters and does not already exist
    if (strlen($username) < 4 || strlen($username) > 20 || !preg_match("/^[a-zA-Z0-9_-]+$/", $username) || check_for_username($username)) { //ADD CHECK FOR DUPLICATE USERNAME
        return 'Username not available';
    } else if (strlen($password) < 8) { //Password is longer than 8 characters
        return 'Password is not strong enough';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) { //Email address is valid and no longer than 255 characters (filer_var already accounts for this but just in case)
        return 'Email address is not valid';
    } else if (!preg_match("/^[a-zA-Z '-]+$/", $first_name) || !preg_match("/^[a-zA-Z '-]+$/", $last_name) || strlen($first_name) > 35 || strlen($last_name) > 35) { //First name and last name only contain permitted characters
        return 'Please ensure your name is spelled correctly';                                                                                                     //and are no longer than 35 characters each
    } else if (!preg_match("/^[a-zA-Z ()'-]+$/", $country) || strlen($country) > 85) { //Country contains only permitted characters and is no longer than 85 characters
        return 'Invalid country selected';
    } else{
        //All input is VALID
        return true;
    }
}

function check_for_username($username){

    //Create connection to database, query for username and verify password
    try {
        //login to DB
        $oConn = LoginToDB();

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


    //Create connection to database, query for username and verify password
    try {
        //login to DB
        $oConn = LoginToDB();

        //Prepare statement
        $addUser = $oConn->prepare("INSERT INTO User VALUES (:username, :password, 'unverified', :email, :fName, :lName, :country)");

        //Bind values to the prepared statement
        $addUser->bindValue(':username', $username, PDO::PARAM_STR);
        $addUser->bindValue(':password', $password, PDO::PARAM_STR);
        $addUser->bindValue(':email', $email, PDO::PARAM_STR);
        $addUser->bindValue(':fName', $first_name, PDO::PARAM_STR);
        $addUser->bindValue(':lName', $last_name, PDO::PARAM_STR);
        $addUser->bindValue(':country', $country, PDO::PARAM_STR);

        //If successfully executed return success message and create new session
        if($addUser->execute()){

            $success = 'successfully added user to database';

            session_start();
            $_SESSION['username'] = $username; //set session variable
            $_SESSION['first_name'] = $first_name; //set session variable
            $_SESSION['last_name'] = $last_name; //set session variable
            $_SESSION['email'] = $email; //set session variable
            $_SESSION['user_group'] = 'unverified'; //set session variable
            $_SESSION['country'] = $country; //set session variable
            $returnMessage = json_encode(array('success' => $success, 'name' => $_SESSION['first_name']));
        } else{
            $returnMessage = json_encode(array('error' => 'Failed to add to database'));
        }
        return $returnMessage;

    } catch(PDOException $e) {
        return 'ERROR: ' . $e->getMessage();
    }
}
