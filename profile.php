<?php
  require 'settings/config.php';
  include("includes/header.php");
  include("includes/classes/User.php");
  include("includes/classes/Post.php");

  if(isset($_GET['profile_username'])) {
    $username = $_GET['profile_username'];
    $user_details_query = mysqli_query($connection, "SELECT * FROM users WHERE username='$username'");
    $user_array = mysqli_fetch_array($user_details_query);

    $num_friends = substr_count($user_array['friend_array'], ",") - 1;
  }

  if(isset($_POST['remove_friend'])) {
    $user = new User($connection, $userLoggedIn);
    $user->remove_friend($username);
  }

  if(isset($_POST['add_friend'])) {
    $user = new User($connection, $userLoggedIn);
    $user->send_request($username);
  }

  if(isset($_POST['respond_request'])) {
    header("Location: requests.php");
  }

?>
  <style media="screen">
    .wrapper {
      margin-left: 0;
      padding-left: 0;
    }
  </style>

  <div class="wrapper">
    <div class="profile_left">
      <img src="<?php echo $user_array['profile_picture']; ?>" alt="">
      <div class="profile_info">
        <p><?php echo "Posts: " . $user_array['number_posts']; ?></p>
        <p><?php echo "Likes: " . $user_array['number_likes']; ?></p>
        <p><?php echo "Friends: " . $num_friends; ?></p>
      </div>
      <form action="<?php echo $username; ?>" method="POST">
        <?php
          $profile_user_object = new User($connection, $username);
          if($profile_user_object->is_closed()) {
            header("Location: user_closed.php");
          }
          $logged_in_user_object = new User($connection, $userLoggedIn);

          if($userLoggedIn != $username) {
            if($logged_in_user_object->is_friend($username)) {
              echo '<input type="submit" name="remove_friend" class="danger" value="Remove frined"><br>';
            } else if ($logged_in_user_object->is_received_request($username)){
              echo '<input type="submit" name="respond_friend" class="warning" value="Response to Request"><br>';
            } else if ($logged_in_user_object->is_sent_request($username)){
              echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
            } else {
              echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';
            }
          }
         ?>
      </form>
      <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="Post something">
    </div>

    <div class="main_column column">
      <?php echo $username; ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Post something!</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>This will appear on the user's profile page and also their newsfeed for your friends to see!</p>
            <form class="profile_post" action="" method="POST">
              <div class="form-group">
                <textarea class="form-control" name="post_body"></textarea>
                <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
                <input type="hidden" name="user_to" value="<?php echo $username; ?>">
              </div>
            </form>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="submit_profile_post" name="post_button">Post</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->

  </div>
</body>
</html>
