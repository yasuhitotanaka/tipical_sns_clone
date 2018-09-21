<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <script src="assets/js/script.js"></script>
    <style media="screen">
      body {
        background-color: #fff;
      }
    </style>
  </head>
  <body>
    <?php
      require 'settings/config.php';
      include("includes/classes/User.php");
      include("includes/classes/Post.php");

     if(isset($_SESSION['username'])){
      $userLoggedIn = $_SESSION['username'];
      $user_details_query = mysqli_query($connection, "SELECT * FROM users WHERE username='$userLoggedIn'");
      $row = mysqli_fetch_array($user_details_query);
    } else {
      header("Location: register.php");
    }

    if (isset($_GET['post_id'])) {
      $post_id = intval($_GET['post_id']);
    }

    $get_likes = mysqli_query($connection, "SELECT likes, added_by FROM posts WHERE id='$post_id'");
    $row =  mysqli_fetch_array($get_likes);
    $total_likes = $row['likes'];
    $user_liked = $row['added_by'];

    $user_details_query = mysqli_query($connection, "SELECT * FROM users WHERE username='$user_liked'");
    $row = mysqli_fetch_array($user_details_query);
    $total_user_likes = $row['number_likes'];

    // Like button
    if (isset($_POST['like_button'])) {
      $total_likes++;
      $query = mysqli_query($connection, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
      $total_user_likes++;
      $user_likes = mysqli_query($connection, "UPDATE users SET number_likes='$total_user_likes' WHERE username='$user_liked'");
      $insert_user = mysqli_query($connection, "INSERT INTO likes VALUES('', '$userLoggedIn', '$post_id')");

      // Insert Notification

    }

    // Unlike button
    if (isset($_POST['unlike_button'])) {
      $total_likes--;
      $query = mysqli_query($connection, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
      $total_user_likes--;
      $user_likes = mysqli_query($connection, "UPDATE users SET number_likes='$total_user_likes' WHERE username='$user_liked'");
      $delete_user = mysqli_query($connection, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
    }

    // Check for previous likes
    $check_query = mysqli_query($connection, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
    $num_rows = mysqli_num_rows($check_query);

    if($num_rows > 0) {
      echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
              <input type="submit" class="comment_like" name="unlike_button" value="unlike">
              <div class="like_value">
                ' . $total_likes . ' Likes
              </div>
            </form>';
    } else {
      echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
              <input type="submit" class="comment_like" name="like_button" value="like">
              <div class="like_value">
                ' . $total_likes . ' Likes
              </div>
            </form>';
    }
  ?>
  </body>
</html>
