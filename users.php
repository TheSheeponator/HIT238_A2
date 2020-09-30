<?php
  require "header.php";
  require "./includes/checkperm.php";

  $sql = "SELECT uidUsers FROM sysusers WHERE uidUsers=? AND perms=1";
  $stmt = mysqli_stmt_init($conn);

  if (!mysqli_stmt_prepare($stmt, $sql)) {
    // require "errordocs/err001.php";
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
<link rel="stylesheet" type="text/css" href="/css/admin.css" />
<script src="/scripts/UserManager.js"></script>
<main>
  <table class="Utable" id="UserTable">
    <thead>
      <tr>
        <th>Username</th>
        <th>User E-mail</th>
        <th>Permission Level</th>
        <th>Remove User</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
    <br>
    <button class="addUserWindowButton">Add User</button>
    <div class="popupContainer">
            <div class="popupBackground"></div>
            <span class="helper"></span>
            <div class="popupBody">
                <div class="popupCloseButton">&times;</div>
                <form action="includes/adduser.inc.php" method="POST">
                <h3>Add User</h3>
                    <table>
                        <tbody>
                          <tr>
                            <td><label for="newuid">Username: </label></td>
                            <td><input type="text" name="newuid" id="newuid" placeholder="Username"></td>
                          </tr>
                          <tr>
                            <td><label for="newmail">E-mail: </label></td>
                            <td><input type="text" name="newmail" id="newmail" placeholder="E-mail"></td>
                          </tr>
                          <tr>
                            <td><label for="newpwd">Password: </label></td>
                            <td><input type="password" name="newpwd" id="newpwd" placeholder="Password"></td>
                          </tr>
                          <tr>
                            <td></td>
                            <td><input type="password" name="newpwd-repeat" id="newpwd-repeat" placeholder="Repeat Password"></td>
                          </tr>
                          <tr>
                            <td colspan="2"><button type="submit" name="adduser-submit">Add</button></td>
                          </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
</main>