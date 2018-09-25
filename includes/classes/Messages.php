<?php
class Message {
  private $user;
  private $connection;

  public function __construct($connection, $user) {
    $this->connection = $connection;
    $this->user_object = new User($this->connection, $user);
  }

  public function get_more_recent_user() {
    $userLoggedIn = $this->user_object->get_username();

    $query = mysqli_query($this->connection, "SELECT user_to, user_from FROM messages
      WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC LIMIT 1");

    if(mysqli_num_rows($query) == 0) return false;

    $row = mysqli_fetch_array($query);
    $user_to = $row['user_to'];
    $user_from = $row['user_from'];

    return ($user_to != $userLoggedIn) ? $user_to : $user_from;
  }

  public function send_message($user_to, $body, $date) {
    if ($body != "") {
      $userLoggedIn = $this->user_object->get_username();
      $query = mysqli_query($this->connection, "INSERT INTO messages VALUES('', '$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");

    }
  }

  public function get_messages($other_user) {
    $userLoggedIn = $this->user_object->get_username();
    $data = "";

    $query = mysqli_query($this->connection,
    "UPDATE messages SET opened='yes'
    WHERE user_to='$userLoggedIn' AND user_from='$other_user'");

    $get_messages_query = mysqli_query($this->connection,
    "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$other_user') OR (user_to='$other_user' AND user_from='$userLoggedIn')");

    while ($row = mysqli_fetch_array($get_messages_query)) {
      $user_to = $row['user_to'];
      $user_from = $row['user_from'];
      $body = $row['body'];

      $div_top = ($user_to == $userLoggedIn)
       ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
      $data = $data . $div_top . $body . "</div><br><br>";
    }
    return $data;
  }

  public function get_latest_message($userLoggedIn, $user2) {
    $details_array = array();

    $query = mysqli_query($this->connection,
     "SELECT * FROM messages
     WHERE (user_to='$userLoggedIn' AND user_from='$user2')
     OR (user_to='$user2' AND user_from='$userLoggedIn')
      ORDER BY id DESC LIMIT 1");

    $row = mysqli_fetch_array($query);
    $sent_by = ($row['user_to'] == $userLoggedIn)
                ? "They said: " : "You said: ";

    $date_time_now = date("Y-m-d H:i:s");
    $start_date = new DateTime($row['date']);
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
    array_push($details_array, $sent_by);
    array_push($details_array, $row['body']);
    array_push($details_array, $time_message);

    return $details_array;
  }

  public function get_conversations() {
    $userLoggedIn = $this->user_object->get_username();
    $return_string = "";
    $conversations = array();

    $query = mysqli_query($this->connection,
    "SELECT user_to, user_from FROM messages
     WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn'
     ORDER BY id DESC");

    while($row = mysqli_fetch_array($query)) {
      $user_to_push = ($row['user_to'] != $userLoggedIn)
       ? $row['user_to'] : $row['user_from'];

       if(!in_array($user_to_push, $conversations)) {
         array_push($conversations, $user_to_push);
       }
    }

    foreach ($conversations as $username) {
      $user_found_object = new User($this->connection, $username);
      $latest_message_details = $this->get_latest_message($userLoggedIn, $username);

      $dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
      $split = str_split($latest_message_details[1], 12);
      $split = $split[0] . $dots;

      $return_string .= "<a href='messages.php?u=$username'>
                          <div class='user_found_messages'>
                            <img src='" . $user_found_object->get_profile_picture() . "'>
                            " . $user_found_object->get_first_and_lastname() . "
                            <span class='timestamp_smaller' id='grey'>
                            " . $latest_message_details[2] ."
                            </span>
                            <p id='grey'>
                            " . $latest_message_details[0] . $split . "
                            </p>
                          </div>
                        </a>";
    }
    return $return_string;
  }

  public function get_conversations_dropdown($data, $limit) {

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

    $set_viewed_query = mysqli_query($this->connection, "UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn'");

    $query = mysqli_query($this->connection,
    "SELECT user_to, user_from FROM messages
     WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn'
     ORDER BY id DESC");

    while($row = mysqli_fetch_array($query)) {
      $user_to_push = ($row['user_to'] != $userLoggedIn)
       ? $row['user_to'] : $row['user_from'];

       if(!in_array($user_to_push, $conversations)) {
         array_push($conversations, $user_to_push);
       }
    }

    $num_iterations = 0;
    $count = 1;

    foreach ($conversations as $username) {

      if($num_iterations++ < $start) continue;
      if($count > $limit) break; else $count++;

      $is_unread_query = mysqli_query($this->connection,
        "SELECT opened FROM messages WHERE user_to='$userLoggedIn' AND user_from='$username' ORDER BY id DESC");
      $row = mysqli_fetch_array($is_unread_query);
      $style = ($row['opened'] == 'no') ? "background-color: #ddedff;" : "";

      $user_found_object = new User($this->connection, $username);
      $latest_message_details = $this->get_latest_message($userLoggedIn, $username);

      $dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
      $split = str_split($latest_message_details[1], 12);
      $split = $split[0] . $dots;

      $return_string .= "<a href='messages.php?u=$username'>
                          <div class='user_found_messages' style='" . $style ."'>
                            <img src='" . $user_found_object->get_profile_picture() . "'>
                            " . $user_found_object->get_first_and_lastname() . "
                            <span class='timestamp_smaller' id='grey'>
                            " . $latest_message_details[2] ."
                            </span>
                            <p id='grey'>
                            " . $latest_message_details[0] . $split . "
                            </p>
                          </div>
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
          <p style='text-align: center;'>No more messages to load!</p>";
    }

    return $return_string;
  }

  public function get_unread_number() {
    $userLoggedIn = $this->user_object->get_username();
    $query = mysqli_query($this->connection, "SELECT * FROM messages WHERE viewed='no' AND user_to='$userLoggedIn'");
    return mysqli_num_rows($query);
  }

}
?>
