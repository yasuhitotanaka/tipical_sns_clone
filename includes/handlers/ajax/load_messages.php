<?php
  include("../../../settings/config.php");
  include("../../classes/User.php");
  include("../../classes/Message.php");

  $LIMIT = 7;

  $messages = new Message($connection, $_REQUEST['userLoggedIn']);
  echo $messages->get_conversations_dropdown($_REQUEST, $LIMIT);
?>
