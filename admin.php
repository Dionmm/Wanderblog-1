<?php

require_once 'functions.php';

try{

    $loggedIn = loggedIn();

    $oConn = loginToDB();

    if ($loggedIn['user_group'] === 3) {

    }

    //Templating
    require_once 'vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('views');
    $twig = new Twig_environment($loader);
    $template = $twig->loadTemplate('admin.twig');

    echo $template->render(array(
        'user' => $user[0],
        'adventures' => $adventures,
        'loggedIn' => $loggedIn
    ));


} catch (PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}