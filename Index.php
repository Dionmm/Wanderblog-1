<?php
    require_once 'functions.php';

//Load more adventures when requested
if (isset($_POST['timesRequested'])) {
    $timesRequested = $_POST['timesRequested'];

    $oConn = loginToDB();
    $loggedIn = loggedIn();
    $username = $loggedIn['username'] ?: NULL;
    //Prepare statement, substitute :username with username field input
    $query = $oConn->prepare('SELECT tmp_table.*, Upvoted.Username as Voted FROM (SELECT Adventures.PostID, Adventures.Username, Adventures.Title, Adventures.Upvotes, Adventures.DatePosted, Pictures.Path FROM Adventures LEFT JOIN Pictures ON Adventures.PostID = Pictures.PostID GROUP BY Adventures.PostID) as tmp_table LEFT JOIN Upvoted ON tmp_table.PostID = Upvoted.PostID AND Upvoted.Username = :username ORDER BY Upvotes DESC LIMIT :limit, 8');
    $query->bindValue(':limit', $timesRequested * 8, PDO::PARAM_INT);
    $query->bindValue(':username', $username, PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match

    //Check if each adventure has an image, if not assign a random one
    foreach ($rows as $key => $row) {
        if ($row['Path'] === NULL) {
            $randINT = rand(300, 500);
            $rows[$key]['Path'] = 'http://lorempixel.com/' . $randINT . '/' . $randINT . '/nature/';
        }
        if ($row['Voted'] === NULL) {
            $rows[$key]['Voted'] = 'false';
        } else {
            $rows[$key]['Voted'] = 'true';
        }
    }


    echo json_encode($rows);

    //Add a like to an adventure
} else if (isset($_POST['liked'])) {
    echo addLike($_POST['liked']);
} else { //Load index.twig

    //Create connection to database, query for username and verify password
    try {
        $oConn = loginToDB();

        //Check for logged in
        $loggedIn = loggedIn();
        $username = $loggedIn['username'] ?: NULL;

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT tmp_table.*, Upvoted.Username as Voted FROM (SELECT Adventures.PostID, Adventures.Username, Adventures.Title, Adventures.Upvotes, Adventures.DatePosted, Pictures.Path FROM Adventures LEFT JOIN Pictures ON Adventures.PostID = Pictures.PostID GROUP BY Adventures.PostID) as tmp_table LEFT JOIN Upvoted ON tmp_table.PostID = Upvoted.PostID AND Upvoted.Username = :username ORDER BY Upvotes DESC LIMIT 8'); // Grab the 8 most recent Adventures
        $query->bindValue(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match

        //Check if each adventure has an image, if not assign a random one
        foreach ($rows as $key => $row) {
            if ($row['Path'] === NULL) {
                $randINT = rand(300, 500);
                $rows[$key]['Path'] = 'http://lorempixel.com/' . $randINT . '/' . $randINT . '/nature/';
            }
            if ($row['Voted'] === NULL) {
                $rows[$key]['Voted'] = 'false';
            } else {
                $rows[$key]['Voted'] = 'true';
            }
        }

        $adventures = $rows;

        //Templating
        require_once 'vendor/autoload.php';

        $loader = new Twig_Loader_Filesystem('views');
        $twig = new Twig_environment($loader);
        $template = $twig->loadTemplate('index.twig');

        echo $template->render(array(
            'adventures' => $adventures,
            'loggedIn' => $loggedIn
        ));

    } catch(PDOException $e){
        echo 'ERROR: ' . $e->getMessage();
    }
    finally{
        $oConn = null;
    }
}