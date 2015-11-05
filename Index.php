<?php
    //Querying
    require_once 'config.php'; //Grabs the database details
    require_once 'functions.php';
    //Create connection to database, query for username and verify password
    try {
        //Set persistent connection
        $oConn = new PDO('mysql:host='.$sHost.';dbname='.$sDb, $sUsername, $sPassword, array(
            PDO::ATTR_PERSISTENT => true
        ));
        $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //error handling

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT PostID, Username, Title, Upvotes, DatePosted FROM Adventures ORDER BY DatePosted DESC LIMIT 8'); // Grab the 8 most recent Adventures
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match

        $adventures = $rows;


        //Check for logged in
        $loggedIn = logged_in();

        //Templating
        require_once 'vendor/autoload.php';

        $loader = new Twig_Loader_Filesystem('views');
        $twig = new Twig_environment($loader);
        $template = $twig->loadTemplate('index.html');

        echo $template->render(array(
            'adventures' => $adventures,
            'loggedIn' => $loggedIn['loggedIn'],
            'name' => $loggedIn['first_name'],
            'permissions' => $loggedIn['user_group']
        ));

    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
