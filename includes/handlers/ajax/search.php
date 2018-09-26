<?php
  include("../../../settings/config.php");
  include("../../classes/User.php");

  $query = $_POST['query'];
  $userLoggedIn = $_POST['userLoggedIn'];
  $names = explode(" ", $query);

  // If query contains an underscore, assume user is searching for username
  if(strpos($query, '_') !== false) {
    $user_return_query = mysqli_query($connection,
      "SELECT * FROM users WHERE username LIKE '%query%' AND user_closed='no' LIMIT 8");

  // If there are two words, assume they are first and last names rspectively
  } else if(count($names) == 2) {
    $user_return_query = mysqli_query($connection,
      "SELECT * FROM users
        WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%')
         AND user_closed='no' LIMIT 8");
  // If there are one word only, assume they are first or last name
  } else {
    $user_return_query = mysqli_query($connection,
      "SELECT * FROM users
        WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%')
         AND user_closed='no' LIMIT 8");
  }

  if($query != "") {
    while($row = mysqli_fetch_array($user_return_query)) {
      $user = new User($connection, $userLoggedIn);

      if($row['username'] != $userLoggedIn) {
        $mutual_friends = $user->get_mutual_friends($row['username']) . "friends in common";
      } else {
        $mutual_friends = "";
      }

      echo "<div class='result_display'>
              <a href='" . $row['username'] . "' style='color: #1455bd'>
                <div class='live_search_profile_picture'>
                  <img src='" . $row['profile_picture'] . "'>
                </div>

                <div class='live_search_text'>
                  " . $row['first_name'] . " " . $row['last_name'] . "
                <p>" . $row['username'] . "</p>
                <p class='grey'>" . $mutual_friends . "</p>
                </div>
              </a>
            </div>";
    }
  }


?>
