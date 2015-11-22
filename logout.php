
<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 20/10/2015
 * Time: 13:28
 */
session_start(); //initialise session
session_destroy(); //destroy all sessions
header("Location: index.php"); //go back to index page
?>

