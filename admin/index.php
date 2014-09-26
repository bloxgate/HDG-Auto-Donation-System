<?php
session_start();
define('IS_INTERNAL',1);
require "../core/settings.php";
$db = mysqli_connect($setting['dbip'], $setting['dbusername'], $setting['dbpassword'], $setting['dbname']);

if(isset($_COOKIE['user_id']) && isset($_COOKIE['user_username']) && $_COOKIE['user_id'] != null)
{
	$sql="SELECT * FROM admin WHERE id='{$_COOKIE['user_id']}' AND username='{$_COOKIE['user_username']}'" or die(mysql_error());
	$result=mysqli_query($db, $sql);
	$count=mysqli_num_rows($result);
	if($count==1)
	{
		header("location:admin.php");
	}
}
?>
<html><head>
<link href="default.css" rel="stylesheet" type="text/css" />
</head><body bgcolor="tan">
<center><h2>Administrator Control Panel</h2></center><br><br>

<table width="300" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
<tr>
<form name="form1" method="post" action="checklogin.php">
<td>
<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="tan">
<tr>
<td colspan="3"><strong>Administrator Login </strong></td>
</tr>
<tr>
<td width="78">Username</td>
<td width="6">:</td>
<td width="294"><input name="myusername" type="text" id="myusername"></td>
</tr>
<tr>
<td>Password</td>
<td>:</td>
<td><input name="mypassword" type="password" id="mypassword"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td><input type="submit" name="Submit" value="Login"></td>
</tr>
</table>
</td>
</form>
</tr>
</table>
<center>
<br><br>Return to </font><a href="../"><b>Front-End</b></a>
</center>
</body></html>