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
require("core/config.php");
require("core/steamapiv2.class.php");
require("core/steamlogin.php");
error_reporting(~E_ALL);

function GetSteamNorm($Steam64){
	$authserver = bcsub( $Steam64, '76561197960265728' ) & 1;
	//Get the third number of the steamid
	$authid = ( bcsub( $Steam64, '76561197960265728' ) - $authserver ) / 2;
	//Concatenate the STEAM_ prefix and the first number, which is always 0, as well as colons with the other two numbers
	$steamid = "STEAM_0:$authserver:$authid";
	return $steamid;
}
global $paypalurl;
global $paypalemail;
$paypalurl;
$paypalemail;
if($config['payment']['sandbox']){
	$paypalurl = $config['payment']['sandbox_api'];
	$paypalemail = $config['payment']['sandbox_email'];
}else{
	$paypalurl = $config['payment']['api'];
	$paypalemail = $config['payment']['email'];
}
$isBot = false;
$op = strtolower($_SERVER['HTTP_X_OPERAMINI_PHONE']);
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
$ac = strtolower($_SERVER['HTTP_ACCEPT']);
$ip = $_SERVER['REMOTE_ADDR'];
        $isBot =  $ip == '66.249.65.39' 
        || strpos($ua, 'googlebot') !== false 
        || strpos($ua, 'mediapartners') !== false 
        || strpos($ua, 'yahooysmcm') !== false 
        || strpos($ua, 'baiduspider') !== false
        || strpos($ua, 'msnbot') !== false
        || strpos($ua, 'slurp') !== false
        || strpos($ua, 'ask') !== false
        || strpos($ua, 'teoma') !== false
        || strpos($ua, 'spider') !== false 
        || strpos($ua, 'heritrix') !== false 
        || strpos($ua, 'attentio') !== false 
        || strpos($ua, 'twiceler') !== false 
        || strpos($ua, 'irlbot') !== false 
        || strpos($ua, 'fast crawler') !== false                        
        || strpos($ua, 'fastmobilecrawl') !== false 
        || strpos($ua, 'jumpbot') !== false
        || strpos($ua, 'googlebot-mobile') !== false
        || strpos($ua, 'yahooseeker') !== false
        || strpos($ua, 'motionbot') !== false
        || strpos($ua, 'mediobot') !== false
        || strpos($ua, 'chtml generic') !== false
        || strpos($ua, 'nokia6230i/. fast crawler') !== false;
?>
<html>
<link href="default.css" rel="stylesheet" type="text/css" />
	<head>
		<title>Test</title>
	</head>
	<body bgcolor="tan">
		<div style="margin-top:3px;">
				<div class="donationform" style="text-align:center;">
					<form name="_xclick" action="<?php echo $paypalurl; ?>" method="post"> 
						
					
						<input name="cmd" value="_xclick" type="hidden" /> 
						<input name="business" value="<?php echo $paypalemail;?>" type="hidden" />
						<input name="item_name" value="<?php echo $config['generic']['community']; ?> - Game Server Donation" type="hidden" /> 
						<input name="no_shipping" value="1" type="hidden" />
						<input name="return" value="<?php echo $config['payment']['return']; ?>" type="hidden" />
						<input type="hidden" name="rm" value="2" /> 
						<input type="hidden" name="notify_url"value="<?php echo $config['payment']['ipn']; ?>" />
						<input name="cn" value="Comments" type="hidden" />
						<input name="co" value="This is a donation for <?php echo $config['generic']['community']; ?>, automatically processed at <?php $config['generic']['website']; ?>. This donation earns various rewards at the aforementioned site for the payer, however all rewards are non-tangible and therefore not eligible for refund according to PayPal's Terms of Service." type="hidden" />
						<input name="currency_code" value="<?php echo $config['monetary']['currency']; ?>" type="hidden" />
						<input name="tax" value="0" type="hidden" /> 
						<input name="lc" value="GB" type="hidden" />					
						<h1 class="label">Packages</h1>
						<br><br>
						
						<?php
						require "./core/settings.php";
						$db = mysqli_connect($setting['dbip'], $setting['dbusername'], $setting['dbpassword'],$setting['dbname']);
						$query = "SELECT * FROM packages";
						$action = mysqli_query($db, $query);
						$result = mysqli_fetch_all($action);
						foreach($result as $package => $val)
						{
							$i++;
							if($i == 1)
							{
								echo "<table align='center' style=\"text-align:center;\"><tr>";
								//echo "<input type=\"radio\" id=\"cost".$i."\" name=\"amount\" value=\"".$val["Price"]."\" checked>".$val["Name"]." ($".$val["Price"]." ".$val["Currency"].")<br>";
							}
							echo "<td><h2 class=\"packageh2\">{$val[1]}<div class=\"package\">{$val[2]}</div></h2><h3 class=\"packageh3\">{$config['monetary']['symbol']}{$val[3]} {$config['monetary']['currency']}<br><input type=\"radio\" id=\"cost{$val[0]}\" name=\"amount\" value=\"{$val[3]}\"></h3></td>";							
							if($i == 5)
							{
								echo "</tr>";
								echo "</table>";
								$i = 0;
								//echo "<input type=\"radio\" id=\"cost".$i."\" name=\"amount\" value=\"".$val["Price"]."\">".$val["Name"]." ($".$val["Price"]." ".$val["Currency"].")<br>";
							}
						}
						echo "<table align=\"center\"><br><br><tr><td width=\"50%\" class=\"barleft\" align=\"center\">";
						$steam_login_verify = SteamSignIn::validate();
						if(!empty($steam_login_verify))
						{
							$steam64 = $steam_login_verify;								
							$steam = new SteamAPI($steam_login_verify);								
							$steamID = GetSteamNorm($steam_login_verify); //Get normal steamID		
							$friendlyName = $steam->getFriendlyName();  //Get players ingame name.	
									
							echo "<br><a href=\"{$steam_sign_in_url}\"><img src=\"http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png\" /></a>
							<br>Successfully grabbed your details!</td><td width=\"50%\" class=\"barright\" align=\"center\">
							<input type=\"hidden\" name=\"on2\" value=\"Email Address\" maxlength=\"200\">Email Address:
							<input type=\"text\" id=\"emaildonate\" name=\"os2\" value=\"\"><br>
							<input type=\"hidden\" name=\"on0\" value=\"In-Game Name\" maxlength=\"200\">In-Game Name:
							<input type=\"text\" id=\"namedonate\"  name=\"os0\" value=\"{$friendlyName}\" readonly><br>
							<input type=\"hidden\" name=\"on1\" value=\"SteamID\" maxlength=\"200\">SteamID: 
							<input type=\"text\" id=\"siddonate\"  name=\"os1\" value=\"{$steamID}\" readonly><br>";							
						}
						else
						{
							$steam_sign_in_url = SteamSignIn::genUrl();
							echo "<br><a href=\"{$steam_sign_in_url}\"><img src=\"http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png\" /></a>
							<br>Sign in through Steam to automatically fill in your details.<br><font size=\"1\"><strong>What do you do with these details?</strong></font>	</td>
							<td width=\"50%\" class=\"barright\" align=\"center\">
							<table><tr><td><input type=\"hidden\" name=\"on2\" value=\"Email Address\" maxlength=\"200\">Email Address:<br>
							<input type=\"hidden\" name=\"on0\" value=\"In-Game Name\" maxlength=\"200\">In-Game Name:<br>
							<font color=\"#ff0000\">*</font><input type=\"hidden\" name=\"on1\" value=\"SteamID\" maxlength=\"200\"  >SteamID:</td><td>
							<input type=\"text\" id=\"emaildonate\" name=\"os2\" value=\"\"><br>
							<input type=\"text\" id=\"namedonate\"  name=\"os0\" value=\"\"><br>
							<input type=\"text\" id=\"siddonate\"  name=\"os1\" value=\"\"></td></tr></table>";
						}
						?>
						</tr></table>
						<input class="donatebutton" type="image" src="./images/paypal-donate.gif" border="0" name="submit" id="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /><br>
					</form>
				</div>
			</p>
		</div>
        <center>
            <b><a href="./admin/">Admin CP</a></b>
        </center>
	</body>
</html>