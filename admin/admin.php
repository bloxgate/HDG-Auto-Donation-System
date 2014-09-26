<?php
define('IS_INTERNAL', 1);
require "../core/settings.php";
global $db;
$db = mysqli_connect($setting['dbip'], $setting['dbusername'], $setting['dbpassword'],$setting['dbname']);
// NOTE: Switch to PDO
session_start();
//If your session isn't valid, it returns you to the login screen for protection
if(empty($_COOKIE['user_id']))
{
 header("location:index.php");
}
else
{
	if(isset($_COOKIE['user_id']) && isset($_COOKIE['user_username']))
	{	
		$sql="SELECT * FROM admin WHERE id='{$_COOKIE['user_id']}' AND username='{$_COOKIE['user_username']}'";
		$result=mysqli_query($db, $sql);
		$count=mysqli_num_rows($result);
		if($count==0)
		{
			header("location:index.php");
		}
	}
}

?>
<head>
<link href="default.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php

if (isset($_GET["x"])) 
{
    $x = explode(":",$_GET["x"]);

	if($x[0] == "packages")
	{
		packages();
	}
	elseif($x[0] == "paypal")
	{
		paypal();
	}
	elseif($x[0] == "home")
	{
		home();
	}
	elseif($x[0] == "accounts")
	{
		accounts();
	}
	elseif($x[0] == "logout")
	{
		header("location:logout.php");
	}
	else
	{
		home();
	}
}
else { home(); }

function menu()
{
// Echo the left panel.
	echo '<div id="fulladmin">';
	echo '<div id="adminleft">';
	//Add a function and change this line to it.
	echo '<br><center><a href="admin.php?x=home"><font color=';
	if($_GET['x'] == "home" || !isset($_GET['x']))
	{
		echo "orange";
	}
	else
	{
		echo "white";
	}
	echo '>Home</font></a></center><br>';

	echo '<br><center><a href="admin.php?x=packages"><font color=';
	if($_GET['x'] == "packages")
	{
		echo "orange";
	}
	else
	{
		echo "white";
	}
	echo '>Packages</font></a></center><br>';
	
	echo '<br><center><a href="admin.php?x=paypal"><font color=';
	if($_GET['x'] == "paypal")
	{
		echo "orange";
	}
	else
	{
		echo "white";
	}
	echo '>Paypal</font></a></center><br>';
	
	echo '<br><center><a href="admin.php?x=accounts"><font color=';
	if($_GET['x'] == "accounts")
	{
		echo "orange";
	}
	else
	{
		echo "white";
	}
	echo '>Accounts</font></a></center><br>';

	echo '<br><center><a href="admin.php?x=logout"><font color=white>Log Out</font></a></center><br></div>';
}
//Main Admin Homepage
function home()
{
	
	menu();
	echo '<div id="adminright"><center><h1>Administrator Control Panel</h1><br><br>';
	echo "Welcome to your control panel, <strong>{$_COOKIE['user_username']}</strong>. Click a link on the left side to continue.<br><br>";
}
 
 
//A Blank second page
function packages()
{
	menu();
	// Yay I get to set up sql shit here.
	echo '<div id="adminright"><center><h1>Packages</h1><br><br>';
	if(isset($_POST['id']))
	{
		packages_go();
	}
	echo 'This is where the donation packages can be defined for the server.<br>
	Make sure you do not include any special characters in Price.<br><br>';
	require "../core/settings.php";
	$db = mysqli_connect($setting['dbip'], $setting['dbusername'], $setting['dbpassword'],$setting['dbname']);
	$query = "SELECT * FROM packages";
	$result = mysqli_query($db, $query);
	echo "<hr><h3>Packages</h3><table>
		<tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Description</b></td><td><b>Price</b></td><td><b>Command</b></td><td><b>Rank<b></td><td></td></tr>";
	$packages = mysqli_fetch_all($result);
	foreach($result as $key => $val)
	{
		echo "
			<form action=\"admin.php?x=packages\" method=\"post\">
			<tr><td style=\"vertical-align:top\"><input type=\"hidden\" name=\"id\" value=\"{$val[id]}\">{$val[id]}</td>
			<td style=\"vertical-align:top\"><input type=\"text\" name=\"name\" value=\"{$val[name]}\"></td>
			<td style=\"vertical-align:top\"><textarea rows=\"4\" cols=\"25\" name=\"description\">{$val[description]}</textarea></td>
			<td style=\"vertical-align:top\"><input type=\"text\" name=\"price\" value=\"{$val[price]}\"></td>
			<td style=\"vertical-align:top\"><input type=\"text\" name=\"command\" value=\"{$val[command]}\"></td>
			<td style=\"vertical-align:top\"><input type=\"text\" name=\"rank\" value=\"{$val[rank]}\"></td>
			<td style=\"vertical-align:top\"><input type=\"submit\" name=\"submit\" value=\"Submit\"><input type=\"submit\" name=\"delete\" value=\"Delete\"></td>
			</tr></form>";
		
	}
	echo "
		<form action=\"admin.php?x=packages\" method=\"post\">
		<tr><td style=\"vertical-align:top\"><input type=\"hidden\" name=\"id\" value=\"NEW\">NEW</td>
		<td style=\"vertical-align:top\"><input type=\"text\" name=\"name\"></td>
		<td style=\"vertical-align:top\"><textarea rows=\"4\" cols=\"25\" name=\"description\"></textarea></td>
		<td style=\"vertical-align:top\"><input type=\"text\" name=\"price\"></td>
		<td style=\"vertical-align:top\"><input type=\"text\" name=\"command\"></td>
		<td style=\"vertical-align:top\"><input type=\"text\" name=\"rank\"></td>
		<td style=\"vertical-align:top\"><input type=\"submit\" name=\"submit\" value=\"Submit\"></td>
		</tr></form>";
		echo "</table>";
}
function packages_go()
{
	echo "<h3><font color=red><strong>"; // Get the admin's attention.
	require "../core/settings.php";
	$db = mysqli_connect($setting['dbip'], $setting['dbusername'], $setting['dbpassword'],$setting['dbname']);
	// User is trying to edit his own account and isn't root...
	// OR the user is a root account, and could be doing anything.
	if($_POST['id'] != "NEW" && $_POST['delete'] != "Delete")
	{
		$query = "UPDATE packages SET `name` = '{$_POST['name']}', `description` = '{$_POST['description']}', `price` = '{$_POST['price']}', `command` = '{$_POST['command']}', `rank` = '{$_POST['rank']}' WHERE `id` = {$_POST['id']};";
		$result = mysqli_query($db,$query);
		echo "Success! Package \"{$_POST['name']}\" updated!";
	}
	elseif($_POST['delete'] == "Delete")
	{
		$query = "DELETE FROM packages WHERE `id` = {$_POST['id']}";
		$result = mysqli_query($db,$query);
		echo "Package \"{$_POST['name']}\" has been deleted!";
	}
	elseif($_POST['id'] == "NEW")
	{
		$query = "INSERT INTO packages (`name`, `description`, `price`, `command`,`rank`) VALUES ('{$_POST['name']}', '{$_POST['description']}', '{$_POST['price']}', '{$_POST['command']}', '{$_POST['rank']}');";
		$result = mysqli_query($db,$query);
		echo "New package \"{$_POST['name']}\" was successfully created!";
	}
	echo "</strong></font></h3><br><br>";
}
function accounts()
{
	menu();

	echo '<div id="adminright"><center><h1>Accounts</h1><br><br>';
	if(isset($_POST['id']))
	{
		accounts_go();
	}
	//echo '<br>Todo: accounting<br><br>';
	require "../core/settings.php";
	$db = mysqli_connect($setting['dbip'], $setting['dbusername'], $setting['dbpassword'],$setting['dbname']);
	$query = "SELECT * FROM admin WHERE id='{$_COOKIE['user_id']}' AND username='{$_COOKIE['user_username']}'";
	$oresult=mysqli_query($db, $query);
	//$result=mysqli_fetch_array($result);
	$result=mysqli_fetch_row($oresult);
	
	echo "<table><tr><td><b>Username</b></td><td><b>Password</b></td><td><b>Password (again)</b></td><td><b>Email</b></td><td></td></tr>
	<form action=\"admin.php?x=accounts\" method=\"post\">
	<input type=\"text\" name=\"fuckingautofill\" style=\"display:none\"> <!-- This serves no purpose other than making chome shag itself -->
	<input type=\"password\" name=\"fuckingautofill2\" style=\"display:none\">
	<tr><input type=\"hidden\" name=\"id\" value=\"{$result[0]}\">
	<td><input type=\"hidden\" name=\"username\" value=\"{$result[1]}\">{$result[1]}</td>
	<td><input type=\"password\" name=\"password1\"></td>
	<td><input type=\"password\" name=\"password2\"></td>
	<td><input type=\"text\" name=\"email\" value=\"{$result[3]}\"></td>
	<td><input type=\"submit\" name=\"submit\" value=\"Submit\"></td>
	</tr></form></table>";
	
	
	if($result[4] == "1")
	{
	$db = mysqli_connect($setting['dbip'], $setting['dbusername'], $setting['dbpassword'],$setting['dbname']);
	$query2 = "SELECT * FROM admin WHERE id!='{$_COOKIE['user_id']}'";
	$end = mysqli_query($db, $query2);
	$end1 = mysqli_fetch_all($end);
	echo "<hr><h3>Other Acounts</h3><table>
	<tr><td><b>ID</b></td><td><b>Username</b></td><td><b>Password</b></td><td><b>Password (again)</b></td><td><b>Email</b></td><td></td></tr>";
	foreach($end1 as $key => $val)
		{
			echo "
				<form action=\"admin.php?x=accounts\" method=\"post\">
				<input type=\"text\" name=\"fuckingautofill\" style=\"display:none\"> <!-- This serves no purpose other than making chome shag itself -->
				<input type=\"password\" name=\"fuckingautofill2\" style=\"display:none\">
				<tr><td><input type=\"hidden\" name=\"id\" value=\"{$val[0]}\">{$val[0]}</td>
				<td><input type=\"hidden\" name=\"username\" value=\"{$val[1]}\">{$val[1]}</td>
				<td><input type=\"password\" name=\"password1\"></td>
				<td><input type=\"password\" name=\"password2\"></td>
				<td><input type=\"text\" name=\"email\" value=\"{$val[3]}\"></td>
				<td><input type=\"submit\" name=\"submit\" value=\"Submit\"><input type=\"submit\" name=\"delete\" value=\"Delete\"></td>
				</tr></form>";
		}
		echo "
			<form action=\"admin.php?x=accounts\" method=\"post\">
			<input type=\"text\" name=\"fuckingautofill\" style=\"display:none\"> <!-- This serves no purpose other than making chome shag itself -->
			<input type=\"password\" name=\"fuckingautofill2\" style=\"display:none\">
			<tr><td><input type=\"hidden\" name=\"id\" value=\"NEW\">New Account</td>
			<td><input type=\"text\" name=\"username\"></td>
			<td><input type=\"password\" name=\"password1\"></td>
			<td><input type=\"password\" name=\"password2\"></td>
			<td><input type=\"text\" name=\"email\"></td>
			<td><input type=\"submit\" name=\"submit\" value=\"Submit\"></td>
			</tr></form>";
	}	
	echo '</table>';
}
function accounts_go()
{
	// This isn't a GUI function, its a function function, so we don't need to add any labels or anything to this.
	// We call upon this function in accounts() if $_POST['id'] is set.
	echo "<h3><font color=red><strong>"; // Get the admin's attention.
	require "../core/settings.php";
	$db = mysqli_connect($setting['dbip'], $setting['dbusername'], $setting['dbpassword'],$setting['dbname']);
	// Verify that this user is editing a valid account
	$query = "SELECT * FROM admin WHERE id='{$_COOKIE['user_id']}' AND username='{$_COOKIE['user_username']}'";
	$result = mysqli_query($db,$query);
	$user = mysqli_fetch_row($result);
	if(($_POST['id'] == $user[0] && $user[4] == "0") || $user[4] == "1") 
	{
		// User is trying to edit his own account and isn't root...
		// OR the user is a root account, and could be doing anything.
		if($_POST['id'] != "NEW" && $_POST['delete'] != "Delete")
		{
			if($_POST['password1'] == $_POST['password2'] && !($_POST['password1'] == "" || $_POST['password1'] == null))
			{
				$password = md5($_POST['password1']);
				$query = "UPDATE admin SET `password` = '{$password}', `email` = '{$_POST['email']}' WHERE `id` = {$_POST['id']};";
				$result = mysqli_query($db,$query);
				echo "Success! Account {$_POST['username']} updated!";
			}
			elseif($_POST['password1'] != $_POST['password2'])
			{
				echo "Error: {$_POST['username']}'s passwords do not match!";
			}
			elseif($_POST['password1'] == "" || $_POST['password1'] == null)
			{
				echo "Error: {$_POST['username']}'s passwords were blank!";
			}
		}
		elseif($_POST['delete'] == "Delete")
		{
			$query = "DELETE FROM admin WHERE `id` = {$_POST['id']}";
			$result = mysqli_query($db,$query);
			echo "Account {$_POST['username']} has been deleted!";
		}
		elseif($_POST['id'] == "NEW")
		{
			if($_POST['password1'] == $_POST['password2'] && !($_POST['password1'] == "" || $_POST['password1'] == null))
			{
				$username = addslashes($_POST['username']); //Protects against some forms of SQL injection.
				$email = addslashes($_POST['email']);
				$password = md5($_POST['password1']);
				$query = "INSERT INTO admin (`username`, `password`, `email`, `root`) VALUES ('{$username}', '{$password}', '{$email}', '0');";
				$result = mysqli_query($db,$query);
				echo "New account {$username} was successfully created!";
			}
			elseif($_POST['password1'] != $_POST['password2'])
			{
				echo "Error: {$_POST['username']}'s passwords do not match!";
			}
			elseif($_POST['password1'] == "" || $_POST['password1'] == null)
			{
				echo "Error: {$_POST['username']}'s passwords were blank!";
			}
		}
	}
	else
	{
		// User is probably non-root and is trying to edit a profile he shouldnt be.
		echo "Error: Target account is not your own!"; // Lower admins don't need to know about root.
	}
	
	echo "</strong></font></h3><br><br>";
	
}
function paypal()
{
menu();
echo '<div id="adminright"><center><h1>Paypal</h1><br><br>';
}
echo '</center></div></div>';
?>
<div id="adminright"><center><br><br><br><br>Return to main <a href="admin.php"><font color="red">Control Panel</font></a>, or you can <a href="logout.php"><font color="red">Log Out</font></a></center></div>
</body>