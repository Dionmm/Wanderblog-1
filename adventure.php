<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 03/11/2015
 * Time: 18:20
 */
require_once 'functions.php'; //Grabs any extra functions

if (isset($_GET['id'])) {

    if (isset($_POST['comment'])) {
        $PostID = $_GET['id']; //grab the id for the post
        addComment($PostID);
    } else if (isset($_POST['save'])) {
        $PostID = $_GET['id'];
        saveAdventure($PostID, 'existing'); //Save to DB here

    } else {
        $PostID = $_GET['id']; //grab the id for the post
        readAdventure($PostID);
    }

} else if (isset($_POST['save']) && !isset($_GET['id'])) {
//    if(isset($_GET['id'])){
//        $PostID = $_GET['id'];
//        saveAdventure($PostID, 'existing'); //Save to DB here
//
//    } else{
    $PostID = createPostID(); //Generate PostID here
    saveAdventure($PostID, 'new'); //Save to DB here

//    }

} else if (isset($_GET['create']) && !isset($_GET['id'])) {
    //Create adventure here
    createAdventure();

} else {
    echo 'Error: Something Went wrong ' . $_GET['id'];

}

function readAdventure($PostID)
{

    //Create connection to database, query for username and verify password
    try {
        $oConn = LoginToDB();

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT * FROM Adventures LEFT JOIN Comments ON Comments.PostID = Adventures.PostID WHERE Adventures.PostID = :PostID'); //Query for PostID and any comments it may have
        $query->bindValue(':PostID', $PostID, PDO::PARAM_STR);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_NAMED); //grab all values that match. FETCH_NAMED is used due to having multiple columns with the same name

        // If there are results
        if ($rows) {

            //Set adventure to the first item in array. A post with comments will return multiple items in the same array
            //Where as a post without comments will return just 1 item
            $adventure = $rows[0];

            //Check for logged in
            $loggedIn = logged_in();


            //Check if they own the article or are admin
            $canEdit = false;
            if ($loggedIn['loggedIn']) {
                if ($_SESSION['user_group'] == 'admin' || $_SESSION['username'] == $rows[0]['Username'][0]) {
                    $canEdit = true;
                }
            }

            //Check if the user is editing the
            if (isset($_GET['edit']) && $canEdit == true) {

                //Templating
                require_once 'vendor/autoload.php';
                $loader = new Twig_Loader_Filesystem('views');
                $twig = new Twig_environment($loader);
                $template = $twig->loadTemplate('adventureEdit.html');

                //return the template specified above with the following variables filled in
                echo $template->render(array(
                    'adventure' => $adventure,
                    'loggedIn' => $loggedIn['loggedIn'],
                    'name' => $loggedIn['first_name'],
                    'permissions' => $loggedIn['user_group'],
                    'editing' => 'true',
                    'postID' => $PostID //Temporary till i can think of a better, more secure way
                ));

            } else {

                //Check if there are comments on the post, if not set $comments to zero
                if ($rows[0]['CommentID'] == !NULL) {
                    $comments = $rows;
                } else {
                    $comments = 0;
                }

                //Templating
                require_once 'vendor/autoload.php';
                $loader = new Twig_Loader_Filesystem('views');
                $twig = new Twig_environment($loader);
                $template = $twig->loadTemplate('adventure.html');

                //return the template specified above with the following variables filled in
                echo $template->render(array(
                    'adventure' => $adventure,
                    'comments' => $comments,
                    'loggedIn' => $loggedIn['loggedIn'],
                    'name' => $loggedIn['first_name'],
                    'permissions' => $loggedIn['user_group'],
                    'canEdit' => $canEdit,
                    'postID' => $PostID //Temporary till i can think of a better, more secure way
                ));
            }

        } else {
            echo 'Error: File not found';
        }


    } catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
}

function createAdventure()
{
    //Check for logged in
    $loggedIn = logged_in();


    //Check if they own the article or are admin
    if ($loggedIn['loggedIn']) {
        if ($loggedIn['user_group'] > 1) {
            $adventure = ['Username' => array($_SESSION['username'])];
            //Templating
            require_once 'vendor/autoload.php';
            $loader = new Twig_Loader_Filesystem('views');
            $twig = new Twig_environment($loader);
            $template = $twig->loadTemplate('adventureEdit.html');

            //return the template specified above with the following variables filled in
            echo $template->render(array(
                'adventure' => $adventure,
                'loggedIn' => $loggedIn['loggedIn'],
                'name' => $loggedIn['first_name'],
                'permissions' => $loggedIn['user_group'],
                'editing' => 'true'
            ));
        } else {
            echo 'You do not have sufficient permissions to create adventures';
        }
    } else {
        echo 'Error: Must be logged in to create adventure';
    }

}

function createPostID()
{

    //Create connection to database, query for username and verify password
    try {
        $oConn = LoginToDB();

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT PostID FROM Adventures WHERE PostID = :PostID'); //Query for PostID and any comments it may have

        do { //Create a random 5 character string and then query the database to ensure it is new
            $characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKMLNPQRSTUVWXYZ23456789';
            $PostID = '';
            for ($i = 0; $i < 5; $i++) {
                $PostID .= $characters[rand(0, strlen($characters) - 1)];
            }

            $query->bindValue(':PostID', $PostID, PDO::PARAM_STR);
            $query->execute();
            $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match
        } while ($rows);

        return $PostID;
    } catch (PDOException $e) {
        return 'ERROR: ' . $e->getMessage();
    }
}

function saveAdventure($PostID, $SQLType)
{

    //Check for logged in
    $loggedIn = logged_in();
    if (isset($_POST['title'], $_POST['content'])) {
        if (!empty($_POST['title']) && !empty($_POST['content']) && $loggedIn['user_group'] > 1) {

            $title = $_POST['title']; //grab the comment from the post
            $content = $_POST['content'];
            $username = $_SESSION['username'];
            $city = NULL;
            $country = NULL;

            //Create connection to database, query for username and verify password
            try {
                $oConn = LoginToDB();

                if ($SQLType === 'new') {
                    $addComment = $oConn->prepare("INSERT INTO Adventures VALUES (:PostID, :Username, :Title, :Content, 0, :City, :Country, NOW())");
                    $addComment->bindValue(':Username', $username, PDO::PARAM_STR);
                } else {
                    $addComment = $oConn->prepare("UPDATE Adventures SET Title = :Title, Content = :Content, City = :City, Country = :Country WHERE PostID = :PostID");
                }
                //Prepare statement, substitute field input
                $addComment->bindValue(':PostID', $PostID, PDO::PARAM_STR);
                $addComment->bindValue(':Title', $title, PDO::PARAM_STR);
                $addComment->bindValue(':Content', $content, PDO::PARAM_STR);
                $addComment->bindValue(':City', $city, PDO::PARAM_STR);
                $addComment->bindValue(':Country', $country, PDO::PARAM_STR);

                if ($addComment->execute()) {
                    $success = 'successfully added adventure to database';
                    $returnMessage = json_encode(array('success' => $success, 'PostID' => $PostID));
                } else {
                    $returnMessage = json_encode(array('error' => 'Failed to add to database'));

                }
                echo $returnMessage;

            } catch (PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }


        } else {
            echo 'Error: Fields were empty';
        }
    } else {
        echo '500'; //return server error

    }

}

function addComment($PostID)
{

    //Check for logged in
    $loggedIn = logged_in();

    if (!empty($_POST['comment']) && $loggedIn['user_group'] > 0) {

        $comment = $_POST['comment']; //grab the comment from the post
        $username = $_SESSION['username'];
        $InReplyTo = NULL; //Needs to be changed


        //Create connection to database, query for username and verify password
        try {
            $oConn = LoginToDB();

            //Prepare statement, substitute :username with username field input
            $addComment = $oConn->prepare("INSERT INTO Comments VALUES (NULL, :PostID, :Username, :Comment, NOW(), :InReplyTo)");
            $addComment->bindValue(':PostID', $PostID, PDO::PARAM_STR);
            $addComment->bindValue(':Username', $username, PDO::PARAM_STR);
            $addComment->bindValue(':Comment', $comment, PDO::PARAM_STR);
            $addComment->bindValue(':InReplyTo', $InReplyTo, PDO::PARAM_STR);
            if ($addComment->execute()) {
                $success = 'successfully added comment to database';
                $returnMessage = json_encode(array('success' => $success));
            } else {
                $returnMessage = json_encode(array('error' => 'Failed to add to database'));

            }
            echo $returnMessage;

        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }


    } else {
        echo '500'; //return server error
    }
}

