<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head_include.php';?>
</head>
<body>
    <div id="logon">
        <?php

        session_start(); //Initialise a session

        if(isset($_SESSION['name'])){ //Check if there is already an existing session

            echo $_SESSION['name'] . ' is logged in'; //display existing session details
            ?>
            <a href="logout.php">Logout here</a>
            <?php

        } else{ // if no session, allow login?>

            <form onsubmit="validateForm()"  method="POST">
                <label for="username">Username:<input type="text" name="username"></label>
                <label for="password">Password:<input type="password" name="password"></label>
                <input type="submit">
            </form>

            <?php
        }
        ?>
        <p>Username is dionmm and password is password</p>
    </div>

    <?php
    /*
        require_once 'config.php';

        try {
            $oConn = new PDO('mysql:host='.$sHost.';dbname='.$sDb, $sUsername, $sPassword);
            $oConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $oStmt = $oConn->prepare('SELECT data FROM `hello_world`');
            $oResult = $oStmt->fetchAll();

            foreach ($oResult as $aRow) {
                print_r($aRow['data']);
            }

        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }

    */?>
    <?php include 'script_include.php';?>

</body>
</html>