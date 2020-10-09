<?php

    // Error 003 is for ajax only sections of the site and is displayed when a user goes there manually.

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, minimum-scale=1, width=device-width">
        <title>Error</title>
        <style>
            html {height:100%;width:100%;} body{height: 100%;} main{align-items:center;justify-content:center;display:flex;height:100%;width:100%;background-color:rgb(230, 230, 230);} #error-div{align-items:center;justify-content:center;display:flex;} #error-img{float:left;zoom:.3;} p{color:black;padding-left:40px;} inc{color:gray;}
        </style>
    </head>
    <body>
        <main>
            <div id="error-div">
                <a href="index"><img id="error-img" src="/img/logo1.png"></a><br>
                <p>
                    <span style="font-size:20px;padding-bottom:20px">Error: Human Restricted Area!</span><br>
                    <?php
                        $errormsg = array("Where to you think you're going?", "Halt!", "Let me see your papers!", "You shall not pass!", "Back up! Where are you of to now?", "What were you thinking?", "Boss said no, sorry can't let you pass.");
                        echo "<b>".$errormsg[rand(0, sizeof($errormsg)-1)]."</b>";
                    ?>
                    <br>Access to this section of the site by humans is restricted (you'd only get random stuff anyway). Please just go back.<br>
                    <inc>No really you won't find any useful information here, go back.</inc>
                </p>
            </div>
        </main>
    </body>
</html>