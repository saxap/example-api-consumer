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
	if ($self_signed) $OAuth->disableSSLChecks();
	$OAuth->setToken($_GET['oauth_token'], $_SESSION['oauth_token_secret']);
	$OAuth->enableDebug();
	$tokenInfo = $OAuth->getAccessToken($accessURL);

	$response_body = $OAuth->getLastResponse();
	$response = $OAuth->getLastResponseInfo();
	$debug = $OAuth->debugInfo;

	echo "<h1>Requesting an Access Token</h1>\n";

	echo "<p>Copy has redirected the user agent from the copy website to this consumer website. You can look at the URL to see which GET parameters have been added.</p>";

	echo "<h2>Request</h2>\n";
	echo "<pre>{$debug['headers_sent']}</pre>\n";
	echo "<hr />\n";

	echo "<h2>Response</h2>\n";
	echo "<pre>{$response['headers_recv']}</pre>\n";
	echo "<pre>" . htmlentities($response_body) . "</pre>\n";

	if (!isset($tokenInfo['oauth_token']) || !isset($tokenInfo['oauth_token_secret'])) {
		echo "<h2>Error Retrieving oauth_token and oauth_token_secret</h2>\n";
		echo "<pre>RESPONSE:\n";
		echo $OAuth->getLastResponse();
		echo "\nRESPONSE INFO:\n";
		var_dump($OAuth->getLastResponseInfo());
		echo '</pre>';
		exit();
	}

	echo "<hr />\n";

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
<h1>Access Token & Secret saved</h1>

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

