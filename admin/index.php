<?php
	print_r($_POST);
session_start();
define('IS_INTERNAL',1);
require "../core/settings.php";
try {
		$db = new PDO("mysql:host={$setting['dbip']};dbname={$setting['dbname']};charset=utf8", $setting['dbusername'], $setting['dbpassword']);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo "<b>Database Connection Failure: " . $e->getMessage() . "</b>";
		die("Please correct this error.");
	}
global $notice;
if($_GET['action'] == 'logout')
{
	//$_SESSION['user_id'] == null;
	//$_SESSION['user_username'] == null;
	if($_COOKIE['user_id'])
	{
		setcookie('user_id', null, time() + 9999999999999999);
		setcookie('user_username', null, time() + 99999999999999999);
		$notice = "You have been succesfully logged out!";
	}else{
		$error = "You have already been logged out!";
	}
}
if($_GET['return'])
{
	if(isset($_COOKIE['user_id']) && isset($_COOKIE['user_username']) && $_COOKIE['user_id'] != null && !isset($_GET['action']))
	{
		$sql="SELECT COUNT(*) FROM admin WHERE id='{$_COOKIE['user_id']}' AND username='{$_COOKIE['user_username']}'";
		if ($res = $db->query($sql)) {
			print("phase1");
			if ($res->fetchColumn() == 1) {
				if($_POST['return']){
					header("location:admin.php?x={$_POST['return']}");
				}else{
					header("location:admin.php");
				}
			}
		}
	}
}
if($_POST['myusername']){
	ini_set ("display_errors", "1");
	error_reporting(E_ALL);
	
	//ob_start();
	
	// This will connect you to your database
	// Defining your login details into variables
	$myusername=$_POST['myusername'];
	$mypassword=$_POST['mypassword'];
	$encrypted_mypassword=md5($mypassword); //MD5 Hash for security
	
	// Check for common basic SQL injection methods. Slashes and quotes can trick php or mysql into executing external code, thus compromising the entire software.
	$myusername = stripslashes($myusername);
	$mypassword = stripslashes($mypassword);
	//$myusername = mysqli_real_escape_string($db, $myusername);
	//$mypassword = mysqli_real_escape_string($db, $mypassword);
	
	$sql="SELECT COUNT(*) FROM admin WHERE username='$myusername' and password='$encrypted_mypassword'";

	if ($res = $db->query($sql)) {
		$cols = $res->fetchColumn();
		print_r($_POST);
		print($res->fetchColumn());
		print($cols);
		if($cols > 0){
			print("YAY");
			print("phase2");
			$sql="SELECT COUNT(*) FROM admin WHERE username='$myusername' and password='$encrypted_mypassword'";
	//$result=mysqli_query($db, $sql);
	
	// Checking table row
	//$count=mysqli_num_rows($result);
	// If username and password is a match, the count will be 1
	
		// If everything checks out, you will now be forwarded to admin.php
		//$user = mysqli_fetch_assoc($result);
				foreach ($db->query($sql) as $row) {
					print("phase3");
					print_r($row);
					if($_POST["keeplogged"]){
						setcookie('user_id', $row['id'], time()+3600*9001); // Todo: domain.
						setcookie('user_username', $row['username'], time()+3600*9001); // Todo: domain.
					}else{
						setcookie('user_id', $row['id'], time()+3600); // Todo: domain.
						setcookie('user_username', $row['username'], time()+3600); // Todo: domain.
					}
				}
				if($_GET['return']){
					header("location:admin.php?x={$_GET['return']}");
				}else{
					header("location:admin.php");
				}
			}
		}
	//If the username or password is wrong, you will receive this message below.
		else
		{
			$error = "Wrong Username or Password";
		}
	}
//ob_end_flush();
?>
<html><head>
<link href="../default.css" rel="stylesheet" type="text/css" />
</head><body bgcolor="black">
<center><h1 class="label">Administrator Control Panel</h1></center><br>
<?php if($error){
	echo "<div align=\"center\" class=\"error\"><div style=\"float:left;\"><img src=\"../images/error.png\"></div> {$error}</div>";
}
if($notice){
	echo "<div align=\"center\" class=\"success\"><div style=\"float:left;\"><img src=\"../images/success.png\" ></div> {$notice}</div>";
}?>
<br>
<!-- Reminder: Redesign this login site -->
<form name="form1" method="post" action="./">
<input type="hidden" name="return" value="<?php echo $_GET["return"]; ?>">
<table width="300" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#E6ECEF" class="login">
<tr>
<td colspan="3"><strong><center>Administrator Login</center></strong></td>
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
<td colspan="3"><center><input type="submit" name="Submit" value="Login"></center></td>
</tr>
<tr>
<td colspan="3"><center><input type="checkbox" name="keeplogged"><br>Remember Me</center></td>
</tr>
</table>
</form>
<center>
<br><br><font color="white">Return to </font><a href="../"><b>Front-End</b></a>
</center>
</body></html>