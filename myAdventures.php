<?php

require_once 'functions.php';

    try {

        $loggedIn = loggedIn();

        $author = $_GET["username"];

        $adventures = [];

        $wrongUser = true;

        if($loggedIn['username'] == $author){

            $oConn = loginToDB();

            //Get adventure data + comment amount for each adventure via subquery
            $query = $oConn->prepare("SELECT a.*, (SELECT COUNT(*) FROM Comments WHERE PostID = a.PostID) AS CommentAmount FROM Adventures a WHERE a.Username = '$author' ORDER BY a.DatePosted ASC");
            $query->execute();
            $adventures = $query->fetchAll(PDO::FETCH_ASSOC);

            $wrongUser = false;
        }

        //Templating
        require_once 'vendor/autoload.php';
        $loader = new Twig_Loader_Filesystem('views');
        $twig = new Twig_environment($loader);
        $template = $twig->loadTemplate('myAdventures.twig');

        //Return the template specified above with the following variables filled in
        echo $template->render(array(
            'authorUsername' => $author,
            'adventures' => $adventures,
            'loggedIn' => $loggedIn,
            'wrongUser' => $wrongUser
        ));

    } catch(PDOException $e){
        echo 'ERROR: ' . $e->getMessage();
    }
    finally{
        $oConn = null;
    }
