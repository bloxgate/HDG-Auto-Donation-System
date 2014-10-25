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
*/
//////////////////////////////////////////////////////////////////////////////////////////////////
if(!defined('IS_INTERNAL')) die('Direct initialization of this file is not allowed.');

$config['payment'] = array(
	'sandbox' => true,
	'api' => "https://www.paypal.com/cgi-bin/webscr",
	'email' => "paypal@yourdomain.com",
	'sandbox_api' => "https://www.sandbox.paypal.com/cgi-bin/webscr",
	'sandbox_email' => "testpaypal@yourdomain.com",
	'return' => "http://www.yourdomain.com/thanks/",
	'ipn' => "http://www.yourdomain.com/ipn.php"
);
$config['monetary'] = array(
	'currency' => "AUD",
	'symbol' => "$",
	'seperator' => ",",
	'decimal' => "2",
	'symbol_placement' => "before"
);
$config['legal'] = array(
	'terms' => "http://yourdomain.com/terms/",
	'contact_name' => "John Smith",
	'contact_email' => "legal@yourdomain.com"
);
$config['generic'] = array(
	'community' => "Another Gmod Community",
	'website' => "http://www.yourdomain.com/",
	'logo' => "http://www.yourdomain.com/logo.png"
);
$config['emails'] = array(
	'reply' => "no-reply@yourdomain.com",
	'contact' => "contact@yourdomain.com",
);
$config['logs'] = array(
	'date' => "d-m-Y",
	'time' => "g:i.s A",
);
	

//////////////////////////////////
// Teamspeak Config             //
//////////////////////////////////
$config["teamspeak"]["isused"] = false;
$config["teamspeak"]["username"] = "Admin"; // Admin Username for TS3. Generally your one in TS3
$config["teamspeak"]["password"] = "password"; // This is the serverquery password generated in TS3.
$config["teamspeak"]["ip"] = "127.0.0.1"; // IP of the server
$config["teamspeak"]["port"] = "1234"; // Port of your server
$config["teamspeak"]["qport"] = "10011"; // Query port of the server.

?>
