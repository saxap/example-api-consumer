<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @Author	Freek Lijten
 */

require_once __DIR__ . '/config.php';

if (isset($_GET['scope'])) {
	$scope = (array(
		'profile' => array(
			'read' => true,
			'write' => true,
			'email' => array(
				'read' => true,
			),
		),
		'inbox' => array(
			'read' => true,
		),
		'company' => array(
			'multi' => true,
			'filesystem' => array(
				'read' => true,
				'write' => true,
			),
			'inbox' => array(
				'read' => true,
			),
			'email' => array(
				'read' => true,
			),
		),
		'links' => array(
			'read' => true,
			'write' => true,
		),
		'filesystem' => array(
			'read' => true,
			'write' => true,
		),
	));

	if ($_GET['scope'] == 'profile-only') {
		unset($scope['inbox']);
		unset($scope['company']);
		unset($scope['links']);
		unset($scope['filesystem']);
	} else if ($_GET['scope'] == 'filesystem-read') {
		unset($scope['profile']);
		unset($scope['inbox']);
		unset($scope['company']);
		unset($scope['links']);
		$scope['filesystem']['write'] = false;
	} else if ($_GET['scope'] == 'none') {
		$scope = array();
	}

	$scope = json_encode($scope);
	$requestURL .= '?scope=' . urlencode($scope);

}
session_start();
$tokenInfo = null;
try {
	$OAuth              = new OAuth($consumerKey, $consumerSecret);
	$OAuth->enableDebug();
	if ($self_signed) $OAuth->disableSSLChecks();
	$tokenInfo          = $OAuth->getRequestToken($requestURL, $callbackURL);
} catch (Exception $E) {
	echo '<h1>There was an error getting the Request Token</h1>';
	echo '<pre>';
	echo "Message:\n";
	print_r($E->getMessage());
	echo "\n\nLast Response:\n";
	print_r($OAuth->getLastResponse());
	echo "\n\nLast Response Info:\n";
	print_r($OAuth->getLastResponseInfo());
	echo "\n\nDebug Info:\n";
	print_r($OAuth->debugInfo); // get info about headers
	echo '</pre>';
}

if (empty($tokenInfo['oauth_token_secret']) || empty($tokenInfo['oauth_token'])) {
	echo "<pre>Token Info:\n";
	print_r($tokenInfo);
	echo '</pre>';
	exit;
}

$_SESSION['oauth_token_secret'] = $tokenInfo['oauth_token_secret'];

$location = $authorizeURL . '?oauth_token=' . $tokenInfo['oauth_token'];
//header('Location: ' . $location);

$response_body = $OAuth->getLastResponse();
$response = $OAuth->getLastResponseInfo();
$debug = $OAuth->debugInfo;

echo "<h1>Request Token was a Success!</h1>\n";

echo "<h2>Request</h2>\n";
echo "<pre>{$debug['headers_sent']}</pre>\n";
echo "<hr />\n";

echo "<h2>Response</h2>\n";
echo "<pre>{$response['headers_recv']}</pre>\n";
echo "<pre>" . htmlentities($response_body) . "</pre>\n";
echo "<hr />\n";

echo "<p><a href='$location'>Manually continue to Copy Auth page</a>.<br />\n<small>(Normally, the user agent would have automatically been redirected to that page, but since this is a debugging tool, we're displaying the information and you can manually click a link.)</small></p>\n";
