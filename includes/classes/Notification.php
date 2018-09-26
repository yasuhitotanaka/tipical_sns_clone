<?php
class Notification {
  private $user;
  private $connection;

  public function __construct($connection, $user) {
    $this->connection = $connection;
    $this->user_object = new User($this->connection, $user);
  }

  public function get_unread_number() {
    $userLoggedIn = $this->user_object->get_username();
    $query = mysqli_query($this->connection, "SELECT * FROM notifications WHERE viewed='no' AND user_to='$userLoggedIn'");
    return mysqli_num_rows($query);
  }

  public function get_notifications($data, $limit) {

    $page = $data['page'];
    $userLoggedIn = $this->user_object->get_username();
    $return_string = "";
    $conversations = array();
    $start = 0;

    if ($page == 1) {
      $start = 0;
    } else {
      $start = ($page - 1) * $limit;
    }

    $set_viewed_query = mysqli_query($this->connection,
      "UPDATE notifications SET viewed='yes' WHERE user_to='$userLoggedIn'");

    $query = mysqli_query($this->connection,
    "SELECT * FROM notifications
     WHERE user_to='$userLoggedIn' ORDER BY id DESC");

     if (mysqli_num_rows($query) == 0) {
       echo "You have no notifications";
       return;
     }

    $num_iterations = 0;
    $count = 1;

    while ($row = mysqli_fetch_array($query)) {

      if($num_iterations++ < $start) continue;
      if($count > $limit) break; else $count++;

      $user_from = $row['user_from'];

      $query = mysqli_query($this->connection, "SELECT * FROM users WHERE username='$user_from'");
      $user_data = mysqli_fetch_array($query);

      // Timeframe
      $date_time_now = date("Y-m-d H:i:s");
      $start_date = new DateTime($row['datetime']);
      $end_date =  new DateTime($date_time_now);
      $interval = $start_date->diff($end_date);

      if ($interval->y >= 1) {
        if ($interval->y == 1) {
          $time_message = $interval->y . " year ago";
        } else {
          $time_message = $interval->y . " years ago";
        }
      } else if ($interval->m >= 1) {
        if($interval->d == 0) {
          $days = " ago";
        } else if ($interval->d == 1) {
          $days = $interval->d . "day ago";
        } else {
          $days = $interval->d . "days ago";
        }
        if ($interval->m == 1) {
          $time_message = $interval->m . " month" . $days;
        } else {
          $time_message = $interval->m . " months" . $days;
        }
      } else if ($interval->d >= 1) {
        if ($interval->d == 1) {
          $time_message = "Yesterday";
        } else {
          $time_message = $interval->d . "days ago";
        }
      } else if ($interval->h >= 1) {
        if ($interval->h == 1) {
          $time_message = $interval->h . "hour ago";
        } else {
          $time_message = $interval->h . "hours ago";
        }
      } else if ($interval->i >= 1) {
        if ($interval->i == 1) {
          $time_message = $interval->i . "minute ago";
        } else {
          $time_message = $interval->i . "minutes ago";
        }
     } else {
      if ($interval->s < 30) {
        $time_message = "Just now";
      } else {
        $time_message = $interval->s . "seconds ago";
      }
    }

      $opened = $row['opened'];
      $style = ($row['opened'] == 'no') ? "background-color: #ddedff;" : "";

      $return_string .= "<a href='" . $row['link'] ."'>
                          <div class='result_display result_display_notification' style='" . $style . "'>
                            <div class='notifications_profile_picture'>
                              <img src='" . $user_data['profile_picture'] . "'>
                            </div>
                          </div>
                          <p class='timestamp_smaller' id='grey'>" . $time_message ." </p>
                          " . $row['message'] ."
                        </a>";
    }

    // If posts were loaded
    if($count > $limit) {
      $return_string .=
        "<input type='hidden' class='nextPage_DropdownData' value=''"
         . ($page + 1) .
         "'><input type='hidden' class='noMore_DropdownData' value='false'>";
    }
    else{
      $return_string .=
       "<input type='hidden' class='nextPage_DropdownData' value='true'>
          <p style='text-align: center;'>No more notifications to load!</p>";
    }

    return $return_string;
  }

  public function insert_notification($post_id, $user_to, $type) {
    $userLoggedIn = $this->user_object->get_username();
    $userLoggedInName = $this->user_object->get_first_and_lastname();

    $date_time = date("Y-m-d H:i:s");

    switch($type) {
      case 'comment':
        $message = $userLoggedInName . " commented on your post";
        break;
      case 'like':
        $message = $userLoggedInName . " liked your post";
        break;
      case 'profile_post':
        $message = $userLoggedInName . " posted on your profile";
        break;
      case 'comment_non_owner':
        $message = $userLoggedInName . " commented on a post you commented on";
        break;
      case 'profile_comment':
        $message = $userLoggedInName . " commented on your profile post";
        break;
    }

    $link = "post.php?id=" . $post_id;

    $insert_query =
      mysqli_query($this->connection,
        "INSERT INTO notifications VALUES ('', '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
  }

}

?>
