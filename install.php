<?php
/*
|----------------------
| HDG Auto Donation System
|----------------------
Thats right, we are getting an install script. How fancy.
*/
// Credit to MyBB for teaching me with their install script.
// Note to self: Get some carpet in here, there are too many echoes
if(!file_exists("LICENSE.TXT"))
{
	die("Error: The license for this software is missing. Please, re-download the software again.");
}
$license = file_get_contents("./LICENSE.TXT");
error_reporting(E_ALL);
//$_POST["progress"] = "";
define('ROOT', dirname(__FILE__)."/");
define('IS_INTERNAL', 1);
define('VERSION', 1.2);
if(!isset($_GET['p']))
{
	$_GET['p'] = "";
}
?><head>
<link href="default.css" rel="stylesheet" type="text/css" />
</head>
<?php
if(!file_exists(ROOT."core/settings.php"))
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
////LICENSE////
if(!isset($_POST["progress"]) || $_POST["progress"] == "" || $_GET["p"] == "0")
{
	echo '<div id="fulladmin">
	<div id="adminleft">
	<br><center><a href="install.php?p=0"><font color=orange>License Agreement</font></a></center><br>
	<br><center><font color=white>Database info</font></center><br>
	<br><center><font color=white>Admin Account info</font></center><br>
	<br><center><font color=white>Final step</font></center><br></div>
	<div id="adminright"><center><h1>Welcome</h1><br><br>
	Congratulations on choosing the HDG Automated Donation System for your community.<br><br>By choosing us, you are supporting the Garry\'s Mod Community by sticking to Open Source Software, developed by the community for the community.
	<br><br>Before continuing, please take the time to read and accept the license that applies in part to this software. This license is used to protect the software from illegal profitable distribution, including the sale of this software.<br>If you ever paid for this software, demand a full refund immediately.
	<pre><div class="license_agreement">
	'.$license.'
	</div></pre>
	<br><strong>By clicking Next, you agree to the terms stated in the GNU General Public License above.</strong>
	<form action="./install.php" method="post">
	<input type="hidden" name="progress" value="1">
	<input type=\'submit\' name=\'submit\' value=\'Next\'></form>
	</center></div></div>';
}
////SETTINGS////
if(isset($_POST["progress"]) && $_POST["progress"] == "1" || $_GET["p"] == "1")
{
	echo '<div id="fulladmin">';
	echo '<div id="adminleft">';
	echo '<br><center><a href="install.php?p=0"><font color=grey><s>License Agreement</s></font></a></center><br>';
	echo '<br><center><a href="install.php?p=1"><font color=orange>Database info</font></a></center><br>';
	echo '<br><center><font color=white>Admin Account info</font></center><br>';
	echo '<br><center><font color=white>Final step</font></center><br></div>';
	echo '<div id="adminright"><center><h1>Database info</h1><br><br>';
	echo 'Please insert the database information for your donation system. This will be used by the Administrator Control Panel for setting up servers later.';
	echo '<br><br>';
	echo "<form action=\"./install.php\" method=\"POST\">";
	//echo "<input type=\"hidden\" name=\"progress\"	value=\"1\">";
	echo "<input type='hidden' name='progress' value='2'>";
	echo '<table><tr><td>';
	echo "<b>IP:</b></td><td><input type='text' style='background-color:#bbbbbb; font-weight:bold;' name='ip' value=''></td></tr>";
	echo "<tr><td><b>Username:</b></td><td><input type='text' style='background-color:#bbbbbb; font-weight:bold;' name='user' value=''></td></tr>";
	echo "<tr><td><b>Password:</b></td><td><input type='text' style='background-color:#bbbbbb; font-weight:bold;' name='pass' value=''></td></tr>";
	echo "<tr><td><b>DB:</b></td><td><input type='text' style='background-color:#bbbbbb; font-weight:bold;' name='db' value=''></td></tr>";
	echo "<tr><td><b>Encryption Key:</b></td><td><input type='text' style='background-color:#bbbbbb; font-weight:bold;' name='encrypt' value=''></td></tr></table>";
	echo "<input type='submit' name='submit' value='Next'></form> - ";
	echo '<form action="install.php" method="post"><input type="hidden" name="progress" value=""><input type="submit" name="submit" value="Start Again"></form>';
	echo '</center></div></div>';
}
if(isset($_POST["progress"]) && $_POST["progress"] == "2")
{
	echo '<div id="fulladmin">';
	echo '<div id="adminleft">';
	echo '<br><center><a href="install.php?p=0"><font color=grey><s>License Agreement</s></font></a></center><br>';
	echo '<br><center><a href="install.php?p=1"><font color=orange>Database info</font></a></center><br>';
	echo '<br><center><font color=white>Admin Account info</font></center><br>';
	echo '<br><center><font color=white>Final step</font></center><br></div>';
	echo '<div id="adminright"><center><h1>Database info</h1><br><br>';
	echo "Storing data, wait please... ";
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
	echo "Done! <br>";
	echo "Connecting to Database... ";
	$error = 0;
	try {
		$db = new PDO("mysql:host={$_POST['ip']};dbname={$_POST['db']};charset=utf8", $_POST['user'], $_POST['pass']);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo "<b>Failure: " . $e->getMessage();
		$error++;
	}
		
	
	
	
	
	//$db = mysqli_connect($_POST["ip"],$_POST["user"],$_POST["pass"],$_POST["db"]);
	if($error == 0)
	{
		echo "Packages... ";
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
		$stmt = $db->prepare($packages);
		$stmt->execute();
		echo "<b>Success!</b><br>";
		
		echo "Admin CP... ";
		$admin = "CREATE TABLE IF NOT EXISTS admin
			(
			id INT NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			username VARCHAR(250),
			password VARCHAR(250),
			email VARCHAR(250),
			root BOOLEAN
			)";
		$stmt = $db->prepare($admin);
		$stmt->execute();
		echo "<b>Success!</b><br>";
		
		echo "Servers... ";

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
		$stmt = $db->prepare($server);
		$stmt->execute();
		echo "<b>Success!</b><br>";
		
		echo "Emails... ";
		$emails = "CREATE TABLE IF NOT EXISTS email
			(
			id INT NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			email VARCHAR(250)
			)";
		$stmt = $db->prepare($emails);
		$stmt->execute();
		echo "<b>Success!</b><br>";
		
		echo "Donations... ";
		$donations = "CREATE TABLE IF NOT EXISTS donations
			(
			id INT NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(id),
			email VARCHAR(250),
			steamid VARCHAR(250),
			name VARCHAR(250),
			rank VARCHAR(250),
			amount VARCHAR(250)
			)";
		$stmt = $db->prepare($donations);
		$stmt->execute();
		echo "<b>Success!</b><br>";
		echo "<form action=\"install.php\" method=\"post\">";
		echo "<input type='hidden' name='progress' value='3'>";
		echo "<input type='submit' name='submit' value='Next'></form> - ";
	}
	else
	{
		echo "<a href='install.php?p=1'><font color=orange>Click here</font></a> to return to the database info and re-enter the information.<br>-<br>";
	}
	echo '<form action="install.php" method="post"><input type="hidden" name="progress" value="1"><input type="submit" name="submit" value="Start Again"></form>';
	echo '</center></div></div>';
}
if(isset($_POST["progress"]) && $_POST["progress"] == "3" || $_GET["p"] == "2")
{
	echo '<div id="fulladmin">';
	echo '<div id="adminleft">';
	echo '<br><center><a href="install.php?p=0"><font color=grey><s>License Agreement</s></font></a></center><br>';
	echo '<br><center><a href="install.php?p=1"><font color=grey><s>Database info</s></font></a></center><br>';
	echo '<br><center><a href="install.php?p=2"><font color=orange>Admin Account info</font></a></center><br>';
	echo '<br><center><font color=white>Final step</font></center><br></div>';
	echo '<div id="adminright"><center><h1>Admin Account info</h1><br><br>';
	echo "Please insert the default admin account. This password is NOT recoverable, and if you lose it, you may not be able to customize your server.<br><strong>DO NOT LOSE IT</strong><br><br>";
	echo "<form action=\"install.php\" method=\"post\">";
	echo '<table><tr><td>';
	echo "<b>Username:</b></td><td><input type='text' style='background-color:#bbbbbb; font-weight:bold;' name='username' maxlength='15' value=''></td></tr>";
	echo "<tr><td><b>Password:</b></td><td><input type='password' style='background-color:#bbbbbb; font-weight:bold;' name='password' maxlength='15' value=''></td></tr>";
	echo "<tr><td><b>Email Address:</b></td><td><input type='text' style='background-color:#bbbbbb; font-weight:bold;' name='email' maxlength='100' value=''></td></tr></table>";
	echo "<input type='hidden' name='progress' value='4'>";
	echo "<input type='submit' name='submit' value='Next'></form> - ";
	echo '<form action="install.php" method="post"><input type="hidden" name="progress" value="1"><input type="submit" name="submit" value="Start Again"></form>';
	echo '</center></div></div>';
}
if(isset($_POST["progress"]) && $_POST["progress"] == "4")
{
	echo '<div id="fulladmin">';
	echo '<div id="adminleft">';
	echo '<br><center><a href="install.php?p=0"><font color=grey><s>License Agreement</s></font></a></center><br>';
	echo '<br><center><a href="install.php?p=1"><font color=grey><s>Database info</s></font></a></center><br>';
	echo '<br><center><a href="install.php?p=2"><font color=orange>Admin Account info</font></a></center><br>';
	echo '<br><center><font color=white>Final step</font></center><br></div>';
	echo '<div id="adminright"><center><h1>Admin Account info</h1><br><br>';
	echo "Adding the admin account now.<br>";
	require ROOT."/core/settings.php";
	
	$error = 0;
	try {
		$db = new PDO("mysql:host={$setting['dbip']};dbname={$setting['dbname']};charset=utf8", $setting['dbusername'], $setting['dbpassword']);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		echo "<b>Failure: " . $e->getMessage();
		$error++;
	}
	if($error == 0)
	{
		$myUsername = addslashes( $_POST['username'] ); //prevents types of SQL injection
		$myPassword = $_POST['password'];
		$myEmail = $_POST['email'];
		$newpass = md5($myPassword); //This will make your password encrypted into md5, a high security hash
	
		//$sql = mysqli_query($db,"INSERT INTO admin (`id`, `username`, `password`, `email`, `root`) VALUES ('', '$myUsername','$newpass', '$myEmail', true)" );
		$stmt = $db->prepare("INSERT INTO admin (`id`, `username`, `password`, `email`, `root`) VALUES ('', '$myUsername','$newpass', '$myEmail', true)");
		$stmt->execute();
			
		echo "Admin account {$_POST["username"]} was created successfully!";
		echo "<form action=\"install.php\" method=\"post\">";
		echo "<input type='hidden' name='progress' value='done'>";
		echo "<input type='hidden' name='username' value='{$_POST['username']}'>";
		echo "<input type='submit' name='submit' value='Next'></form> - ";
	}
	else
	{
		echo "<a href='install.php?p=2'><font color=orange>Click here</font></a> to return to the admin account info page and re-enter the details.<br>-<br>";
	}
	echo '<form action="install.php" method="post"><input type="hidden" name="progress" value="1"><input type="submit" name="submit" value="Start Again"></form>';
	echo '</center></div></div>';
	
}
if(isset($_POST["progress"]) && $_POST["progress"] == "done")
{
	echo '<div id="fulladmin">';
	echo '<div id="adminleft">';
	echo '<br><center><font color=grey><s>License Agreement</s></font></center><br>';
	echo '<br><center><font color=grey><s>Database info</s></font></center><br>';
	echo '<br><center><font color=grey><s>Admin Account info</s></font></center><br>';
	echo '<br><center><font color=orange>Final step</font></center><br></div>';
	echo '<div id="adminright"><center><h1>Final Step</h1><br><br>';
	echo "Deleting install script for security purposes... ";
	if (unlink(__FILE__)) {
		echo "Done!<br> <a href='./admin/?u={$_POST['username']}'><font color=orange>Click here</font></a> to begin post-installation setup!";
		echo '</center></div></div>';
	}
	else
	{
		echo "<b>ERROR!!!</b><br>Cannot delete install script! Remember to delete it or else someone could hijack your donation system!";
		echo "<br><a href='./admin/?u={$_POST['username']}'><font color=orange>Click here</font></a> to begin post-installation setup!";
		echo '</center></div></div>';
	}
}
?>
