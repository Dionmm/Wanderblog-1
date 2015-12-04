<?php

require_once 'functions.php';

try {

    $loggedIn = loggedIn();

    $oConn = loginToDB();

    $author = $_GET["username"];

    $user = [];

    $adventures = [];

    //Prepare statement, substitute keyword with the submitted query
    $query = $oConn->prepare("SELECT Username, FirstName, LastName, Country FROM User WHERE Username = '$author'");
    $query->execute();
    $user = $query->fetchAll(PDO::FETCH_ASSOC);

    //Prevent querying DB about non existent author
    if(!empty($user)) {
        $user = $user[0];

        //Prepare statement, substitute keyword with the submitted query
        $query = $oConn->prepare("SELECT * FROM Adventures WHERE Username = '$author' ORDER BY DatePosted ASC");
        $query->execute();
        $adventures = $query->fetchAll(PDO::FETCH_ASSOC);
    }


    //Templating
    require_once 'vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('views');
    $twig = new Twig_environment($loader);
    $template = $twig->loadTemplate('author.twig');

    //Return the template specified above with the following variables filled in
    echo $template->render(array(
        'user' => $user,
        'adventures' => $adventures,
        'loggedIn' => $loggedIn
    ));

} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
finally{
    $oConn = null;
}
