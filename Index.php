<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head_include.php';?>
</head>
<body>

    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <img id="logo" alt="Wanderblog" src="img/logo.png">
                </a>
            </div>

            <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">

            </div>
        </div>
    </nav>

    <div class="container-fluid">
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
    </div>

    <?php include 'script_include.php';?>

</body>
</html>