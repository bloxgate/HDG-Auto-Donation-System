<?php
/*
|----------------------
| HDG Auto Donation System
|----------------------
Thats right, we are getting an install script. How fancy.
*/
// Credit to MyBB for teaching me with their install script.
// Step 0: Handy defines.

error_reporting(E_ALL);
session_start();
//$_SESSION["progress"] = "";
define('ROOT', dirname(__FILE__)."/");
define('IS_INTERNAL', 1);
define('VERSION', 1.1);
// Step 1: Have we already installed it?
$installed = false;
if(!file_exists(ROOT."/core/settings.php"))
{
	//Create it!
	$settings = "<?php
	if(!defined('IS_INTERNAL')) die('Direct initialization of this file is not allowed.');
	\$setting = array(
		'dbip' => \"localhost\",
		'dbusername' => \"root\",
		'dbpassword' => \"\",
		'dbname' => \"donate\",
		'encryptionkey' => \"Ch@ngeMe95\"
	)
	?>";
	$file = fopen(ROOT."/core/settings.php", "w");
	fwrite($file, $settings);
	fclose($file);
}
if(isset($_SESSION["progress"]) && $_SESSION["progress"] == "done")
{
	$_SESSION["progress"] = "";
	echo "Deleting install script for security purposes.";
	if (unlink(__FILE__)) {
		header("location:./admin/");
	}
	else
	{
		echo "Cannot delete install script! Remember to delete it or else someone could hijack your donation system!";
		echo "<br><a href=\"./admin/\">Goto Admin CP</a>";
	}
}
if(isset($_SESSION["progress"]) && $_SESSION["progress"] == "3")
{
	echo "Adding the admin account now.";
	require ROOT."/core/settings.php";
	mysql_connect($setting['dbip'],$setting['dbusername'],$setting['dbpassword']) or die(mysql_error()."<br><a href=\"./install.php\">Click here to start again</a>");
	mysql_select_db($setting['dbname']) or die(mysql_error()."<br><a href=\"./install.php\">Click here to start again</a>");
	
	$myUsername = addslashes( $_POST['username'] ); //prevents types of SQL injection
	$myPassword = $_POST['password'];
	$myEmail = $_POST['email'];
	$newpass = md5($myPassword); //This will make your password encrypted into md5, a high security hash
	
	$sql = mysql_query( "INSERT INTO admin (`id`, `username`, `password`, `email`) VALUES ('', '$myUsername','$newpass', '$myEmail')" )
        or die( mysql_error() );
	$_SESSION["progress"] = "done";
	header("location:".__FILE__);
}
if(isset($_SESSION["progress"]) && $_SESSION["progress"] == "2")
{
	echo "Please insert the default admin account.<br><br>";
	echo "<form action=\"install.php\" method=\"post\">";
	echo '<table><tr><td>';
	echo "<b>Username:</b></td><td><input type='text' style='background-color:#999999; font-weight:bold;' name='username' maxlength='15' value=''></td></tr>";
	echo "<tr><td><b>Password:</b></td><td><input type='password' style='background-color:#999999; font-weight:bold;' name='password' maxlength='15' value=''></td></tr>";
	echo "<tr><td><b>Email Address:</b></td><td><input type='text' style='background-color:#999999; font-weight:bold;' name='email' maxlength='100' value=''></td></tr></table>";
	echo "<input type='submit' name='submit' value='Register Account'></form>";
	$_SESSION["progress"] = "3";
}
if(isset($_SESSION["progress"]) && $_SESSION["progress"] == "1")
{
	echo "Storing data<br>";
	$settings = "<?php
	if(!defined('IS_INTERNAL')) die('Direct initialization of this file is not allowed.');
	\$setting = array(
		'dbip' => \"{$_POST['ip']}\",
		'dbusername' => \"{$_POST['user']}\",
		'dbpassword' => \"{$_POST['pass']}\",
		'dbname' => \"{$_POST['db']}\",
		'encryptionkey' => \"{$_POST['encrypt']}\"
	)
?>";
	$file = fopen(ROOT."/core/settings.php", "w");
	fwrite($file, $settings);
	fclose($file);
	echo "Creating tables now!<br>";
	mysql_connect($_POST["ip"],$_POST["user"],$_POST["pass"]) or die(mysql_error()."<br><a href=\"./install.php\">Click here to start again</a>");
	mysql_select_db($_POST["db"]) or die(mysql_error()."<br><a href=\"./install.php\">Click here to start again</a>");
	$packages = "CREATE TABLE IF NOT EXISTS packages
		(
		id INT NOT NULL AUTO_INCREMENT,
		PRIMARY KEY(id),
		name VARCHAR(250),
		description VARCHAR(250),
		price VARCHAR(250),
		command VARCHAR(250),
		rank VARCHAR(250)
		)";
	echo "Packages... ";
	mysql_query($packages) or die( mysql_error() );
	echo "Success!<br>";
	$admin = "CREATE TABLE IF NOT EXISTS admin
		(
		id INT NOT NULL AUTO_INCREMENT,
		PRIMARY KEY(id),
		username VARCHAR(250),
		password VARCHAR(250),
		email VARCHAR(250)
		)";
	echo "Admin CP... ";
	mysql_query($admin) or die( mysql_error() );
	echo "Success!<br>";
	$server = "CREATE TABLE IF NOT EXISTS servers
		(
		id INT NOT NULL AUTO_INCREMENT,
		PRIMARY KEY(id),
		active BOOLEAN,
		ip VARCHAR(250),
		port VARCHAR(250),
		rcon VARCHAR(250),
		name VARCHAR(250),
		ban VARCHAR(250)
		)";
	echo "Servers... ";
	mysql_query($server) or die( mysql_error() );
	echo "Success!<br>";
	$emails = "CREATE TABLE IF NOT EXISTS email
		(
		id INT NOT NULL AUTO_INCREMENT,
		PRIMARY KEY(id),
		email VARCHAR(250)
		)";
	echo "Emails... ";
	mysql_query($emails) or die( mysql_error() );
	echo "Success!<br>";
	$_SESSION["progress"] = "2";
	header("location:".__FILE__);
}
if(!isset($_SESSION["progress"]) || $_SESSION["progress"] == "")
{
	echo "Test time! I have no idea if this works. Database info GO<br>";
	echo "<form action=\"./install.php\" method=\"POST\">";
	//echo "<input type=\"hidden\" name=\"progress\"	value=\"1\">";
	$_SESSION["progress"] = "1";
	echo '<table><tr><td>';
	echo "<b>IP:</b></td><td><input type='text' style='background-color:#999999; font-weight:bold;' name='ip' value=''></td></tr>";
	echo "<tr><td><b>Username:</b></td><td><input type='text' style='background-color:#999999; font-weight:bold;' name='user' value=''></td></tr>";
	echo "<tr><td><b>Password:</b></td><td><input type='text' style='background-color:#999999; font-weight:bold;' name='pass' value=''></td></tr>";
	echo "<tr><td><b>DB:</b></td><td><input type='text' style='background-color:#999999; font-weight:bold;' name='db' value=''></td></tr>";
	echo "<tr><td><b>Encryption Key:</b></td><td><input type='text' style='background-color:#999999; font-weight:bold;' name='encrypt' value=''></td></tr></table>";
	echo "<input type='submit' name='submit' value='GO'></form>";
}
?>