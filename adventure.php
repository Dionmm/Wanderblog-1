<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 03/11/2015
 * Time: 18:20
 */

if(isset($_GET['id'])){
    $PostID = $_GET['id']; //grab the id for the post


    require_once 'config.php'; //Grabs the database details
    require_once 'functions.php'; //Grabs any extra functions
    //Create connection to database, query for username and verify password
    try {
        //Set persistent connection
        $oConn = new PDO('mysql:host='.$sHost.';dbname='.$sDb, $sUsername, $sPassword, array(
            PDO::ATTR_PERSISTENT => true
        ));
        $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT * FROM Adventures LEFT JOIN Comments ON Comments.PostID = Adventures.PostID WHERE Adventures.PostID = :PostID'); //Query for PostID and any comments it may have
        $query->bindValue(':PostID', $PostID, PDO::PARAM_STR);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_NAMED); //grab all values that match. FETCH_NAMED is used due to having multiple columns with the same name

        // If there are results
        if($rows){

            //Set adventure to the first item in array. A post with comments will return multiple items in the same array
            //Where as a post without comments will return just 1 item
            $adventure = $rows[0];
            //Check if there are comments on the post, if not set $comments to zero
            if($rows[0]['CommentID'] == !NULL){
                $comments = $rows;
            } else{
                $comments = 0;
            }

            //Check for logged in
            $loggedIn = logged_in();

            //Templating
            require_once 'vendor/autoload.php';
            $loader = new Twig_Loader_Filesystem('views');
            $twig = new Twig_environment($loader);
            $template = $twig->loadTemplate('adventure.html');

            //return the template specified above with the following variables filled in
            echo $template->render(array(
                'adventure' => $adventure,
                'comments' => $comments,
                'loggedIn' => $loggedIn['loggedIn'],
                'name' => $loggedIn['first_name']
            ));
        } else{
            echo 'Error: File not found';
        }


    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
}
