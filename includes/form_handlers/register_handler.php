<?php

$error_array = array();

function clean_string($string) {
  $string = strip_tags($string);
  $string = str_replace(" ", "", $string);
  $string = ucfirst(strtolower($string));
  return $string;
}

function clean_password($password) {
  $password = strip_tags($password);
  $password = ucfirst(strtolower($password));
  return $password;
}

function clear_session_variables() {
  unset($_SESSION['register_firstname']);
  unset($_SESSION['register_lastname']);
  unset($_SESSION['register_email']);
  unset($_SESSION['register_email2']);
}

if (isset($_POST['register_button'])) {
  $firstname = clean_string($_POST['register_firstname']);
  $_SESSION['register_firstname'] = $firstname;

  $lastname = clean_string($_POST['register_lastname']);
  $_SESSION['register_lastname'] = $lastname;

  $email = clean_string($_POST['register_email']);
  $_SESSION['register_email'] = $email;

  $email2 = clean_string($_POST['register_email2']);
  $_SESSION['register_email2'] = $email2;

  $password = clean_password($_POST['register_password']);
  $_SESSION['register_password'] = $password;

  $password2 = clean_password($_POST['register_password2']);
  $_SESSION['register_password2'] = $password;

  $date = date("Y-m-d");

  if ($email == $email2) {
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email = filter_var($email, FILTER_VALIDATE_EMAIL);

      $email_check = mysqli_query($connection, "SELECT email FROM users WHERE email='$email'");
      $num_row = mysqli_num_rows($email_check);

      if ($num_row > 0) {
        array_push($error_array, Constants::$emailTaken);
      }
    } else {
      array_push($error_array, Constants::$emailInvalid);
    }
  } else {
    array_push($error_array, Constants::$emailsDoNoMatch);
  }

  if (strlen($firstname) > 25 || strlen($firstname) < 2) {
    array_push($error_array, Constants::$firstNameNotAlphanumeric);
  }

  if (strlen($lastname) > 25 || strlen($lastname) < 2) {
    array_push($error_array, Constants::$lastNameNotAlphanumeric);
  }

  if ($password != $password2) {
    array_push($error_array, Constants::$passwordsDoNoMatch);
  } else {
    if(preg_match('/[^A-Za-z0-9]/', $password)) {
      array_push($error_array, Constants::$passwordNotAlphanumeric);
    }
  }

  if(strlen($password) > 30 || strlen($password) < 5) {
    array_push($error_array, Constants::$passwordCharacters);
  }

  if(empty($error_array)) {
    $encrypted_password = md5($password);
    $username = strtolower($firstname . "_" . $lastname);
    $check_username = mysqli_query($connection, "SELECT usernmame FROM users WHERE username='$username'");

    $random = rand(1, 2);

    if ($random == 1) {
      $profile_picture = "assets/images/profile_pictures/defaults/head_deep_blue.png";
    } else {
      $profile_picture = "assets/images/profile_pictures/defaults/head_emerald.png";
    }

    $query = mysqli_query($connection, "INSERT INTO users VALUES ('', '$firstname', '$lastname', '$username', '$email', '$encrypted_password', '$date', '$profile_picture', '0', '0', 'no', ',')");
    array_push($error_array, Constants::$succeedMessage);
    clear_session_variables();
  }
}

?>
