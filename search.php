<?php
  include("includes/header.php");

  if(isset($_GET['q'])) {
    $query = $_GET['q'];
  } else {
    $query = "";
  }

  if(isset($_GET['type'])) {
    $type = $_GET['type'];
  } else {
    $type = "name";
  }


 ?>

<div class="wrapper">
  <div class="main_column column" id="main_column">
    <?php
      if($query == "") {
        echo "You must enter something in the search box";
      } else {
        // If query contains an underscore, assume user is searching for username
        if($type == "username") {
          $user_return_query = mysqli_query($connection,
            "SELECT * FROM users WHERE username LIKE '%query%' AND user_closed='no' LIMIT 8");

        } else {
          $names = explode(" ", $query);

          if (count($names) == 3) {
            $user_return_query = mysqli_query($connection,
              "SELECT * FROM users
                WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%')
                 AND user_closed='no'");
          } else if (count($names) == 2) {
            $user_return_query = mysqli_query($connection,
              "SELECT * FROM users
                WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%')
                 AND user_closed='no'");
          } else {
            $user_return_query = mysqli_query($connection,
              "SELECT * FROM users
                WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%')
                 AND user_closed='no'");
          }
        }
        if (mysqli_num_rows($user_return_query) == 0) {
          echo "We cannot find anyone with a " . $type . " like: " . $query;
        } else {
          echo mysqli_num_rows($user_return_query) . " results found: <br> <br>";
        }
        echo "<p id='grey'>Try searching for: </p>";
        echo "<a href='search.php?q=" . $query ."&type=name'>Names<a>,
              <a href='search.php?q=" . $query ."&type=username'>Usernames<a><br><hr class='search_hr'>";

        while($row = mysqli_fetch_array($user_return_query)) {
          $user_object = new User($connection, $user['username']);
          $button = "";
          $mutual_friends = "";

          if($user['username'] != $row['username']) {
            // Generate button depending on friendshipp status
            if($user_object->is_friend($row['username'])) {
              $button = "<input type='submit' name='" . $row['username'] . "' class='danger' value='Remove Friend'>";
            } else if ($user_object->is_received_request($row['username'])) {
              $button = "<input type='submit' name='" . $row['username'] . "' class='warning' value='Respond to request'>";
            } else if ($user_object->is_sent_request($row['username'])) {
              $button = "<input type='submit' class='default' value='Resquest Sent'>";
            } else {
              $button = "<input type='submit' name='" . $row['username'] . "' class='success' value='Add Friend'>";
            }
            $mutual_friends = $user_object->get_mutual_friends($row['username']) . " friends in common";

            // Button forms
            if(isset($_POST[$row['username']])) {
              if ($user_object->is_friend($row['username'])) {
                $user_object->remove_friend($row['username']);
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
              } else if ($user_object->is_received_request($row['username'])) {
                header("Location: requests.php");
              } else if ($user_object->is_sent_request($row['username'])) {
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
              }
            }
          }
          echo "<div class='search_result'>
                  <div class='search_page_friend_buttons'>
                    <form action='' method='POST'>
                    " . $button . "
                    <br>
                    </form>
                  </div>
                  <div class='result_profile_picture'>
                    <a href='" . $row['username'] ."'>
                      <img src='" . $row['profile_picture'] . "' style='height: 100px'>
                    </a>
                  </div>
                  <a href='" . $row['username'] ."'>
                    " . $row['first_name'] . " " . $row['last_name'] . "
                  <p id='grey'>" . $row['username'] . "</p>
                  </a>
                  <br>
                  " .  $mutual_friends . "
                  <br>
                </div>
                <hr class='search_hr'>";
        } //End while
      }
     ?>
  </div>
</div>
