<?php

    // Error 002 is for the script-side issues. Further uses to be followed.

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, minimum-scale=1, width=device-width">
        <title>Error</title>
        <style>
            html {height:95%;width:100%;} body{height:100%;} main{align-items:center;justify-content:center;display:flex;height:100%;width:100%;background-color:rgb(230, 230, 230);} #error-div{align-items:center;justify-content:center;display:flex;} #error-img{float:left;zoom:.3;} p{color:black;padding-left:40px;} inc{color:gray;}
        </style>
    </head>
    <body>
        <main>
            <div id="error-div">
                <a href="index"><img id="error-img" src="/img/logo1.png"></a><br>
                <p>
                    <span style="font-size:20px;padding-bottom:20px">Internal Error</span><br>
                    <?php
                        $errormsg = array("WHAT HAPPENED?!?!", "Oh, no...", "My bad, sorry.", "OOPS, That wasn't supposed to happen.", "We've lost control! Please come back once the issue has been tamed.", "Bugger...", "Well, Well, Well... That was supposed to be fixed already!");
                        echo "<b>".$errormsg[rand(0, sizeof($errormsg)-1)]."</b>";
                    ?>
                    <br>
                    Something bad happened behind the scenes and we are unable to provide you with this content right now.<br>
                    Try refreshing the page, if that doesn't work<br>
                    please restart the system and if the issue persists contact support.<br>
                    <inc>Well, that could have gone better.</inc><br>
                    <span style="color:maroon; padding-top:5px;">Error code: 002</span>
                </p>
            </div>
        </main>
    </body>
</html>