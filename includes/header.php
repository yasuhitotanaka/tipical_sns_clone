<?php

if(isset($_SESSION['username'])){
  $userLoggedIn = $_SESSION['username'];
  $user_details_query = mysqli_query($connection, "SELECT first_name FROM users WHERE username='$userLoggedIn'");
  $row = mysqli_fetch_array($user_details_query);
  $firstname = $row['first_name'];
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
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>

  </head>
  <body>
    <div class="top_var">
      <div class="logo">
        <a href="index.php">Swirlfeed!</a>
      </div>
      <nav>
        <a href="#">
          <?php echo $firstname; ?>
        </a>
        <a href="index.php">
          <i class="fa fa-home fa-lg"></i>
        </a>
        <a href="#">
          <i class="fa fa-envelope fa-lg"></i>
        </a>
        <a href="#">
          <i class="fa fa-bell fa-lg"></i>
        </a>
        <a href="#">
          <i class="fa fa-users fa-lg"></i>
        </a>
        <a href="#">
          <i class="fa fa-cog fa-lg"></i>
        </a>
      </nav>
    </div>
