<?php  
/////////////////////////////////////////////////////////////////////////
/*  Higher Dimensions Gaming Automated Donation System for Garry's Mod 13.
    Copyright (C) 2014 Nathan Davies-Lee

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	
	Project Version: Version 1.0
	File Version: 2014.09.15
*/
//////////////////////////////////////////////////////////////////////////////////////////////////
define('IS_INTERNAL',true);
require "libraries/paypal.class.php";
require "libraries/rcon_code.php";
require "./config.php";
require "./settings.php";
require "libraries/forum.php";
require "libraries/logging.php";
//require "./mailhandler.php"; Fuck it. Not making one.
	
$p = new paypal_class;
if($config['payment']['sandbox'])
{
	$p->paypal_url = $config['payment']['sandbox_api'];
}
else
{
	$p->paypal_url = $config['payment']['api'];
}
//Database stuff
$db = mysqli_connect($setting['dbip'], $setting['dbusername'], $setting['dbpassword'],$setting['dbname']);
$current = "Connected to database at {$setting['dbip']} using password ";
if($setting['dbpassword'])
{
	$current .= "YES";
}
else
{
	$current .= "NO";
}
WriteLog($current);
// Check connection
if (mysqli_connect_errno($db))
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	WriteLog("Failed to connect to database: ".mysqli_connect_error());
} 
$table = mysqli_real_escape_string($db, "donations");
$result = mysqli_query($db,"SHOW TABLES LIKE '{$table}'");
if (!$result)
{
	WriteLog('Error: '.mysqli_error($db).'. Please correct or disable your database configuration.');
	//Email($p->ipn_data['payer_email'],'Donation Error','A fatal error occured and your donation could not be processed','templates/success.php');
	die("A fatal error has occured. Please refer to /logs/log_".date($config['logs']['date']).".txt"." for more information.");
}
$tableExists = mysqli_num_rows($result) > 0;
	
if($tableExists)
{
	//connect to the table
	WriteLog("Table exists, Connecting to table.");
	mysqli_select_db($db, $table);
}
else
{
	//Create table
	WriteLog("Table does not exist.");
	die("Logs cannot be recorded, table doesn't exist!");
}	

	
if ($p->validate_ipn())
{
	WriteLog("IPN Validated.");
	$fee = $p->ipn_data['mc_gross'];
	$email = $p->ipn_data['payer_email']; 
	$name = $p->ipn_data['option_selection1'];
	$steamid = $p->ipn_data['option_selection2'];
	$remail = $p->ipn_data['option_selection3'];
	if(!$remail or $remail == "")
	{
		$remail = $email;
	}
	$foundpackage;
	$nicetry;
	$query = "SELECT * FROM packages";
	$action = mysqli_query($db,$query);
	$result = mysqli_fetch_all($action);
	global $finalcommand;
	foreach($result as $package => $val)
	{
		if($val['price'] == $fee)
		{
			$foundpackage = $val; //TODO: Fix this. Its wrong , I know it.
			$finalcommand = $val["command"].' '.$steamid.' '.$val["rank"];
			$rank = $val["rank"]; // I'll fucking kick myself for this.
		}
	}
	if(!$foundpackage)
	{
		$nicetry = true;
		WriteLog("WARNING!!! A donation that did not match known packages was detected! Promotion will not occur!");
	}
	if(is_array($foundpackage) && !$nicetry)
	{
		WriteLog("Donation incoming:\nEmail: ".$email.'\nName: '.$name.'\nAmmount: '.$fee .'\nSteam ID: '.$steamid.'\nRank '.$config["packages"]["Package"][$foundpackage]["Rank"]);
	}
	elseif(!is_array($foundpackage) && !$nicetry)
	{
		WriteLog("Donation incoming:\nEmail: ".$email.'\nName: '.$name.'\nAmmount: '.$fee .'\nSteam ID: '.$steamid.'\nRank '.$config["packages"]["No Package"]["Rank"]);
	}
		
		//Add user donation to database.
	$sql;
	if(is_array($foundpackage) && !$nicetry)
	{
		$sql = 	'INSERT INTO donations VALUES (NULL , "'.mysqli_real_escape_string($db, $email).'", "'.mysqli_real_escape_string($db, $steamid).'", "'.mysqli_real_escape_string($db, $name).'", "'.mysqli_real_escape_string($db, $config["packages"]["Package"][$foundpackage]["Rank"]).'" , "'.mysqli_real_escape_string($db, $fee).'")';
	}
	elseif($nicetry)
	{
		$sql = 	'INSERT INTO donations VALUES (NULL , "'.mysqli_real_escape_string($db, $email).'", "'.mysqli_real_escape_string($db, $steamid).'", "'.mysqli_real_escape_string($db, $name).'", "'.mysqli_real_escape_string($db, "INVALID DONATION").'" , "'.mysqli_real_escape_string($db, $fee).'")';
	}
	mysqli_query($db,$sql);	
	WriteLog("Added to database.");
	$query = 'SELECT * FROM servers';
	$action = mysqli_query($db,$query);
	$servers = mysqli_fetch_all($action);
	foreach ($servers as &$SERVER)
	{
		if($SERVER["active"])
		{
			$srcds_rcon = new srcds_rcon();
			if(is_array($foundpackage) && !$nicetry)
				$OUTPUT = $srcds_rcon->rcon_command($SERVER["ip"], $SERVER["port"], $SERVER["rcon"], $finalcommand);
			{
				WriteLog('ip: '.$SERVER["ip"].' Port: '.$SERVER["port"].' Password: HIDDEN Command: '.$command);
				if( $OUTPUT == 'Unable to connect!' || $OUTPUT == '' )
				{ 
					if($OUTPUT == "")
					{
						$OUTPUT = "No response from server (invalid command?).";
					}
					WriteLog($OUTPUT);
					WriteLog("Unable to connect to Rcon, please check your configuration. (".$SERVER["ip"].")");
					$SERVER["status"] = $SERVER["name"]." (".$SERVER["ip"].":".$SERVER["port"].") - Failed: ".$OUTPUT;
					if(!$failuretopromote)
					{
						$failuretopromote = true;
					}
				}
				else
				{
					$SERVER["Status"] = $SERVER["Server Name"]." (".$SERVER["ip"].":".$SERVER["port"].") - Success";
				}
			}
		}
	}
	if($config["forum"]["Is Used"] && !$nicetry)
	{
		$fdb = new PDO('msyql:host=$config["forum"]["host"];dbname=$config["forum"]["dbname"];charset=utf8', '$config["forum"]["username"]', '$config["forum"]["password"]');
		$query = $ForumQuery;
		$query_params = array(
			':steamid' => $steamid
		);
		$promote = $ForumUpdate;
		$promote_params = array(
			':steamid' => $steamid,
			':forumrank' => $forumrank
		);
		
		try
		{
			$stmt = $fdb->prepare($query);
			$result = $stmt->execute($query_params);
		}
		catch(PDOException $ex)
		{
			$forumstatus = "Couldn't find any forum username with ".$steamid." as their SteamID.";
			$forumfailed = true;
			if(!$failuretopromote)
			{
				$failuretopromote = true;
			}
		}
		if(!$forumfailed)
		{
			$rowq = $stmt->fetch();
			if($rowq)
			{
				$fusername = $rowq[$usernamefield];
			}
			try
			{
				$stmt2 = $fdb->prepare($promote);
				$result2 = $stmt2->execute($promote_params);
			}
			catch(PDOException $ex)
			{
				$forumstatus = "We know your username is ".$fusername." but for some reason we couldn't promote you to your rank.<br>Our coder has been notified via email and your rank will be applied within 24 hours.";
				$forumfailed = true;
				$forumfailed2 = true;
				if(!$failuretopromote)
				{
					$failuretopromote = true;
				}
			}
		}
		
		if($fusername or !$fusername == "" or !$fusername == null)
		{
			$forumstatus = "Success! User ".$fusername." was promoted to ".$rank." on the forums!";
		}
		else
		{
			$forumstatus = "Failure! No user with the SteamID ".$steamid." could be found!";
			$forumfailed = true;
		}
	}
	elseif($config["forum"]["isused"] && $nicetry)
	{
		$forumfailed = true;
		$forumfailed2 = true;
		$forumstatus = "Invalid donation detected! No queries were sent to the forum.";
	}
	
	
	if($failuretopromote)
	{
		$subject = $communityname.' - Donation Complete - Partial failure: '.$rank.'';  
		$messagesummary = "One or more of our servers have reported a failure to promote you to your rank.<br>Please report this to us immediately!";
	}
	else
	{
		$subject = $communityname.' - Donation Complete - Full Promotion: '.$rank.'';
		$messagesummary = "All of our servers have reported a successful promotion to your rank.";
	}
	if($forumfailed)
	{
		$forumsummary = "Our forums have failed to promote you to your proper rank.<br>This COULD be because you didn't add your SteamID to your profile.<br>Either way, we have been notified.";
	}
	else
	{
		$forumsummary = "";
	}
	if($config["teamspeak"]["isused"] && !$nicetry)
	{
		try
		{
			require_once("libraries/TeamSpeak3/TeamSpeak3.php");
			$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$config["teamspeak"]["username"].":".$config["teamspeak"]["password"]."@".$config["teamspeak"]["ip"].":".$config["teamspeak"]["qport"]."/?server_port=".$config["teamspeak"]["port"]);
			if(is_array($foundpackage))
			{
				$arr_ServerGroup = $ts3_VirtualServer->serverGroupGetByName($config["teamspeak"]["rank"]);
			}
			//elseif($foundpackage == "None")
			//{
				//$arr_ServerGroup = $ts3_VirtualServer->serverGroupGetByName($config["packages"]["No Package"]["TS3Rank"]);
			//}
			$ts3_PrivilegeKey = $arr_ServerGroup->privilegeKeyCreate($name." (".$steamid.")");
			if(!$ts3_PrivilegeKey)
			{
				$ts3failed = true;
				$ts3summary = "We were unable to generate a Privilege Key for the TeamSpeak 3 server.";
			}
			else
			{
				$ts3failed = false;
				$ts3summary = "We have successfully generated a TeamSpeak 3 Privilege Key for you to use on the server.<br>This key is <strong><u><i>".$ts3_PrivilegeKey."</i></u></strong><br>Please enter this key when connected to our TeamSpeak 3 server by clicking on Permissions, then on Use Privilege Key.";
			}
		}
		catch(Exception $e)
		{
			$ts3failed = true;
			$ts3summary = "TeamSpeak 3: Error: ".$e->getMessage()."<br>We will email you a Privilege Key at ".$remail." when we have made one.";
		}
	}		

	$to      = $remail;
	$headers = 'From: '.$config["emails"]["Sender"].' <'.$config["emails"]["Reply To"].'> '. "\r\n" .
		'Reply-To: '.$config["emails"]["Reply To"].' ' . "\r\n" .
		'X-Mailer: PHP/' . phpversion() . " \r\n" .
		'MIME-Version: 1.0' . "\r\n" .
		'Content-Type: text/html; charset=iso-8859-1' . "\r\n";
		
	include("templates/success.php");
	
	mail($to, $config["emails"]["Community Name"]." - Donation Notification", $successmessage, $headers);
	$query = 'SELECT email FROM admin';
	$action = mysqli_query($db,$query);
	$emails = mysqli_fetch_all($action);
	foreach($emails as $logemailn)
	{
		mail($logemailn[0], $config["emails"]["Community Name"]." - Donation Notification", "<center>--Carbon Copy of Email sent to Donator--</center>\n\n".$message, $headers);
	}
	
}
else 
{
	$to      = $remail;	
	$subject = $config["emails"]["Community Name"].' - Donation Failed:';  
	$headers = 'From: '.$config["emails"]["Sender"].' <'.$config["emails"]["Reply To"].'> '. "\r\n" .
		'Reply-To: '.$config["emails"]["Reply To"].' ' . "\r\n" .
		'X-Mailer: PHP/' . phpversion() . " \r\n" .
		'MIME-Version: 1.0' . "\r\n" .
		'Content-Type: text/html; charset=iso-8859-1' . "\r\n"; 
	if(!$name)
	{
		$name = "Donator";
	}
	include("templates/failure.php");
	mail($to, $subject, $failedmessage, $headers);
	$query = 'SELECT email FROM admin';
	$action = mysqli_query($db,$query);
	$emails = mysqli_fetch_all($action);
	foreach($emails as $logemailn)
	{
		mail($logemailn[0], $subject, "<center>--Carbon Copy of Email sent to Donator--</center>\n\n".$failedmessage, $headers);
	}
	
}
mysqli_close($db);  
?>  