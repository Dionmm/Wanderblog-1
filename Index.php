<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wanderblog</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/main.css">
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
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>