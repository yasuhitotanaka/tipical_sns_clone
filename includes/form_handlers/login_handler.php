<?php
$unique = 1;

if (isset($_POST['login_button'])) {
  $email = filter_var($_POST['login_email'], FILTER_SANITIZE_EMAIL);
  $_SESSION['login_email'] = $email;
  $encrypted_password = md5($_POST['login_password']);

  $check_username_and_passowrd = mysqli_query($connection, "SELECT * FROM users WHERE email='$email' AND password='$encrypted_password'");
  $check_login = mysqli_num_rows($check_username_and_passowrd);

  if($check_login == $unique) {
    $row = mysqli_fetch_array($check_username_and_passowrd);

    $user_closed_query = mysqli_query($connection, "SELECT * FROM users WHERE email='$email' AND user_closed='yes'");
    if(mysqli_num_rows($user_closed_query) == $unique) {
      $reopen_accout = mysqli_query($connection, "UPDATE users SET user_closed='no' WHERE email='$email'");
    }

    $_SESSION['username'] = $row['username'];
    header("Location: index.php");
  } else {
    array_push($error_array, Constants::$loginFalied);
  }
}

?>
