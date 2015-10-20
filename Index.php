
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wanderblog</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<div id="logon">
    <?php
    session_start();
    if(isset($_SESSION['name'])){
        echo $_SESSION['name'] . ' is logged in';
        ?>
        <a href="logout.php">Logout here</a>
        <?php
    } else{?>
        <form onsubmit="validateForm()"  method="POST">
            <label for="username">Username:</label> <input type="text" name="username">
            <label for="password">Password:</label><input type="password" name="password">
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
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="app.js"></script>
</body>
</html>