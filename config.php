<?php
/**
 * Config file used to 'setup' the basic example with your own data.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

//$server = "copy.local";
//$secure = false;
//$self_signed = true;

//$server = "dev.copy.com";
//$secure = false;
//$self_signed = true;

$server = "copy.com";
$secure = true;
$self_signed = false;

$keys = json_decode(file_get_contents('keys.json'));

if (!$keys) die ("Error parsing keys.json");

$consumerKey 	= $keys->consumer->key;
$consumerSecret = $keys->consumer->secret;
$token			= $keys->access->token;
$tokenSecret	= $keys->access->secret;

$s = $secure ? 's' : '';

$requestURL 	= "http$s://api.$server/oauth/request";
$accessURL	 	= "http$s://api.$server/oauth/access";
$apiURL	 		= "http$s://api.$server/rest/";
$authorizeURL   = "http$s://www.$server/applications/authorize";

// This URL points to your local third party app
$callbackURL    = 'http://' . $_SERVER['SERVER_NAME'] . '/get_access_token.php';
