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

    //Return the template specified above with the following variables filled in
    echo $template->render(array(
        'user' => $user[0],
        'adventures' => $adventures,
        'loggedIn' => $loggedIn
    ));
}