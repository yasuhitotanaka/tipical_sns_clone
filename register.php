<?php
require 'settings/config.php';
include("includes/classes/Constants.php");
include("includes/form_handlers/register_handler.php");
include("includes/form_handlers/login_handler.php");

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Welcome to Swirlfeed</title>

    <link rel="stylesheet" href="assets/css/register.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="assets/js/register.js"></script>

  </head>
  <body>

    <?php
      if(isset($_POST['register_button'])) {
        echo '
        <script>
          $(document).ready(function(){
            $("#first").hide();
            $("#second").show();
          });
        </script>
        ';
      }
    ?>

    <div class="wrapper">
      <div class="login_box">
        <div class="login_header text_center">
          <h1>Swirlfeed!</h1>
          <p>Login or Sign up below!</p>
        </div>

        <div id="first">
          <form class="text_center" action="register.php" method="POST">
            <p>
              <input type="email" name="login_email" placeholder="Email Address" value=
              "<?php if(isset($_SESSION['register_firstname'])) echo $_SESSION['register_firstname']; ?>" reqired>
            </p>
            <p>
              <input type="password" name="login_password" placeholder="Password">
            </p>
              <?php if(in_array(Constants::$loginFalied, $error_array)) echo Constants::$loginFalied;  ?>
            <p>
              <input type="submit" name="login_button" value="Login">
            </p>
            <span id="signup" class="signup">Need and Account? Register here!</span>
          </form>
        </div>

        <div id="second">
          <form class="text_center" action="register.php" method="POST">
            <p>
              <input type="text" name="register_firstname" placeholder="First Name" value=
              "<?php if(isset($_SESSION['register_firstname'])) echo $_SESSION['register_firstname']; ?>" reqired>
            </p>
            <?php if(in_array(Constants::$firstNameNotAlphanumeric, $error_array)) echo Constants::$firstNameNotAlphanumeric;  ?>

            <p>
              <input type="text" name="register_lastname" placeholder="Last Name" value=
              "<?php if(isset($_SESSION['register_lastname'])) echo $_SESSION['register_lastname']; ?>" reqired>
            </p>
            <?php if(in_array(Constants::$lastNameNotAlphanumeric, $error_array)) echo Constants::$lastNameNotAlphanumeric;  ?>

            <p>
              <input type="email" name="register_email" placeholder="Email" value=
              "<?php if(isset($_SESSION['register_email'])) echo $_SESSION['register_email']; ?>" reqired>
            </p>
            <?php if(in_array(Constants::$emailTaken, $error_array)) echo Constants::$emailTaken ?>
            <?php if(in_array(Constants::$emailInvalid, $error_array)) echo Constants::$emailInvalid;  ?>
            <?php if(in_array(Constants::$emailsDoNoMatch, $error_array)) echo Constants::$emailsDoNoMatch;  ?>

            <p>
              <input type="email" name="register_email2" placeholder="Confirm Email" value=
              "<?php if(isset($_SESSION['register_email2'])) echo $_SESSION['register_email2']; ?>" reqired>
            </p>

            <p>
              <input type="password" name="register_password" placeholder="Password" reqired>
            </p>
            <?php if(in_array(Constants::$passwordsDoNoMatch, $error_array)) echo Constants::$passwordsDoNoMatch;  ?>
            <?php if(in_array(Constants::$passwordNotAlphanumeric, $error_array)) echo Constants::$passwordNotAlphanumeric;  ?>
            <?php if(in_array(Constants::$passwordCharacters, $error_array)) echo Constants::$passwordCharacters;  ?>

            <p>
              <input type="password" name="register_password2" placeholder="Confirm Password" reqired>
            </p>
            <?php if(in_array(Constants::$succeedMessage, $error_array)) echo Constants::$succeedMessage;  ?>

            <p>
              <input type="submit" name="register_button" value="register">
            </p>
            <span id="signin" class="signin">Already have an account? Sign in here!</span>
          </form>
        </div>

      </div>
    </div>
  </body>
</html>
