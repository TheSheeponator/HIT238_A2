<?php
  require "header.php";
  require "./includes/checkperm.php";
  
  $sql = "SELECT uidUsers FROM sysUsers WHERE uidUsers=? AND perms=1";
  $stmt = mysqli_stmt_init($conn);
  
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    //header("Location: ./signup.php?error=error");
    require "errordocs/err001.php";
    exit();
  }
  else {
    $userName = $_SESSION['userUid'];
    mysqli_stmt_bind_param($stmt, "s", $userName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $resultCheck = mysqli_stmt_num_rows($stmt);

    if ($resultCheck <= 0) {
      redirect("control");
    }
  }
?>
<link rel="stylesheet" type="text/css" href="admin.css" />
<main>
  <div class="AU">
    <h1>Add User</h1>
    <?php
      if (isset($_GET['error'])) {
        if ($_GET['error'] == "emptyfields") {
          echo '<p id="AUerror">Fill in all fields!</p>';
        }
        else if ($_GET['error'] == "invalidmailuid") {
          echo '<p id="AUerror">Invalid username and E-mail!</p>';
        }
        else if ($_GET['error'] == "invalidmail") {
          echo '<p id="AUerror">Invalid E-mail!</p>';
        }
        else if ($_GET['error'] == "invaliduid") {
          echo '<p id="AUerror">Invalid username!</p>';
        }
        else if ($_GET['error'] == "passwordcheck") {
          echo '<p id="AUerror">Your passwords do not match!</p>';
        }
        else if ($_GET['error'] == "usertacken") {
          echo '<p id="AUerror">Username is already taken!</p>';
        }
      }
      else if (isset($_GET['signup']) && $_GET['signup'] == "success") {
        echo '<p id="AUsuccess">Signup Successful!</p>';
      }
    ?>
    <br>
    <form action="includes/adduser.inc.php" method="POST">
      
      <br>
      <input type="text" name="mail" placeholder="E-mail" <?php if(isset($_GET['mail'])) { echo 'value="'.$_GET['mail'].'"';} ?>>
      <br>
      <input type="password" name="pwd" placeholder="Password">
      <br>
      <input type="password" name="pwd-repeat" placeholder="Repeat Password">
      <br>
      <button type="submit" name="adduser-submit">Add</button>
    </form>
  </div>
</main>