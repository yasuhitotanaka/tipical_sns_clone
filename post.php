<?php
  include("includes/header.php");

  if(isset($_GET['id'])) {
    $id = $_GET['id'];
  } else {
    $id = 0;
  }

 ?>


 <div class="wrapper">
   <div class="user_details column">
     <a href="<?php echo $userLoggedIn; ?>">
       <img src="<?php echo $row['profile_picture']; ?>" alt="">
     </a>

     <div class="user_details_left_right">
       <a href="<?php echo $userLoggedIn; ?>">
         <?php echo $row['first_name'] . " " . $row['last_name']; ?>
       </a>
       <br>
       <?php
         echo "Posts: " . $row['number_posts']
         . "<br>" . "Likes: " . $row['number_likes']
        ?>
     </div>
   </div>

   <div class="main_column column" id="main_column">
     <div class="posts_area">
      <?php
        $post = new Post($connection, $userLoggedIn);
        $post->get_signle_post($id);
       ?>
     </div>
   </div>

</div>
