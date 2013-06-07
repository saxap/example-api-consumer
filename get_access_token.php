<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @Author	Freek Lijten
 */

require_once __DIR__ . '/config.php';

session_start();

try {
	$OAuth = new OAuth($consumerKey, $consumerSecret);
	$OAuth->setToken($_GET['oauth_token'], $_SESSION['oauth_token_secret']);
	$tokenInfo = $OAuth->getAccessToken($accessURL);

	if (!isset($tokenInfo['oauth_token']) || !isset($tokenInfo['oauth_token_secret'])) {
		echo "<h1>Error Retreiving oauth_token and oauth_token_secret</h1>\n";
		echo "<pre>RESPONSE:\n";
		echo $OAuth->getLastResponse();
		echo "\nRESPONSE INFO:\n";
		var_dump($OAuth->getLastResponseInfo());
		echo '</pre>';
		exit();
	}

	// Open and decode the file
	$data = json_decode(file_get_contents('keys.json'));

	if (!isset($data->consumer) || !isset($data->access)) {
		die("Someone has deleted the consumer field during the OAuth handshake.");
	}

	// Setting new data
	$data->access->token = $tokenInfo['oauth_token'];
	$data->access->secret = $tokenInfo['oauth_token_secret'];

	// encode
	$new_data = json_encode($data);

	// write contents to the file
	$handle = fopen('keys.json', 'w');
	fwrite($handle, $new_data);
	fclose($handle);
	
?>
<h1>Access Token/Secrets have been set.</h1>

<pre>Access Token:       <?php echo $tokenInfo['oauth_token']; ?>

Oauth Token Secret: <?php echo $tokenInfo['oauth_token_secret']; ?></pre>

<a href="index.php">Return to Main Screen</a>
<?php
} catch (Exception $E) {
	echo "<pre>OAuth ERROR MESSAGE:\n";
	echo $E->getMessage();
	echo "\nRESPONSE:\n";
	var_dump($OAuth->getLastResponse());
	echo "\nRESPONSE INFO:\n";
	var_dump($OAuth->getLastResponseInfo());
	echo '</pre>';
}

