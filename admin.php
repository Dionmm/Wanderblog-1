<?php

require_once 'functions.php';

if (isset($_GET['username']) && isset($_GET['usertype'])) {
    changeUserType($_GET['username'], $_GET['usertype']);
} else{
    display();
}


function display(){

    $loggedIn = loggedIn();

    $oConn = loginToDB();

    try{

        $adventures = [];
        $users = [];
        $error = true;

        if ($loggedIn['user_group'] === 3) {

            //Get adventure data + comment amount for each adventure via subquery
            $query = $oConn->prepare("SELECT a.*, (SELECT COUNT(*) FROM Comments WHERE PostID = a.PostID) AS CommentAmount FROM Adventures a ORDER BY a.DatePosted ASC");
            $query->execute();
            $adventures = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $oConn->prepare("SELECT * FROM User;");
            $query->execute();
            $users = $query->fetchAll(PDO::FETCH_ASSOC);

            //$adventuresJson = json_encode(utf8ize($adventures));
            $error = false;
        }

    }
    catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
    finally{
        $oConn = null;

        //Templating
        require_once 'vendor/autoload.php';
        $loader = new Twig_Loader_Filesystem('views');
        $twig = new Twig_environment($loader);
        $template = $twig->loadTemplate('admin.twig');

        echo $template->render(array(
            'users' => $users,
            'adventures' => $adventures,
            'error' => $error,
            'loggedIn' => $loggedIn
        ));

    }
}

function changeUserType($username, $userType){

    $loggedIn = loggedIn();

    $oConn = loginToDB();

    try{

        if ($loggedIn['user_group'] === 3) {

            $query = $oConn->prepare("UPDATE User SET UserType = '$userType' WHERE Username = '$username';");
            $query->execute();
            echo json_encode(array('success'));

        }

    }
    catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        echo json_encode(array('fail'));
    }
    finally{
        $oConn = null;
    }
}

function deleteUser($username){

    $loggedIn = loggedIn();

    $oConn = loginToDB();

    try{

        if ($loggedIn['user_group'] === 3) {

            $query = $oConn->prepare("DELETE FROM User WHERE Username = '$username';");
            $query->execute();
            echo json_encode(array('success'));

        }

    }
    catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
        echo json_encode(array('fail'));
    }
    finally{
        $oConn = null;
    }
}
