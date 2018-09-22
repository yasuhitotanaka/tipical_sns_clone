<?php
  include("../../../settings/config.php");
  include("../../classes/User.php");

  $query = $_POST['query'];
  $userLoggedIn = $_POST['userLoggedIn'];

  $names = explode(" ", $query);

  if(strpos($query, "_") !== false) {
    $users_returned = mysqli_query($connection,
    "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
  } else if (count($names) == 2) {
    $users_returned = mysqli_query($connection,
    "SELECT * FROM users
    WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%')
      AND user_closed='no' LIMIT 8");
  } else {
    $users_returned = mysqli_query($connection,
    "SELECT * FROM users
    WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%')
      AND user_closed='no' LIMIT 8");
  }

  if($query != "") {
    while($row = mysqli_fetch_array($users_returned)) {
      $user = new User($connection, $userLoggedIn);

      if($row['username'] != $userLoggedIn) {
        $mutual_friends = $user->get_mutual_friends($row['username']) . "friends in common";
      } else {
        $mutual_friends = "";
      }

      if($user->is_friend($row['username'])) {
        echo "<div class='result_display'>
                <a href='messages.php?u='" . $row['username'] . "'>
                  <div class='live_search_profile_picture'>
                    <img src='" . $row['profile_picture'] ."' >
                  </div>
                  <div class='live_search_text'>
                    " . $row['first_name'] . " " . $row['last_name'] . "
                    <p>" . $row['username'] . "</p>
                    <p id='grey'>" . $mutual_friends . "</p>
                  </div>
                </a>
              </div>";
      }

    }
  }

?>
