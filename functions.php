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

function addLike($postID)
{
    $loggedIn = logged_in();
    $username = $_SESSION['username'];
    if ($loggedIn['user_group'] >= 1) {
        $oConn = LoginToDB();

        $query = $oConn->prepare('SELECT * FROM Upvoted WHERE PostID = :postID AND Username = :username');
        $query->bindValue(':postID', $postID, PDO::PARAM_STR);
        $query->bindValue(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match

        if ($rows) {
            $update = $oConn->prepare('UPDATE Adventures SET Upvotes = Upvotes-1 WHERE PostID = :postID;DELETE FROM Upvoted WHERE PostID = :postID AND Username = :username');
            $update->bindValue(':postID', $postID, PDO::PARAM_STR);
            $update->bindValue(':username', $username, PDO::PARAM_STR);

            if ($update->execute()) {
                return json_encode(array('success' => 'Upvote removed'));
            } else {
                return json_encode(array('error' => 'Something went wrong'));

            }
        } else {
            $update = $oConn->prepare('UPDATE Adventures SET Upvotes = Upvotes+1 WHERE PostID = :postID;INSERT INTO Upvoted VALUES(NULL, :postID, :username)');
            $update->bindValue(':postID', $postID, PDO::PARAM_STR);
            $update->bindValue(':username', $username, PDO::PARAM_STR);

            if ($update->execute()) {
                return json_encode(array('success' => 'Upvote added'));
            } else {
                return json_encode(array('error' => 'Something went wrong'));

            }
        }

    } else {
        return json_encode(array('error' => 'Do not have permission to vote'));
    }
}