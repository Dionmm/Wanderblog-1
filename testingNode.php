<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 20/12/2015
 * Time: 20:11
 */
?>
<html>
<head>

</head>
<body>
<header>
    <h1>Header Tag</h1>
</header>
<main>
    <h2>Main content</h2>
</main>
<aside id="twitter">

</aside>
<script>
    window.onload = function () {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                document.getElementById("twitter").innerHTML = xhttp.responseText;
            }
        };
        xhttp.open("GET", "http://onenodeapp.azurewebsites.net", true);
        xhttp.send();
    }
</script>
</body>
</html>
