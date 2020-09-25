<?php
    function redirect($url) {
        header('Location: ./'.$url);

        try 
        {
            mysqli_close($conn);
        }
        finally 
        {
            exit();
        }

    }
    function redirectUp($url) {
        header('Location: ../'.$url);

        try 
        {
            mysqli_close($conn);
        }
        finally 
        {
            exit();
        }
    }
    function redirectDown($url) {
        header('Location: ./includes/'.$url);
        
        try 
        {
            mysqli_close($conn);
        }
        finally 
        {
            exit();
        }
    }