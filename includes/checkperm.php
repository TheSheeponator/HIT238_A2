<?php 
        $sql = "SELECT uidUsers FROM sysUsers WHERE uidUsers=? AND perms=1";
        $stmt = mysqli_stmt_init($conn);
    
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            //header("Location: ./signup.php?error=error");
            // $errormess = array('Oops-ies... something bad happened to me! Please help by getting in touch with by fixer-upper.', 'Where has the page gone!', 'WHAT DID YOU DO?',  'Oh no, this was not in the script.', '');
            // echo '<div id="error">';
            // echo '<b>'.$errormess[rand(0, sizeof($errormess)-1)].'</b>';
            
            // echo '<br> Error \'01\' Occurred and the page could not be loaded (if it even exists)! Please Contact the System Administrator.</div>';
            redirectUp('errordocs/err001');
            mysqli_close($conn);
            exit();
        }
        else {
            $userName = $_SESSION['userUid'];
            mysqli_stmt_bind_param($stmt, "s", $userName);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck <= 0) {
                require './errordocs/err403.php';
                mysqli_close($conn);
                exit();
            }
        }
?>