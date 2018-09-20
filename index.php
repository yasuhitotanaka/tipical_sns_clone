<?php
require 'settings/config.php';
include("includes/header.php");
include("includes/classes/User.php");
include("includes/classes/Post.php");

if(isset($_POST['post'])) {
  $post = new Post($connection, $userLoggedIn);
  $post->submit_post($_POST['post_text'], 'none');
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

  <div class="main_column column">
    <form class="post_form" action="index.php" method="POST">
      <textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>
      <input type="submit" name="post" id="post_button" value="Post">
    </form>
    <hr>
    <div class="posts_area"></div>
    <img id="loading" src="assets/images/icons/loading.gif" alt="">
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
