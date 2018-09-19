<?php
ob_start();
session_start();

$timezone = date_default_timezone_set("Asia/Bangkok");

$connection = mysqli_connect("localhost", "root", "", "social");
if (mysqli_connect_errno()) {
  echo "Fail to connect!" . mysqli_connect_errno();
}

 ?>
