<?php
class User {
  private $user;
  private $connection;

  public function __construct($connection, $user) {
    $this->connection = $connection;
    $user_details_query = mysqli_query($connection, "SELECT * FROM users WHERE username='$user'");
    $this->user = mysqli_fetch_array($user_details_query);
  }

  public function get_username() {
    return $this->user['username'];
  }

  public function get_number_post() {
    $username = $this->user['username'];
    $query = mysqli_query($this->connection, "SELECT number_posts FROM users WHERE username='$username'");
    $row = mysqli_fetch_array($query);
    return $row['number_posts'];
  }

  public function get_first_and_lastname() {
    $username = $this->user['username'];
    $query = mysqli_query($this->connection, "SELECT first_name, last_name FROM users WHERE username='$username'");
    $row = mysqli_fetch_array($query);
    return $row['first_name'] . " " . $row['last_name'];
  }

  public function get_profile_picture() {
    $username = $this->user['username'];
    $query = mysqli_query($this->connection, "SELECT profile_picture FROM users WHERE username='$username'");
    $row = mysqli_fetch_array($query);
    return $row['profile_picture'];
  }

  public function is_closed() {
    $username = $this->user['username'];
    $query = mysqli_query($this->connection, "SELECT user_closed FROM users WHERE username='$username'");
    $row = mysqli_fetch_array($query);

    return ($row['user_closed'] == 'yes') ? true : false;
  }

  public function is_friend($username_to_check) {
    $username_comma = "," . $username_to_check . ",";
    // find specified username
    return (strstr($this->user['friend_array'], $username_comma)
      || $username_to_check == $this->user['username'] ) ? true : false;
  }

  public function is_received_request($user_from) {
    $user_to = $this->user['username'];
    $check_request_query = mysqli_query($this->connection,
      "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
    return (mysqli_num_rows($check_request_query) > 0) ? true : false;
  }

  public function is_sent_request($user_to) {
    $user_from = $this->user['username'];
    $check_request_query = mysqli_query($this->connection,
      "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
    return (mysqli_num_rows($check_request_query) > 0) ? true : false;
  }

  public function remove_friend($user_to_remove) {
    $logged_in_user = $this->user['username'];

    $query = mysqli_query($this->connection, "SELECT friend_array FROM users WHERE username='$user_to_remove'");
    $row = mysqli_fetch_array($query);
    $friend_array_username = $row['friend_array'];

    $new_frined_array = str_replace($user_to_remove . "," , "", $this->user['friend_array']);
    $remove_friend = mysqli_query($this->connection, "UPDATE users SET friend_array='$new_frined_array' WHERE username='$logged_in_user'");

    $new_frined_array = str_replace($logged_in_user . "," , "", $friend_array_username);
    $remove_friend = mysqli_query($this->connection, "UPDATE users SET friend_array='$new_frined_array' WHERE username='$user_to_remove'");

  }

  public function send_request($user_to) {
    $user_from = $this->user['username'];
    $query = mysqli_query($this->connection, "INSERT INTO friend_requests VALUES ('', '$user_to', '$user_from')");
  }

  public function get_friend_array() {
    $username = $this->user['username'];
    $query = mysqli_query($this->connection, "SELECT friend_array FROM users WHERE username='$username'");
    $row = mysqli_fetch_array($query);
    return $row['friend_array'];
  }

}

?>
