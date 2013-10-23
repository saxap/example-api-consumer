<?php
require_once __DIR__ . '/config.php';
session_start();
?>
<style>
	td {
		padding-bottom: 10px;
		vertical-align: top;
	}
</style>
<h1>Copy API File Uploader</h1>
<h2>Uploads a file to <code><?php echo $apiURL; ?>files/*</code></h2>

<form method="post" action="file_upload.php" enctype="multipart/form-data">
	<table>
		<tr>
			<td>File</td>
			<td><input type="file" name="file" /><br /><small>Select a file from your computer to upload to this server, which will then send that file to Copy.</small></td>
		</tr>
		<tr>
			<td>Name</td>
			<td><input type="text" name="filename" placeholder="file.txt" /><br /><small>Overrides the name of the file being uploaded. If left blank, will use the filename uploaded.</small></td>
		</tr>
		<tr>
			<td>Path</td>
			<td><input type="text" name="path" placeholder="/path/to/dir/" /><br /><small>The location to store the file at. If blank, will default to /.</small></td>
		</tr>
		<tr>
			<td>Overwrite?</td>
			<td><input type="checkbox" name="overwrite" checked="checked" /><br /><small>If checked, an existing file will be replaced as a new version, otherwise, the filename will have (1) appended.</small></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Upload File" /></td>
		</tr>
	</table>
</form>

<p><a href="https://www.copy.com/developer/documentation#api-calls/filesystem" target="_blank">Documentation</a></p>

<?php
if (!$_POST) exit;
if (!$_FILES) die("Please upload a file!");
if ($_FILES['file']['error']) die("There was a file upload error!");
?>

<hr />

<?php
$endpoint = 'files' . (!empty($_POST['path']) ? $_POST['path'] : '/');
$filename = !empty($_POST['filename']) ? $_POST['filename'] : $_FILES['file']['name'];
$method = 'POST';
$overwrite = empty($_POST['overwrite']) ? '?overwrite=false' : '';

$boundary = "FormBoundary" . rand(1000000,9999999);

$additional_errors = null;

$encoded_file = file_get_contents($_FILES['file']['tmp_name']);
//$encoded_file = base64_encode($encoded_file);										// Base64 Encode file contents
//$encoded_file = rtrim($encoded_file, '=');										// Remove trailing Base64 ='s
//$encoded_file = trim(chunk_split($encoded_file, 64, "\n"));						// Split it into 64 byte columns

$body = <<<UPLOADBODY
------$boundary
Content-Disposition: form-data; name="file"; filename="$filename"
Content-Type: application/octet-stream

RAW_FILE_CONTENTS
------$boundary--
UPLOADBODY;

$OAuth	= new OAuth($consumerKey, $consumerSecret);
$OAuth->setToken($token, $tokenSecret);
$OAuth->enableDebug();
if ($self_signed) $OAuth->disableSSLChecks();

try {
	$result = $OAuth->fetch($apiURL . $endpoint . $overwrite, str_replace('RAW_FILE_CONTENTS', $encoded_file, $body), $method, array(
		'X-Api-Version' => '1',
		'Accept' => 'application/json',
		'Content-Type' => "multipart/form-data; boundary=----$boundary"
		//'Accept' => 'application/vnd.copy-v1+json',
	));
} catch (OAuthException $E) {
	$additional_errors = $E->getMessage();
}

unset($encoded_file);

$response_body = $OAuth->getLastResponse();
$response = $OAuth->getLastResponseInfo();
$debug = $OAuth->debugInfo;

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
	echo "<pre>$body</pre>\n";
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

if ($additional_errors) {
	echo "<hr />\n";
	echo "<h2>Additional OAuth Client Errors</h2>\n";
	echo "<pre>$additional_errors</pre>\n";
}

echo "</body>\n";