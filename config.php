<?php
/**
 * Config file used to 'setup' the basic example with your own data.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

$keys = json_decode(file_get_contents('keys.json'));

if (!$keys) die ("Error parsing keys.json");

$consumerKey 	= $keys->consumer->key;
$consumerSecret = $keys->consumer->secret;
$token			= $keys->access->token;
$tokenSecret	= $keys->access->secret;

$requestURL 	= 'https://api.copy.com/oauth/request';
$accessURL	 	= 'https://api.copy.com/oauth/access';
$apiURL	 		= 'https://api.copy.com/rest/';
$authorizeURL   = 'https://www.copy.com/applications/authorize';

// This URL points to your local third party app
$callbackURL    = 'http://copy-oauth.local/get_access_token.php';
