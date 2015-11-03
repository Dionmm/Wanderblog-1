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

    //Create connection to database, query for username and verify password
    try {
        //Set persistent connection
        $oConn = new PDO('mysql:host='.$sHost.';dbname='.$sDb, $sUsername, $sPassword, array(
            PDO::ATTR_PERSISTENT => true
        ));
        $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT * FROM adventures WHERE PostID = :PostID');
        $query->bindValue(':PostID', $PostID, PDO::PARAM_STR);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match

        if($rows){

            $adventure = $rows[0];

            //Check for logged in
            session_start();
            $loggedIn = false;
            $name = '';
            if(isset($_SESSION['name'])){
                $loggedIn = true;
                $name = $_SESSION['name'];
            }


            //Templating
            require_once 'vendor/autoload.php';

            $loader = new Twig_Loader_Filesystem('views');
            $twig = new Twig_environment($loader);
            $template = $twig->loadTemplate('adventure.html');

            echo $template->render(array(
                'adventure' => $adventure,
                'loggedIn' => $loggedIn,
                'name' => $name
            ));
        } else{
            echo 'Error: File not found';
        }


    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
}
