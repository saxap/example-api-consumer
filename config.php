<?php
/**
 * Config file used to 'setup' the basic example with your own data.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

//$server = "api.copy.local";
//$secure = false;
//$self_signed = true;
//$www = "www.copy.local";

//$server = "api.dev.copy.com";
//$secure = false;
//$self_signed = true;
//$www = "www.dev.copy.local";

//$server = "next.api.dev.copy.com";
//$secure = false;
//$self_signed = true;
//$www = "next.www.dev.copy.com";

$server = "api.copy.com";
$secure = true;
$self_signed = false;
$www = "www.copy.com";

$keys = json_decode(file_get_contents('keys.json'));

if (!$keys) die ("Error parsing keys.json");

$consumerKey 	= $keys->consumer->key;
$consumerSecret = $keys->consumer->secret;
$token			= $keys->access->token;
$tokenSecret	= $keys->access->secret;

$s = $secure ? 's' : '';

$requestURL 	= "http$s://$server/oauth/request";
$accessURL	 	= "http$s://$server/oauth/access";
$apiURL	 		= "http$s://$server/rest/";
$authorizeURL   = "http$s://$www/applications/authorize";

// This URL points to your local third party app
$callbackURL    = 'http://' . $_SERVER['SERVER_NAME'] . '/get_access_token.php';





function json_prettify($json) {
	if (strnatcmp(phpversion(),'5.4.0') >= 0) {
		return json_encode(json_decode($json), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	} else {

		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = '  ';
		$newLine     = "\n";
		$prevChar    = '';
		$outOfQuotes = true;

		for ($i=0; $i<=$strLen; $i++) {

			// Grab the next character in the string.
			$char = substr($json, $i, 1);

			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;

			// If this character is the end of an element,
			// output a new line and indent the next line.
			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}

			// Add the character to the result string.
			$result .= $char;

			// If the last character was the beginning of an element,
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}

				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}

			$prevChar = $char;
		}

		return $result;
	}
}