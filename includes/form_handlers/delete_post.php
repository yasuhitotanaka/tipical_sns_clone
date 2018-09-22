<?php
  include("../../settings/config.php");
  include("../classes/User.php");
  include("../classes/Post.php");

  if(isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
  }

  if(isset($_POST['result'])) {
    if($_POST['result'] == 'true') {
      $query = mysqli_query($connection, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
    }
  }
?>
