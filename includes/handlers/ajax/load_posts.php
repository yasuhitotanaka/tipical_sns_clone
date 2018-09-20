<?php
  include("../../../settings/config.php");
  include("../../classes/User.php");
  include("../../classes/Post.php");

  $LIMIT = 10;
  
  $posts = new Post($connection, $_REQUEST['userLoggedIn']);
  $posts->load_post_friends($_REQUEST, $LIMIT);
?>
