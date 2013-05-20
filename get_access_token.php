<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @Author	Freek Lijten
 */

require_once __DIR__ . '/config.php';

session_start();

try {
	$OAuth              = new OAuth($consumerKey, $consumerSecret);
	$OAuth->setToken($_GET['oauth_token'], $_SESSION['oauth_token_secret']);
	$tokenInfo          = $OAuth->getAccessToken($accessURL . '?oauth_verifier=' . $_GET['oauth_verifier']);

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
<h1>Access Token/Secrets set</h1>

<pre>Access Token: <?php echo $tokenInfo['oauth_token']; ?>

Oauth Token Secret: <?php echo $tokenInfo['oauth_token_secret']; ?></pre>

<a href="index.php">Return to Main Screen</a>
<?php
} catch (Exception $E) {
	echo '<pre>';
	var_dump($E->getMessage());
	var_dump($OAuth->getLastResponse());
	var_dump($OAuth->getLastResponseInfo());
	echo '</pre>';
}

