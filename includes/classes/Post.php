<?php
class Post {
  private $user;
  private $connection;

  public function __construct($connection, $user) {
    $this->connection = $connection;
    $this->user = new User($this->connection, $user);
  }

  public function submit_post($body, $user_to, $image_name) {
    $body = strip_tags($body); // remove html tags
    $body = mysqli_real_escape_string($this->connection, $body);
    $check_empty = preg_replace('/\s+/', '', $body); // delete all spaces

    if($check_empty != "") {

      $body_array = preg_split("/\s+/", $body);

      foreach ($body_array as $key => $value) {
        if(strpos($value, "www.youtube.com/watch?v=") != false) {

          // https://www.youtube.com/watch?v=lvP4F4S1VKo&list=PLGO1qX_1g9_rT7tGrPScZFiXG6rQC9dAZ
          $link = preg_split("!&!", $value);
          $value = preg_replace("!watch\?v=!", "embed/", $link[0]);
          $value = "<br><iframe width=\'420\' height=\'315\' src=\'" . $value . "\'></iframe><br>";
          $body_array[$key] = $value;
          }
        }
        $body = implode(" ", $body_array);
      }

      $date_added = date("Y-m-d H:i:s");
      $added_by = $this->user->get_username();

      if ($user_to == $added_by) $user_to = "none";

      $query = mysqli_query($this->connection, "INSERT INTO posts VALUES ('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$image_name') ");
      $returned_id = mysqli_insert_id($this->connection);

      // Inser notification
      if ($user_to != 'none') {
          $notification = new Notification($this->connection, $added_by);
          $notification->insert_notification($returned_id, $user_to, "profile_post");
      }

      // Update post conut for user
      $number_posts = $this->user->get_number_post();
      $number_posts++;
      $update_query = mysqli_query($this->connection, "UPDATE users SET number_posts='$number_posts' WHERE username='$added_by'");

      $stopWords = "a about above across after again against all almost alone along already
			 also although always among am an and another any anybody anyone anything anywhere are
			 area areas around as ask asked asking asks at away b back backed backing backs be became
			 because become becomes been before began behind being beings best better between big
			 both but by c came can cannot case cases certain certainly clear clearly come could
			 d did differ different differently do does done down down downed downing downs during
			 e each early either end ended ending ends enough even evenly ever every everybody
			 everyone everything everywhere f face faces fact facts far felt few find finds first
			 for four from full fully further furthered furthering furthers g gave general generally
			 get gets give given gives go going good goods got great greater greatest group grouped
			 grouping groups h had has have having he her here herself high high high higher
		     highest him himself his how however i im if important in interest interested interesting
			 interests into is it its itself j just k keep keeps kind knew know known knows
			 large largely last later latest least less let lets like likely long longer
			 longest m made make making man many may me member members men might more most
			 mostly mr mrs much must my myself n necessary need needed needing needs never
			 new new newer newest next no nobody non noone not nothing now nowhere number
			 numbers o of off often old older oldest on once one only open opened opening
			 opens or order ordered ordering orders other others our out over p part parted
			 parting parts per perhaps place places point pointed pointing points possible
			 present presented presenting presents problem problems put puts q quite r
			 rather really right right room rooms s said same saw say says second seconds
			 see seem seemed seeming seems sees several shall she should show showed
			 showing shows side sides since small smaller smallest so some somebody
			 someone something somewhere state states still still such sure t take
			 taken than that the their them then there therefore these they thing
			 things think thinks this those though thought thoughts three through
	         thus to today together too took toward turn turned turning turns two
			 u under until up upon us use used uses v very w want wanted wanting
			 wants was way ways we well wells went were what when where whether
			 which while who whole whose why will with within without work
			 worked working works would x y year years yet you young younger
			 youngest your yours z lol haha omg hey ill iframe wonder else like
             hate sleepy reason for some little yes bye choose";

      //Convert stop words into array - split at white space
      $stopWords = preg_split("/[\s,]+/", $stopWords);
      // $no_punctuation = preg_replace("/[^a-zA-Z0-9]+/", "", $body);
      $no_punctuation = $body;
      if (
        strpos($no_punctuation, "height") === false &&
        strpos($no_punctuation, "width") === false &&
        strpos($no_punctuation, "http") === false
      ) {
        $no_punctuation = preg_split("/[\s,]+/", $no_punctuation);

        foreach($stopWords as $value) {
          foreach($no_punctuation as $key => $value2) {
            if (strtolower($value) == strtolower($value2)) $no_punctuation[$key] = "";
          }
        }

        foreach ($no_punctuation as $value) {
          $this->calculate_trend(ucfirst($value));
        }
      }
  }

  public function calculate_trend($term) {
    if($term != '') {
      $query = mysqli_query($this->connection, "SELECT * FROM trends WHERE title='$term'");

      if(mysqli_num_rows($query) == 0) {
        $insert_query = mysqli_query($this->connection, "INSERT INTO trends VALUES('$term', '1') ");
      } else {
        $insert_query = mysqli_query($this->connection, "UPDATE trends SET hits=hits+1 WHERE title='$term' ");
      }

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
        $id = $row['id'];
        $added_by = $row['added_by'];
        $body = $row['body'];
        $user_to = $row['user_to'];
        $image_path = $row['image'];

        if($row['user_to'] == "none") {
          $user_to = "";
        } else {
          $user_to_object = new User($this->connection, $user_to);
          $user_to_name = $user_to_object->get_first_and_lastname();
          $user_to = "to <a href='" . $user_to . "'>" . $user_to_name . "</a>";
        }

        $added_by_object = new User($this->connection, $row['added_by']);
        if ($added_by_object->is_closed()) continue;

        $user_logged_object = new User($this->connection, $userLoggedIn);
        if($user_logged_object->is_friend($added_by)) {

          if ($num_iterations++ < $start) continue;
          if ($count > $limit) break; else $count++;

          if ($userLoggedIn == $added_by) {
            $delete_button =
              "<buttton class='delete_button btn-danger' id='post$id'>X</button>";
          } else {
            $delete_button = "";
          }

          $user_details_query = mysqli_query($this->connection, "SELECT first_name, last_name, profile_picture FROM users WHERE username='$added_by'");
          $user_row = mysqli_fetch_array($user_details_query);
          $first_name = $user_row['first_name'];
          $last_name = $user_row['last_name'];
          $profile_picture = $user_row['profile_picture'];
          ?>

          <script>
            // load css in each iframes
            $('iframe').on('load', function() {
              $('iframe').contents().find("head")
              .append('<link rel="stylesheet" href="assets/css/style.css">');
            });

            function toggle<?php echo $id; ?>() {
              let target = $(event.target);

              if(!target.is("a")) {
                let element = document.getElementById("toggleComment<?php echo $id; ?>");

                if(element.style.display == "block") {
                  element.style.display = "none";
                } else {
                  element.style.display = "block";
                }
              }
            }
          </script>

          <?php

          $comments_check = mysqli_query($this->connection, "SELECT * FROM comments WHERE post_id='$id'");
          $comments_check_num = mysqli_num_rows($comments_check);

          // Timeframe
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

        if ($image_path != "") {
          $image_div = "<div class='posted_image'>
                          <img src='$image_path'>
                        </div>";
        } else {
          $image_div = "";
        }

        $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
                  <div class='post_profile_pic'>
                    <img src='$profile_picture' width=50>
                  </div>
                  <div class='posted_by'>
                    <a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;
                    $time_message
                    $delete_button
                  </div>
                  <div id='post_body'>
                    $body
                    <br>
                    $image_div
                    <br>
                    <br>
                  </div>

                  <div class='newsfeedPostOptions'>
                    Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
                    <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                  </div>

                </div>
                <div class='post_comment' id='toggleComment$id'>
                  <iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
                </div>
                <hr>";
        }
    ?>
    <script>
      $(document).ready(function(){
        $('#post<?php echo $id; ?>').on('click', function(){
          bootbox.confirm("Are you sure you want to delete this post?", function(result){
            $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result: result});
            if (result) location.reload();
          })
        });
      })
    </script>
    <?php
      } // End while loop
        if($count > $limit) {
          $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                    <input type='hidden' class='noMorePosts value='false>";
        } else {
          $str .= "<input type='hidden' class='nextPage' value='true'>
                    <input type='hidden' class='noMorePosts value='false>
                    <p>No more posts to show!</p>";

        }
      }
      echo $str;
    }

  public function load_profile_posts($data, $limit) {

      $page = $data['page'];
      $profile_user = $data['profile_usernname'];
      $userLoggedIn = $this->user->get_username();

      if($page == 1) {
        $start = 0;
      } else {
        $start = ($page - 1) * $limit;
      }

      $str = "";
      $data_query =
        mysqli_query($this->connection, "SELECT * FROM posts
         WHERE deleted='no'
         AND ((added_by='$profile_user' and user_to='none') OR user_to='$profile_user')
         ORDER BY id DESC");

      if(mysqli_num_rows($data_query) > 0) {
        $num_iterations = 0; // number of results checked (not necessarilty posted)
        $count = 1;

        while($row = mysqli_fetch_array($data_query)) {
          $id = $row['id'];
          $added_by = $row['added_by'];
          $body = $row['body'];
          $user_to = $row['user_to'];

          if ($num_iterations++ < $start) continue;
          if ($count > $limit) break; else $count++;

          if ($userLoggedIn == $added_by) {
            $delete_button =
              "<buttton class='delete_button btn-danger' id='post$id'>X</button>";
          } else {
            $delete_button = "";
          }

          $user_details_query = mysqli_query($this->connection, "SELECT first_name, last_name, profile_picture FROM users WHERE username='$added_by'");
          $user_row = mysqli_fetch_array($user_details_query);
          $first_name = $user_row['first_name'];
          $last_name = $user_row['last_name'];
          $profile_picture = $user_row['profile_picture'];
          ?>

          <script>
            // load css in each iframes
            $('iframe').on('load', function() {
              $('iframe').contents().find("head")
              .append('<link rel="stylesheet" href="assets/css/style.css">');
            });

            function toggle<?php echo $id; ?>() {
              let target = $(event.target);

              if(!target.is("a")) {
                let element = document.getElementById("toggleComment<?php echo $id; ?>");

                if(element.style.display == "block") {
                  element.style.display = "none";
                } else {
                  element.style.display = "block";
                }
              }
            }
          </script>

          <?php

          $comments_check = mysqli_query($this->connection, "SELECT * FROM comments WHERE post_id='$id'");
          $comments_check_num = mysqli_num_rows($comments_check);

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
        $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
                  <div class='post_profile_pic'>
                    <img src='$profile_picture' width=50>
                  </div>
                  <div class='posted_by'>
                    <a href='$added_by'> $first_name $last_name </a>&nbsp;&nbsp;&nbsp;&nbsp;
                    $time_message
                    $delete_button
                  </div>
                  <div id='post_body'>
                    $body
                    <br>
                    <br>
                    <br>
                  </div>

                  <div class='newsfeedPostOptions'>
                    Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
                    <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                  </div>

                </div>
                <div class='post_comment' id='toggleComment$id'>
                  <iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
                </div>
                <hr>";
      ?>
      <script>
        $(document).ready(function(){
          $('#post<?php echo $id; ?>').on('click', function(){
            bootbox.confirm("Are you sure you want to delete this post?", function(result){
              $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result: result});
              if (result) location.reload();
            })
          });
        })
      </script>
      <?php
        } // End while loop
          if($count > $limit) {
            $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                      <input type='hidden' class='noMorePosts value='false>";
          } else {
            $str .= "<input type='hidden' class='nextPage' value='true'>
                      <input type='hidden' class='noMorePosts value='false>
                      <p>No more posts to show!</p>";

          }
        }
        echo $str;
      }

  public function get_signle_post($post_id){
    $userLoggedIn = $this->user->get_username();
    $opened_query = mysqli_query($this->connection,
      "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

    $str = "";
    $data_query = mysqli_query($this->connection,
      "SELECT * FROM posts WHERE deleted='no' AND id='$post_id'");

    if(mysqli_num_rows($data_query) > 0) {

      $row = mysqli_fetch_array($data_query);
      $id = $row['id'];
      $added_by = $row['added_by'];
      $body = $row['body'];
      $user_to = $row['user_to'];

      if($row['user_to'] == "none") {
        $user_to = "";
      } else {
        $user_to_object = new User($this->connection, $user_to);
        $user_to_name = $user_to_object->get_first_and_lastname();
        $user_to = "to <a href='" . $user_to . "'>" . $user_to_name . "</a>";
      }

      $added_by_object = new User($this->connection, $row['added_by']);
      if ($added_by_object->is_closed()) return;

      $user_logged_object = new User($this->connection, $userLoggedIn);
      if($user_logged_object->is_friend($added_by)) {

        if ($userLoggedIn == $added_by) {
          $delete_button =
            "<buttton class='delete_button btn-danger' id='post$id'>X</button>";
        } else {
          $delete_button = "";
        }

        $user_details_query = mysqli_query($this->connection, "SELECT first_name, last_name, profile_picture FROM users WHERE username='$added_by'");
        $user_row = mysqli_fetch_array($user_details_query);
        $first_name = $user_row['first_name'];
        $last_name = $user_row['last_name'];
        $profile_picture = $user_row['profile_picture'];
        ?>

        <script>
          // load css in each iframes
          $('iframe').on('load', function() {
            $('iframe').contents().find("head")
            .append('<link rel="stylesheet" href="assets/css/style.css">');
          });

          function toggle<?php echo $id; ?>() {
            let target = $(event.target);

            if(!target.is("a")) {
              let element = document.getElementById("toggleComment<?php echo $id; ?>");

              if(element.style.display == "block") {
                element.style.display = "none";
              } else {
                element.style.display = "block";
              }
            }
          }
        </script>

        <?php

        $comments_check = mysqli_query($this->connection, "SELECT * FROM comments WHERE post_id='$id'");
        $comments_check_num = mysqli_num_rows($comments_check);

        // Timeframe
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
      $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
                <div class='post_profile_pic'>
                  <img src='$profile_picture' width=50>
                </div>
                <div class='posted_by'>
                  <a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;
                  $time_message
                  $delete_button
                </div>
                <div id='post_body'>
                  $body
                  <br>
                  <br>
                  <br>
                </div>

                <div class='newsfeedPostOptions'>
                  Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
                  <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                </div>

              </div>
              <div class='post_comment' id='toggleComment$id'>
                <iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
              </div>
              <hr>";
      ?>
      <script>
        $(document).ready(function(){
          $('#post<?php echo $id; ?>').on('click', function(){
            bootbox.confirm("Are you sure you want to delete this post?", function(result){
              $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result: result});
              if (result) location.reload();
            })
          });
        });
      </script>

    <?php
        } else {
          echo "You cannot see this post bacause you are not friends with this user";
          return;
        }
      }  else {
        echo "No post found. If you clicked a link, it may be broken.";
        return;
      }
      echo $str;
    }




}

?>
