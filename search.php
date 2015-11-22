<?php

require_once 'functions.php';

    try {
        $oConn = loginToDB();

        //Check for logged in
        $loggedIn = loggedIn();

        $searchedQuery = $_POST["query"];

        //Prepare statement, substitute keyword with the submitted query
        $query = $oConn->prepare("SELECT a.* FROM Adventures a, Keywords k WHERE a.PostID = k.PostID AND k.Keyword LIKE '%$searchedQuery%'");
        $query->execute();
        $adventures = $query->fetchAll(PDO::FETCH_ASSOC);

        $query = $oConn->prepare("SELECT * FROM User WHERE User.FirstName LIKE '%$searchedQuery%' OR User.LastName LIKE '%$searchedQuery%'");
        $query->execute();
        $authors = $query->fetchAll(PDO::FETCH_ASSOC);

        //Templating
        require_once 'vendor/autoload.php';
        $loader = new Twig_Loader_Filesystem('views');
        $twig = new Twig_environment($loader);
        $template = $twig->loadTemplate('search.twig');

        //Return the template specified above with the following variables filled in
        echo $template->render(array(
            'query' => $searchedQuery,
            'adventures' => $adventures,
            'authors' => $authors,
            'loggedIn' => $loggedIn
        ));

    } catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
