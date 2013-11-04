<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

require_once __DIR__ . '/config.php';

session_start();

$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : 'user';

$method = isset($_GET['method']) ? strtoupper($_GET['method']) : 'GET';

switch($method) {
	case 'GET': $method = OAUTH_HTTP_METHOD_GET; break;
	case 'POST': $method = OAUTH_HTTP_METHOD_POST; break;
	case 'PUT': $method = OAUTH_HTTP_METHOD_PUT; break;
	case 'DELETE': $method = OAUTH_HTTP_METHOD_DELETE; break;
	case 'HEAD': $method = OAUTH_HTTP_METHOD_HEAD; break;
	default: die("Invalid Method: $method");
}

$body = (isset($_GET['body']) && !empty($_GET['body'])) ? $_GET['body'] : null;

$additional_errors = null;

$OAuth	= new OAuth($consumerKey, $consumerSecret);
$OAuth->setToken($token, $tokenSecret);
$OAuth->enableDebug();
if ($self_signed) $OAuth->disableSSLChecks();

try {
	$result = $OAuth->fetch($apiURL . $endpoint, $body, $method, array(
		'X-Api-Version' => '1',
		'Accept' => 'application/json',
		//'Accept' => 'application/vnd.copy-v1+json',
	));
} catch (OAuthException $E) {
	$additional_errors = $E->getMessage();
}

$response_body = $OAuth->getLastResponse();
$response = $OAuth->getLastResponseInfo();
$debug = $OAuth->debugInfo;

if (strpos($response['content_type'], 'image/') === 0) {
	header("Content-Type: {$response['content_type']}");
	echo $response_body;
	exit();
}

?>
<style>
	body h1 {
		color: red;
	}
	body.S200 h1,
	body.S201 h1 {
		color: green;
	}
	body pre.prettyprint { /* Going for the complete HTTP packet look */
        padding: 0px;
        border: 0px;
    }
</style>
<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
<?php
echo "<body class='S{$response['http_code']}'>\n";
echo "<h1>$method {$response['url']}</h1>\n";
echo "<p><a href='index.php'>Return to Main Screen</a></p>\n";

// REQUEST

echo "<h2>Request</h2>\n";
echo "<pre>{$debug['headers_sent']}</pre>\n";
if ($body) {
	echo "<pre>" . json_prettify($body) . "</pre>\n";
}
echo "<hr />\n";

// RESPONSE

echo "<h2>Response</h2>\n";
echo "<pre>{$response['headers_recv']}</pre>\n";
if (!$response_body) {
	echo "<div><em>&lt;empty body&gt;</em></div>\n";
} else if ($body_decoded = @json_decode($response_body)) {
	echo "<pre class='prettyprint'><code class='language-javascript'>" . json_prettify($response_body) . "</code></pre>\n";
} else {
	echo "<pre>" . htmlentities($response_body) . "</pre>\n";
}

// ADDITIONAL ERRORS

if ($additional_errors) {
	echo "<hr />\n";

	echo "<h2>Additional OAuth Client Errors</h2>\n";
	echo "<pre>$additional_errors</pre>\n";
}

echo "</body>\n";
