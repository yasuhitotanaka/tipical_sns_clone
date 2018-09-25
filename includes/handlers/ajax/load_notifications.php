<?php
  include("../../../settings/config.php");
  include("../../classes/User.php");
  include("../../classes/Notification.php");

  $LIMIT = 7;

  $notifications = new Notification($connection, $_REQUEST['userLoggedIn']);
  echo $notifications->get_notifications($_REQUEST, $LIMIT);
?>
