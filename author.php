<?php

require_once 'functions.php';

    try {

        $loggedIn = loggedIn();

        if ($loggedIn['loggedIn']) {

            $oConn = loginToDB();

            $author = $_GET["username"];

            //Prepare statement, substitute keyword with the submitted query
            $query = $oConn->prepare("SELECT Username, UserType, FirstName, LastName, Country FROM User WHERE Username = '$author'");
            $query->execute();
            $user = $query->fetchAll(PDO::FETCH_ASSOC);

            //Prepare statement, substitute keyword with the submitted query
            $query = $oConn->prepare("SELECT PostID, Title, Upvotes, City, Country, DatePosted FROM Adventures WHERE Username = '$author' ORDER BY DatePosted");
            $query->execute();
            $adventures = $query->fetchAll(PDO::FETCH_ASSOC);

            //Templating
            require_once 'vendor/autoload.php';
            $loader = new Twig_Loader_Filesystem('views');
            $twig = new Twig_environment($loader);
            $template = $twig->loadTemplate('author.twig');

            //Return the template specified above with the following variables filled in
            echo $template->render(array(
                'user' => $user[0],
                'adventures' => $adventures,
                'loggedIn' => $loggedIn
            ));
        }

        else{
            //Templating
            require_once 'vendor/autoload.php';
            $loader = new Twig_Loader_Filesystem('views');
            $twig = new Twig_environment($loader);
            $template = $twig->loadTemplate('author.twig');

            //Return the template specified above with the following variables filled in
            echo $template->render(array(
                'loggedIn' => $loggedIn
            ));
        }

    } catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
