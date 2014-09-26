<?php
session_start();
//$_SESSION['user_id'] == null;
//$_SESSION['user_username'] == null;
setcookie('user_id', null, time() + 9999999999999999);
setcookie('user_username', null, time() + 99999999999999999);
session_destroy();
?>
You have successfully logged out of the control panel.<br><br><br>
Return to <a href="./">Login</a>