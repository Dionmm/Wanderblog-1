<?php
    //Querying
    require_once 'config.php'; //Grabs the database details

    //Create connection to database, query for username and verify password
    try {
        //Set persistent connection
        $oConn = new PDO('mysql:host='.$sHost.';dbname='.$sDb, $sUsername, $sPassword, array(
            PDO::ATTR_PERSISTENT => true
        ));
        $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT * FROM adventures');
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match

        $articleTitle = $rows;


        //Check for logged in
        session_start();
        $loggedIn = false;
        $name = '';
        if(isset($_SESSION['name'])){
            $loggedIn = true;
            $name = $_SESSION['name'];
        }


        //Templating
        require_once 'vendor/autoload.php';

        $loader = new Twig_Loader_Filesystem('views');
        $twig = new Twig_environment($loader);
        $template = $twig->loadTemplate('index.html');

        echo $template->render(array(
            'articleTitle' => $articleTitle,
            'loggedIn' => $loggedIn,
            'name' => $name
        ));

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
