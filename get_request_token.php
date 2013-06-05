<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @Author	Freek Lijten
 */

require_once __DIR__ . '/config.php';

$scope = json_encode(array(
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

session_start();
$tokenInfo = null;
try {
	$OAuth              = new OAuth($consumerKey, $consumerSecret);
	$OAuth->enableDebug();
	$tokenInfo          = $OAuth->getRequestToken($requestURL. '?scope=' . urlencode($scope), $callbackURL);
} catch (Exception $E) {
	echo '<pre>';
	var_dump($E->getMessage());
	var_dump($OAuth->getLastResponse());
	var_dump($OAuth->getLastResponseInfo());
	var_dump($OAuth->debugInfo); // get info about headers
	echo '</pre>';
}

if (empty($tokenInfo['oauth_token_secret']) || empty($tokenInfo['oauth_token'])) {
	echo '<pre>Token Info';
	var_dump($tokenInfo);
	echo '</pre>';
	exit;
}

$_SESSION['oauth_token_secret'] = $tokenInfo['oauth_token_secret'];

$location = $authorizeURL . '?oauth_token=' . $tokenInfo['oauth_token'];
header('Location: ' . $location);
