<?php

require_once 'functions.php';

    try {
        $oConn = loginToDB(); //Login to DB (Functions.php)

        $searchedQuery = $_POST["query"];

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare("SELECT a.* FROM Adventures a, Keywords k WHERE a.PostID = k.PostID AND k.Keyword = '$searchedQuery'");
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        //Templating
        require_once 'vendor/autoload.php';
        $loader = new Twig_Loader_Filesystem('views');
        $twig = new Twig_environment($loader);
        $template = $twig->loadTemplate('search.html');

        //Return the template specified above with the following variables filled in
        echo $template->render(array(
            'query' => $searchedQuery,
            'adventures' => $rows
        ));

    } catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
