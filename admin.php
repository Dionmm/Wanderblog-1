<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 11/11/2015
 * Time: 18:35
 */

require_once 'functions.php';

$loggedIn = loggedIn();

if ($loggedIn['user_group'] === 3) {
    //Templating
    require_once 'vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('views');
    $twig = new Twig_environment($loader);
    $template = $twig->loadTemplate('admin.twig');

    //return the template specified above with the following variables filled in
    echo $template->render(array(
        'name' => $loggedIn['first_name'],
        'permissions' => $loggedIn['user_group'],
        'username' => $loggedIn['username'],
        'loggedIn' => $loggedIn
    ));
} else {
    //redirect to homepage (Maybe change this to login page at some point)
    header("Location: /");
    die();
}