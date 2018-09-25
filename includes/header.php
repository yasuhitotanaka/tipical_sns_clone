<?php
  require 'settings/config.php';
  include("includes/classes/User.php");
  include("includes/classes/Post.php");
  include("includes/classes/Message.php");
  include("includes/classes/Notification.php");

  if(isset($_SESSION['username'])){
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($connection, "SELECT last_name, first_name, profile_picture, number_posts, number_likes FROM users WHERE username='$userLoggedIn'");
    $row = mysqli_fetch_array($user_details_query);
  } else {
    header("Location: register.php");
  }

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Welcome to Swirlfeed</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/jquery.Jcrop.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/jquery.Jcrop.js"></script>
    <script src="assets/js/jcrop_bits.js"></script>
    <script src="assets/js/script.js"></script>

  </head>
  <body>
    <div class="top_var">
      <div class="logo">
        <a href="index.php">Swirlfeed!</a>
      </div>
      <nav>
        <?php
          $messages = new Message($connection, $userLoggedIn);
          $num_messages = $messages->get_unread_number();

          $notifications = new Notification($connection, $userLoggedIn);
          $num_notifications = $notifications->get_unread_number();
         ?>
        <a href="<?php echo $userLoggedIn; ?>">
          <?php echo $row['first_name']; ?>
        </a>
        <a href="index.php">
          <i class="fa fa-home fa-lg"></i>
        </a>
        <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
          <i class="fa fa-envelope fa-lg"></i>
          <?php
            if($num_messages > 0)
              echo '<span class="notification_badge" id="unread_message">' . $num_messages . '</span>';
           ?>
        </a>
        <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
          <i class="fa fa-bell fa-lg"></i>
          <?php
            if($num_notifications > 0)
              echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
           ?>
        </a>
        <a href="requests.php">
          <i class="fa fa-users fa-lg"></i>
        </a>
        <a href="#">
          <i class="fa fa-cog fa-lg"></i>
        </a>
        <a href="includes/handlers/logout.php">
          <i class="fa fa-sign-out-alt fa-lg"></i>
        </a>
      </nav>

      <div class="dropdown_data_window" style="height:0px">
        <input type="hidden" id="dropdown_data_type" value="">
      </div>
    </div>

    <script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';

    $(document).ready(function(){

      $('.dropdown_data_window').scroll(function(){
        var inner_height = $('.dropdown_data_window').innerHeight();
        var scroll_top = $('.dropdown_data_window').scrollTop();
        var page = $('.dropdown_data_window').find('.nextPage_DropdownData').val();
        var noMoreData = $('.dropdown_data_window').find('.noMore_DropdownData').val();

        // function for final page
        if((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight)
        && noMoreData == 'false') {
          var pageName;
          var type = $('#dropdown_data_type').val();

          if(type == 'notification')
            pageName = "load_notifications.php";
          else if (type == 'message')
            pageName = "load_messages.php";

          var ajaxRequest = $.ajax({
            url:"includes/handlers/ajax/" + pageName,
            type: "POST",
            data: "page=" + page +"&userLoggedIn=" + userLoggedIn,
            cache: false,

            success: function(response) {
              $('.dropdown_data_window').find('.nextPage_DropdownData').remove();
              $('.dropdown_data_window').find('.noMore_DropdownData').remove();
              $('.dropdown_data_window').append(response);
            }
          });
         }
         return false;
      });
    });
  </script>
