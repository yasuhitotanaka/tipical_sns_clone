<?php
include("includes/header.php");

if(isset($_POST['post'])) {

  $uploadOK = 1;
  $image_name = $_FILES['file_to_upload']['name'];
  $error_message = "";

  if ($image_name != "") {
    $target_directory = "assets/images/posts/";
    $image_name = $target_directory . uniqid() . basename($image_name);
    $image_file_type = pathinfo($image_name, PATHINFO_EXTENSION);

    if ($_FILES['file_to_upload']['size'] > 10000000) {
      $error_message = "Sorry your file is too large";
      $uploadOK = 0;
    }

    if(
      strtolower($image_file_type) != "jpeg" &&
      strtolower($image_file_type) != "jpg" &&
      strtolower($image_file_type) != "png"
    ) {
      $error_message = "Sorry, only jpeg, jpg, png files are allowed";
      $uploadOK = 0;
    }

    if ($uploadOK) {
      if (move_uploaded_file($_FILES['file_to_upload']['tmp_name'], $image_name)) {

      } else {
        $uploadOK = 0;
      }
    }
  }

  if ($uploadOK) {
    $post = new Post($connection, $userLoggedIn);
    $post->submit_post($_POST['post_text'], 'none', $image_name);
  } else {
    echo "<div style='text-align:center;' class='alert alert-error'>
            $error_message;
          </div>";
  }

}

?>
<div class="wrapper">
  <div class="user_details column">
    <a href="<?php echo $userLoggedIn; ?>">
      <img src="<?php echo $user['profile_picture']; ?>" alt="">
    </a>

    <div class="user_details_left_right">
      <a href="<?php echo $userLoggedIn; ?>">
        <?php echo $user['first_name'] . " " . $user['last_name']; ?>
      </a>
      <br>
      <?php
        echo "Posts: " . $user['number_posts']
        . "<br>" . "Likes: " . $user['number_likes']
       ?>
    </div>
  </div>

  <div class="main_column column">
    <form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
      <input type="file" name="file_to_upload" id="file_to_upload" value="upload">
      <textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>
      <input type="submit" name="post" id="post_button" value="Post">
    </form>
    <hr>
    <div class="posts_area"></div>
    <img id="loading" src="assets/images/icons/loading.gif" alt="">
  </div>

  <div class="user_details column">
    <div class="trends">
      <?php
        $query = mysqli_query($connection, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");

        foreach ($query as $row) {
          $word = $row['title'];
          $word_dot = strlen($word) >= 14 ? "..." : "";

          $trimed_word = str_split($word, 14);
          $trimed_word = $trimed_word[0];

          echo "<div style='padding: 1px'>";
          echo $trimed_word . $word_dot;
          echo "<br></div>";

        }
       ?>
    </div>
  </div>
    <script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';

    $(document).ready(function(){
      $('#loading').show();

      $.ajax({
        url:"includes/handlers/ajax/load_posts.php",
        type: "POST",
        data: "page=1&userLoggedIn=" + userLoggedIn,
        cache: false,

        success: function(data) {
          $('#loading').hide();
          $('.posts_area').html(data);
        }
      });

      $(window).scroll(function(){
        var height = $('.posts_area').height();
        var scoll_top = $(this).scrollTop();
        var page = $('.posts_area').find('.nextPage').val();
        var noMorePosts = $('.posts_area').find('.noMorePosts').val();

        // function for final page
        if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight)
         && noMorePosts == 'false') {
          $('#loading').show();

          var ajaxRequest = $.ajax({
            url:"includes/handlers/ajax/load_posts.php",
            type: "POST",
            data: "page=" + page +"&userLoggedIn=" + userLoggedIn,
            cache: false,

            success: function(response) {
              $('.posts_area').find('.nextPage').remove();
              $('.posts_area').find('.noMorePosts').remove();
              $('#loading').hide();
              $('.posts_area').append(response);
            }
          });
         }
         return false;
      });
    });
  </script>
</div>

  </body>
</html>
