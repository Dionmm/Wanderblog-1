<?php
/**
 * Created by PhpStorm.
 * User: dion
 * Date: 20/12/2015
 * Time: 20:11
 */
header('Access-Control-Allow-Origin: *');
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
                var tweets = JSON.parse(xhttp.responseText);
                var tweetString = '';
                for (var i = 0; i < tweets.length; i++) {
                    tweetString += "<h3>" + tweets[i].name + "</h3></br>";
                    tweetString += "<p>" + tweets[i].text + "</p></br>";
                    tweetString += '<img src="' + tweets[i].image + '">';
                }
                document.getElementById("twitter").innerHTML = tweetString;
            }
        };
        xhttp.open("GET", "http://onenodeapp.azurewebsites.net", true);
        xhttp.send();
    }
</script>
</body>
</html>
