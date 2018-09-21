<?php
  require 'settings/config.php';
  include("includes/header.php");
  include("includes/classes/User.php");
  include("includes/classes/Post.php");
?>

<div class="wrapper">
  <div class="main_column column" id="main_column">
    <h4>Friend Requests</h4>
    <?php
      $query = mysqli_query($connection, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");

      if(mysqli_num_rows($query) == 0) {
        echo "You have no friends requests at this time!!";
      } else {
        while($row = mysqli_fetch_array($query)) {
          $user_from = $row['user_from'];
          $user_from_object = new User($connection, $user_from);

          echo $user_from_object->get_first_and_lastname() . " sent you a friend request!";

          $user_from_frinend_array = $user_from_object->get_friend_array();

          if(isset($_POST['accept_request' . $user_from])) {
            $add_friend_query = mysqli_query($connection, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from') WHERE username='$userLoggedIn'");
            $add_friend_query = mysqli_query($connection, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn') WHERE username='$user_from'");

            $delete_query = mysqli_query($connection, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
            echo "You are now friends!";
            header("Location: requests.php");
          }

          if(isset($_POST['ignore_request' . $user_from])) {
            $delete_query = mysqli_query($connection, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
            header("Location: requests.php");
          }
    ?>

          <form action="requests.php" method="POST">
            <input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" class="request_button success" value="Accept">
            <input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" class="request_button danger" value="Ignore">
          </form>

    <?php

        }
      }
    ?>


  </div>
</div>
