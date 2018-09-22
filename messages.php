<?php
  include("includes/header.php");
  include("includes/classes/User.php");
  include("includes/classes/Post.php");
  include("includes/classes/Message.php");

  $message_object = new Message($connection, $userLoggedIn);

  if(isset($_GET['u'])) {
    $user_to = $_GET['u'];
  } else {
    $user_to = $message_object->get_more_recent_user();
    if($user_to == false) {
      $user_to = 'new';
    }
  }

  if($user_to != "new") {
    $user_to_object = new User($connection, $user_to);
  }
  if(isset($_POST['post_message'])) {
    if(isset($_POST['message_body'])) {
      $body = mysqli_real_escape_string($connection, $_POST['message_body']);
      $date = date("Y-m-d H:i:s");
      $message_object->send_message($user_to, $body, $date);
    }
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
    <?php
      if($user_to != "new") {
        echo "<h4>You and <a href='$user_to'>"
         . $user_to_object->get_first_and_lastname() .
         "</a></h4>
         <hr>
         <br>";
        echo "<div class='loaded_messages' id='scroll_messages'>";
        echo $message_object->get_messages($user_to);
        echo "</div>";
      } else {
        echo "<h4>New Message</h4>";
      }
     ?>
     <div class="messages_post">
       <form action="" method="POST">
        <?php
          if($user_to == "new") {
            echo "Select the friend you would like to message <br><br>";
        ?>

        To: <input type='text'
        onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")'
         name='q' placeholder='Name' autocomplete='off' id='search_text_input'>

        <?php
            echo "<div class='results'></div>";
          } else {
            echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
            echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
          }

          ?>
       </form>
     </div>

     <script>
      let div = document.getElementById("scroll_messages");
      div.scrolltop = div.scrollHeight;
     </script>
  </div>

  <div class="user_details column" id="conversation">
    <h4>Conversations</h4>

    <div class="loaded_conversations">
      <?php echo $message_object->get_conversations(); ?>
    </div>
    <br>
    <a href="messages.php?u=new">New Message</a>
  </div>

</div>
