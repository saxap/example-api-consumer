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

try {
	$result = $OAuth->fetch($apiURL . $endpoint, $body, $method, array(
		'X-Api-Version' => '1',
		'Accept' => 'application/json',
		//'Accept' => 'application/vnd.copy-v1+json',
	));
} catch (OAuthException $E) {
	$additional_errors = $E->getMessage();
}

$body = $OAuth->getLastResponse();
$response = $OAuth->getLastResponseInfo();
$debug = $OAuth->debugInfo;

if (strpos($response['content_type'], 'image/') === 0) {
	header("Content-Type: {$response['content_type']}");
	echo $body;
} else {
?>
<style>
	body h1 {
		color: red;
	}
	body.S200 h1,
	body.S201 h1 {
		color: green;
	}
</style>
<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
<?php
	echo "<body class='S{$response['http_code']}'>\n";
	echo "<h1>$method {$response['url']}</h1>\n";
	echo "<p><a href='index.php'>Return to Main Screen</a></p>\n";

	if ($body_decoded = @json_decode($body)) {
		echo "<h2>Response Body <small>(Valid JSON)</small></h2>\n";
		echo "<pre class='prettyprint'><code class='language-javascript'>" . json_prettify($body) . "</code></pre>\n";
	} else {
		echo "<h2>Response Body <small>(Invalid JSON)</small></h2>\n";
		echo "<pre>" . htmlentities($body) . "</pre>";
	}

	echo "<h2>Response Headers</h2>\n<pre>{$response['headers_recv']}</pre>\n";

	echo "<hr />\n";
	echo "<h2>Request Headers</h2>\n<pre>{$debug['headers_sent']}</pre>\n";

	if ($additional_errors) {
		echo "<hr />\n";
		echo "<h2>Additional OAuth Client Errors</h2>\n";
		echo "<pre>$additional_errors</pre>\n";
	}

	echo "</body>\n";
}

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
