<?php

require_once 'functions.php';

try {
    $oConn = loginToDB();

    //Check for logged in
    $loggedIn = loggedIn();

    //Prepare it for the '%searchedQuery%' LIKE query type
    $searchedQuery = '%' . $_POST["query"] . '%';

    //Selects adventure data + picture path and orders them by the amount of matched keywords in a descending order
    $query = $oConn->prepare("SELECT a.*, p.Path, ak.MatchedKeywordAmount FROM (SELECT a.*, COUNT(*) AS `MatchedKeywordAmount` FROM Adventures a INNER JOIN Keywords k ON a.PostID = k.PostID WHERE k.Keyword LIKE :searchedQuery GROUP BY k.PostID) as ak RIGHT OUTER JOIN Adventures a ON a.PostID = ak.PostID LEFT JOIN Pictures p ON a.PostID = p.PostID WHERE (a.Title LIKE :searchedQuery2) OR (ak.MatchedKeywordAmount IS NOT NULL) GROUP BY a.PostID ORDER BY ak.MatchedKeywordAmount DESC");
    $query->bindValue(':searchedQuery', $searchedQuery, PDO::PARAM_STR);
    $query->bindValue(':searchedQuery2', $searchedQuery, PDO::PARAM_STR);
    $query->execute();
    $adventures = $query->fetchAll(PDO::FETCH_ASSOC);

    //Selects only users of type author or admin
    $query = $oConn->prepare("SELECT * FROM User WHERE (User.FirstName LIKE :searchedQuery OR User.LastName LIKE :searchedQuery2 OR User.Username LIKE :searchedQuery3) AND (User.UserType REGEXP 'author|admin')");
    $query->bindValue(':searchedQuery', $searchedQuery, PDO::PARAM_STR);
    $query->bindValue(':searchedQuery2', $searchedQuery, PDO::PARAM_STR);
    $query->bindValue(':searchedQuery3', $searchedQuery, PDO::PARAM_STR);
    $query->execute();
    $authors = $query->fetchAll(PDO::FETCH_ASSOC);

    //Check if adventure content has HTML tags and strip them away if so
    //Also checks if adventure has an image - else appends random one
    foreach ($adventures as $key => $adventure) {
        //$adventures[$key]['Content'] = strip_tags($adventure['Content']);
        if ($adventure['Path'] === NULL) {
            $randINT = rand(300, 500);
            $adventures[$key]['Path'] = 'http://lorempixel.com/' . $randINT . '/' . $randINT . '/nature/';
        }
        //print_r($adventures);
    }

    //Templating
    require_once 'vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('views');
    $twig = new Twig_environment($loader);
    $template = $twig->loadTemplate('search.twig');

    //Return the template specified above with the following variables filled in
    echo $template->render(array(
        'query' => $_POST["query"],
        'adventures' => $adventures,
        'authors' => $authors,
        'loggedIn' => $loggedIn
    ));

} catch(PDOException $e){
    echo 'ERROR: ' . $e->getMessage();
}
finally{
    $oConn = null;
}
