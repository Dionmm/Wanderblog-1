<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 15/12/2015
 * Time: 20:39
 */
$input = $_POST['s'];
echo $_POST['s'];
echo "\r\n";
stringCheck($input);

function stringCheck($string)
{
    preg_match_all('/style="(.*)"/', $string, $output);
    print_r($output[0]);
    $replaced = preg_replace('/ style="(.*)"/', '', $string);
    echo "\r\n";
    echo $string;
    echo "\r\n";

    echo $replaced;

//
//    foreach($output[1] as $match){
//        switch($match){
//            case 'div':
//            case 'br':
//            case 'b':
//            case 'strong':
//            case 'i':
//            case 'em':
//            case 'h2':
//            case 'h3':
//            case 'h4':
//            case 'h5':
//            case 'p':
//                return 'true';
//                break;
//            default:
//                return 'false';
//        }
//    }
//    return 'true';
}

?>
<form action="test.php" method="POST">
    <input type="text" name="s">
    <input type="submit">
</form>
