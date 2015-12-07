<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 03/11/2015
 * Time: 18:20
 */
require_once 'functions.php'; //Grabs any extra functions
session_start();

//Check which kind of request is coming through and filter appropriately
if (isset($_GET['id'])) {
    $PostID = $_GET['id']; //grab the id for the post

    if (isset($_POST['loadComment'])) {
        //Load the comments
        loadComments($PostID);
    } else if (isset($_POST['comment'])) {
        //Add a new comment
        addComment($PostID);
    } else if(isset($_GET['remove'])){
        //Delete adventure
        deleteAdventure($PostID);
    } else {
        //Just display the adventure
        readAdventure($PostID);
    }

} else if (isset($_POST['save']) && isset($_SESSION['editingID'])) { //Saves changes to an existing post
    $PostID = $_SESSION['editingID'];
    saveAdventure($PostID, 'existing');

} else if (isset($_POST['save']) && !isset($_SESSION['editingID'])) { //If ID isn't set then save as new adventure
    $PostID = createPostID(); //Generate PostID here
    saveAdventure($PostID, 'new'); //Save to DB here

} else if (isset($_GET['create']) && !isset($_GET['id'])) { //Renders a blank template so user's can create new adventure
    //Create adventure here
    createAdventure();

} else {
    echo 'Error: Something Went wrong ' . $_GET['id'];
}

function readAdventure($PostID)
{

    try {
        $oConn = loginToDB(); //Login to DB (Functions.php)

        //Prepare statement, substitute :username with username field input
        $query = $oConn->prepare('SELECT * FROM Adventures WHERE Adventures.PostID = :PostID'); //Query for PostID and any comments it may have
        $query->bindValue(':PostID', $PostID, PDO::PARAM_STR);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match. FETCH_NAMED is used due to having multiple columns with the same name

        // If there are results
        if ($rows) {

            //Set adventure to the first item in array. A post with comments will return multiple items in the same array
            //Where as a post without comments will return just 1 item
            $adventure = $rows[0];

            //Check for logged in
            $loggedIn = loggedIn();

            //Check if they own the adventure or are admin
            $canEdit = 0;
            if ($loggedIn['loggedIn']) {
                if ($_SESSION['user_group'] == 'admin' || $_SESSION['username'] == $rows[0]['Username'][0]) {
                    $canEdit = true;
                }
            }

            //Check if the user is editing the adventure
            if (isset($_GET['edit']) && $canEdit == true) {

                //Set the editing ID to be compared to PostID once saved, ensures user's can't alter the postID
                $_SESSION['editingID'] = $PostID;

                //return the template specified above with the following variables filled in
                $renderArray = array(
                    'adventure' => $adventure,
                    'loggedIn' => $loggedIn,
                    'editing' => 'true',
                    'postID' => $PostID
                );

            } else {

                //return the template specified above with the following variables filled in
                $renderArray = array(
                    'adventure' => $adventure,
                    'loggedIn' => $loggedIn,
                    'canEdit' => $canEdit,
                    'postID' => $PostID
                );
            }

            //Templating
            require_once 'vendor/autoload.php';
            $loader = new Twig_Loader_Filesystem('views');
            $twig = new Twig_environment($loader);
            $template = $twig->loadTemplate('adventure.twig');

            echo $template->render($renderArray);

        } else {
            echo 'Error: File not found';
        }

    } catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
    finally{
        $oConn = null;
    }
}

function createAdventure()
{
    try{
        //Check for logged in
        $loggedIn = loggedIn();

        //Check if they have sufficient permissions to create adventure (Equal to or greater than AUTHOR)
        if ($loggedIn['loggedIn']) {
            if ($loggedIn['user_group'] > 1) {

                //Put Username into the adventure object
                $adventure = ['Username' => $_SESSION['username']];

                //Templating
                require_once 'vendor/autoload.php';
                $loader = new Twig_Loader_Filesystem('views');
                $twig = new Twig_environment($loader);
                $template = $twig->loadTemplate('adventure.twig');

                //return the template specified above with the following variables filled in
                echo $template->render(array(
                    'adventure' => $adventure,
                    'loggedIn' => $loggedIn,
                    'editing' => 'true'
                ));
            } else {
                echo 'You do not have sufficient permissions to create adventures';
            }
        } else {
            echo 'Error: Must be logged in to create adventure';
        }
    } catch (PDOException $e) {
        return 'ERROR: ' . $e->getMessage();
    }
    finally{
        $oConn = null;
    }


}

function createPostID()
{

    try {
        $oConn = loginToDB();

        $query = $oConn->prepare('SELECT PostID FROM Adventures WHERE PostID = :PostID'); //Prepare query to check for existing postID

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
    finally{
        $oConn = null;
    }
}

function saveAdventure($PostID, $SQLType)
{

    //Check for logged in
    $loggedIn = loggedIn();

    //Check the POST variables are set, that the user's editingID matches the PostID sent back, and that the user has permission (Equal to or greater than AUTHOR)
    if (isset($_POST['title'], $_POST['content']) && $loggedIn['user_group'] > 1) {

        //Ensures post variables are not empty
        if (!empty($_POST['title']) && !empty($_POST['content'])) {

            $title = $_POST['title']; //grab the contents of the post
            $content = $_POST['content'];
            $username = $_SESSION['username'];
            $city = NULL; //These will eventually have their own field to be filled out on the edit page
            $country = NULL;

            try {
                $oConn = loginToDB();

                //******POSSIBLY LOOK INTO SWAPPING THIS IF STATEMENT AROUND AS IT DEFAULTS TO AN UPDATE STATEMENT, OR CHANGE TO ELSE IF AND ADD AN ELSE FOR ERROR
                //If the it's a new post then insert into DB
                if ($SQLType === 'new') {
                    $saveAdventure = $oConn->prepare("INSERT INTO Adventures VALUES (:PostID, :Username, :Title, :Content, 0, :City, :Country, NOW())");
                    $saveAdventure->bindValue(':Username', $username, PDO::PARAM_STR);
                } else { //Otherwise assume it is an UPDATE and update the existing Post where the PostID matches
                    $saveAdventure = $oConn->prepare("UPDATE Adventures SET Title = :Title, Content = :Content, City = :City, Country = :Country WHERE PostID = :PostID");
                }
                //Substitute field input
                $saveAdventure->bindValue(':PostID', $PostID, PDO::PARAM_STR);
                $saveAdventure->bindValue(':Title', $title, PDO::PARAM_STR);
                $saveAdventure->bindValue(':Content', $content, PDO::PARAM_STR);
                $saveAdventure->bindValue(':City', $city, PDO::PARAM_STR);
                $saveAdventure->bindValue(':Country', $country, PDO::PARAM_STR);

                //If the query was executed successfully then return success message and postID
                if ($saveAdventure->execute()) {
                    //Unset the Id that was set earlier
                    if (isset($_SESSION['editingID'])) {
                        unset($_SESSION['editingID']);
                    };
                    $success = 'successfully added adventure to database';
                    $returnMessage = json_encode(array('success' => $success, 'PostID' => $PostID));
                } else {
                    $returnMessage = json_encode(array('error' => 'Failed to add to database'));

                }
                echo $returnMessage;

            } catch (PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
            finally{
                $oConn = null;
            }


        } else {
            echo 'Error: Fields were empty';
        }
    } else {
        echo '500, Couldn\'t save'; //return server error

    }

}

function savePictures($PostID, $SQLType){
    //Check for logged in
    $loggedIn = loggedIn();

    if($loggedIn['user_group'] > 1){

        try {

            $oConn = loginToDB();

            $uploadDir = 'uploads\\' . $PostID . '\\';

            if(!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            for($i=0; $i<count($_FILES['files']['name']); $i++) {

                $file = $_FILES["files"]["tmp_name"][$i];

                //Check file is an image
                $imageSizeData = getimagesize($file);
                if ($imageSizeData === FALSE) {
                    //not an image and don't add to database
                } else {
                    $fileName = $_FILES["files"]["name"][$i];
                    $tmpName = $_FILES["files"]["tmp_name"][$i];

                    $ext = substr(strrchr($fileName, "."), 1);
                    $randName = md5(rand() * time()); //could use sha256

                    $filePath = $uploadDir . $randName . '.' . $ext;

                    $result = move_uploaded_file($tmpName, $filePath);

                    if ($result) {
                        $imageName = addslashes($fileName);

                        $imagePath = addslashes($filePath);

                        if ($SQLType === 'new') {
                            $savePicture = $oConn->prepare("INSERT INTO Pictures VALUES (NULL, $PostID, '$imageName', '$imagePath', NOW())");
                        } else {
                            //editing existing pictures
                        }

                        if ($savePicture->execute()) {
                            $success = 'successfully uploaded picture';
                            $returnMessage = json_encode(array('success' => $success, 'PostID' => $PostID));
                        }

                        echo $returnMessage;
                    }
                }
            }
        }catch(PDOException $e){
            echo 'ERROR: ' . $e->getMessage();

        }finally{
            $oConn = null;
        }
    }else {
        echo '500, Couldn\'t save'; //return server error

    }
}

function loadComments($PostID)
{

    $oConn = loginToDB(); //Login to DB (Functions.php)

    //Prepare statement, substitute :username with username field input
    $query = $oConn->prepare('SELECT * FROM Comments WHERE PostID = :PostID'); //Query for PostID and any comments it may have
    $query->bindValue(':PostID', $PostID, PDO::PARAM_STR);
    $query->execute();
    $rows = $query->fetchAll(PDO::FETCH_ASSOC); //grab all values that match. FETCH_NAMED is used due to having multiple columns with the same name

    echo json_encode($rows);
}

function addComment($PostID)
{

    //Check for logged in
    $loggedIn = loggedIn();

    //Check the comment is not empty and that the user has sufficient permissions to post a comment (Equal to or greater than READER)
    if (!empty($_POST['comment']) && $loggedIn['user_group'] > 0) {

        $comment = $_POST['comment']; //grab the comment from the post
        $username = $_SESSION['username'];
        if (isset($_POST['replyingTo'])) {
            $InReplyTo = $_POST['replyingTo'];
        } else {
            $InReplyTo = NULL;
        }


        //Create connection to database, query for username and verify password
        try {
            $oConn = loginToDB();

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
        finally{
            $oConn = null;
        }


    } else {
        echo '500'; //return server error
    }
}

function deleteAdventure($PostID)
{
    //Check for logged in
    $loggedIn = loggedIn();

    //Check if you are an author with the selected adventure
    if ($loggedIn['user_group'] > 1) {
        try {
            $oConn = loginToDB();

            $username = $loggedIn['username'];

            $query = $oConn->prepare("SELECT a.PostID FROM Adventures a WHERE a.Username = '$username'");
            $query->execute();
            $adventurePostIDs = $query->fetchAll(PDO::FETCH_ASSOC);

            $isAuthorOfThisPost = false;

            //Iterates through 2D array of logged in user's PostIDs
            //and tries to match with PostID that was requested to be deleted
            foreach($adventurePostIDs as $arrValue) {
                if ($arrValue['PostID'] == $PostID) {
                    $isAuthorOfThisPost = true;
                }
            }

            //If author of this post OR admin - allow deletion
            if($isAuthorOfThisPost || $loggedIn['user_group'] == 3){
                $query = $oConn->prepare("DELETE FROM Adventures WHERE PostID = '$PostID'");
                $query->execute();
                echo json_encode(array('success' => true));
            }

        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
        finally{
            $oConn = null;
        }

    } else {
        echo 'Not enough permissions to perform this action.';
    }
}


