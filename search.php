<?php

require_once 'functions.php';

    try {
        $oConn = loginToDB();

        //Check for logged in
        $loggedIn = loggedIn();

        $searchedQuery = $_POST["query"];

        //Selects adventure data and orders it by the amount of matched keywords in a descending order
        $query = $oConn->prepare("SELECT a.*, COUNT(*) AS `MatchedKeywordAmount` FROM Adventures a INNER JOIN Keywords k ON a.PostID = k.PostID WHERE k.Keyword LIKE '%$searchedQuery%' GROUP BY k.PostID ORDER BY MatchedKeywordAmount DESC");
        $query->execute();
        $adventures = $query->fetchAll(PDO::FETCH_ASSOC);

        //Selects only users of type author or admin
        $query = $oConn->prepare("SELECT * FROM User WHERE (User.FirstName LIKE '%$searchedQuery%' OR User.LastName LIKE '%$searchedQuery%') AND (User.UserType REGEXP 'author|admin')");
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
