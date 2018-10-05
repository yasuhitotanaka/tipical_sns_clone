<?php
  if(isset($_POST['update_details'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $email_check = mysqli_query($connection, "SELECT * FROM users WHERE email='$email'");
    $row = mysqli_fetch_array($email_check);
    $matched_user = $row['username'];

    if ($matched_user == "" || $matched_user == $userLoggedIn) {
      $message = "Details updated!<br><br>";

      $query = mysqli_query($connection,
        "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username='$userLoggedIn'");
    } else {
      $message = "That email is already in use!<br><br>";
    }

  } else {
    $message = "";
  }


  //*****************************************************

  $password_message = "";
  if (isset($_POST['update_password'])) {
    $old_password = strip_tags($_POST['old_password']);
    $new_password = strip_tags($_POST['new_password']);
    $new_password2 = strip_tags($_POST['new_password2']);

    $password_query = mysqli_query($connection,
     "SELECT password FROM users WHERE username='$userLoggedIn'");
    $row = mysqli_fetch_array($password_query);
    $old_password_DB = $row['password'];

    if(md5($old_password) == $old_password_DB) {
      if ($new_password == $new_password2) {

        if(strlen($new_password) <= 4) {
          $password_message = "Sorry, your password must be greater than 4 charcters<br><br>";
        } else {
          $new_password_md5 = md5($new_password);
          $password_query = mysqli_query($connection,
           "UPDATE users SET password='$new_password_md5' WHERE username='$userLoggedIn'");
          $password_message = "Password has been changed! <br><br>";
        }

      } else {
        $password_message = "Your two new passwords need to match!<br><br>";
      }

    } else {
      $password_message = "The old passwords is incorrect!<br><br>";
    }
  }

  if (isset($_POST['close_account'])) {
    header("Location: close_account.php");
  }
?>
