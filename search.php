<?php

try {

    $query = $_POST["query"];


    //Templating
    require_once 'vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('views');
    $twig = new Twig_environment($loader);
    $template = $twig->loadTemplate('search.html');

    //Return the template specified above with the following variables filled in
    echo $template->render(array(
        'query' => $query,
    ));

} catch (Exception $e) {
    die ('ERROR: ' . $e->getMessage());
}
