<?php
  include("includes/header.php");
  include("includes/form_handlers/settings_handler.php");


 ?>

<div class="wrapper">
  <div class="main_column column">
    <h4>Account Settings</h4>
    <?php
      echo "<img src='" .  $user['profile_picture'] . "' id='small_profile_picture'>"
     ?>
     <br>
     <a href="upload.php">Upload new profile picture</a>
     <br><br>
     Modify the values and click 'Update Details'

     <?php
      $user_data_query = mysqli_query($connection,
        "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
      $row = mysqli_fetch_array($user_data_query);

      $first_name = $row['first_name'];
      $last_name = $row['last_name'];
      $email = $row['email'];
      ?>

     <form action="settings.php" method="POST">
       First Name: <input type="text" name="first_name" class="setting_input" value="<?php echo $first_name; ?>"><br>
       Last Name: <input type="text" name="last_name" class="setting_input" value="<?php echo $last_name; ?>"><br>
       Email: <input type="text" name="email" class="setting_input" value="<?php echo $email; ?>"><br>
       <?php echo $message; ?>
       <input type="submit" name="update_details" id="save_details" class="info settings_submit" value="Update Details"><br>
     </form>

     <h4>Change Password</h4>
     <form action="settings.php" method="POST">
       Old Password: <input type="password" class="setting_input" name="old_password"><br>
       New Password: <input type="password" class="setting_input" name="new_password"><br>
       New Password Again: <input type="password" class="setting_input" name="new_password2"><br>
       <?php echo $password_message; ?>
       <input type="submit" name="update_password" id="save_password" class="info settings_submit" value="Update Password"><br>
     </form>

     <h4>Close Account</h4>
     <form action="settings.php" method="POST">
       <input type="submit" name="close_account" id="close_account" class="danger settings_submit" value="Close Account">
     </form>
  </div>
</div>
