<?php include 'head_include.php';?>

<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 20/10/2015
 * Time: 13:28
 */
session_start(); //initialise session
session_destroy(); //destroy all sessions
?>
<p>Session has been destroyed.</p>
<a href="index.php">Go back</a>

<?php include 'script_include.php';?>
