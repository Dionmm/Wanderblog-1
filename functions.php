<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 04/11/2015
 * Time: 16:06
 */
function loggedIn(){
    //Initiate session if not started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    //Initiate variables
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

function loginToDB()
{
    require 'config.php';

    //Set persistent connection
    $oConn = new PDO('mysql:host=' . $sHost . ';dbname=' . $sDb, $sUsername, $sPassword);
    $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling
    return $oConn;
}

function addLike($postID)
{
    try{
        $loggedIn = loggedIn();

        $username = $_SESSION['username'];

        if ($loggedIn['user_group'] >= 1) {
            $oConn = loginToDB();

            //Check if the user is the author of the article
            $query = $oConn->prepare('SELECT Username FROM Adventures WHERE PostID = :postID AND Username = :username');
            $query->bindValue(':postID', $postID, PDO::PARAM_STR);
            $query->bindValue(':username', $username, PDO::PARAM_STR);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            if (!$results || $loggedIn['user_group'] = 3) {
                //Check if the user has previously upvoted the post
                $query = $oConn->prepare('SELECT * FROM Upvoted WHERE PostID = :postID AND Username = :username');
                $query->bindValue(':postID', $postID, PDO::PARAM_STR);
                $query->bindValue(':username', $username, PDO::PARAM_STR);
                $query->execute();
                $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match

                //If has previous upvote, remove upvote, else add upvote
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
                return json_encode(array('error' => 'Cannot like own adventure'));

            }

        } else {
            return json_encode(array('error' => 'Do not have permission to vote'));
        }
    } catch(PDOException $e){
        echo 'ERROR: ' . $e->getMessage();
    }
    finally{
        $oConn = null;
    }

}

//Fix encoding problems
//converts non UTF-8 strings to UTF-8
function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}