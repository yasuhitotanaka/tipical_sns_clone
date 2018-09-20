<?php
class Post {
  private $user;
  private $connection;

  public function __construct($connection, $user) {
    $this->connection = $connection;
    $this->user = new User($this->connection, $user);
  }

  public function submit_post($body, $user_to) {
    $body = strip_tags($body); // remove html tags
    $body = mysqli_real_escape_string($this->connection, $body);
    $check_empty = preg_replace('/\s+/', '', $body); // delete all spaces

    if($check_empty != "") {
      $data_added = date("Y-m-d H:i:s");
      $added_by = $this->user->get_username();

      if ($user_to == $added_by) {
        $user_to = "none";
      }

      $query = mysqli_query($this->connection, "INSERT INTO posts VALUES ('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0') ");
      $returned_id = mysqli_insert_id($this->connection);

      $number_posts = $this->user->get_number_post();
      $number_posts++;
      $update_query = mysqli_query($this->connection, "UPDATE users SET number_posts='$number_posts' WHERE username='$added_by'");
    }
  }

  public function load_post_friends($data, $limit) {

    $page = $data['page'];
    $userLoggedIn = $this->user->get_username();

    if($page == 1) {
      $start = 0;
    } else {
      $start = ($page - 1) * $limit;
    }

    $str = "";
    $data_query = mysqli_query($this->connection, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

    if(mysqli_num_rows($data_query) > 0) {
      $num_iterations = 0; // number of results checked (not necessarilty posted)
      $count = 1;

      while($row = mysqli_fetch_array($data_query)) {
        $added_by = $row['added_by'];
        $body = $row['body'];

        if($row['user_to'] == "none") {
          $user_to = "";
        } else {
          $user_to_object = new User($this->connection, $user_to);
          $user_to_name = $user_to_object->getget_first_and_lastname();
          $user_to = "<a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
        }

        $added_by_object = new User($this->connection, $row['added_by']);
        if ($added_by_object->is_closed()) continue;
        if ($num_iterations++ < $start) continue;
        if ($count > $limit) break; else $count++;

        $user_details_query = mysqli_query($this->connection, "SELECT first_name, last_name, profile_picture FROM users WHERE username='$added_by'");
        $user_row = mysqli_fetch_array($user_details_query);
        $first_name = $user_row['first_name'];
        $last_name = $user_row['last_name'];
        $profile_picture = $user_row['profile_picture'];

        $date_time_now = date("Y-m-d H:i:s");
        $start_date = new DateTime($row['date_added']);
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
      $str .= "<div class='status_post'>
                <div class='post_profile_pic'>
                  <img src='$profile_picture' width=50>
                </div>
                <div class='posted_by'>
                  <a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;
                  $time_message
                </div>
                <div id='post_body'>
                  $body<br>
                </div>
              </div>
              <hr>";
      }
        if($count > $limit) {
          $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                    <input type='hidden' class='noMorePosts value='false>";
        } else {
          $str .= "<input type='hidden' class='nextPage' value='true'>
                    <input type='hidden' class='noMorePosts value='false>
                    <p>No more posts to show!</p>";

        }
      echo $str;
      }
    }
  }
?>