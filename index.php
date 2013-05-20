<h1>OAuth Example Consumer</h1>

<p>These scripts will allow you to test the OAuth API access within the Copy server.</p>

<ol>
	<li><a href="http://www.copy.com/developer/create" target="_blank">Create Application</a></li>
	<li><a href="set_consumer_key.php">Set Consumer Key/Secret</a>
		<ul>
			<li>This step will clear the Access Token info, since it's no longer valid</li>
			<li>Protip: Set both to blank if you want to reset all keys</li>
		</ul>
	</li>
	<li><a href="get_request_token.php">Perform OAuth Dance</a>
		<ul>
			<li>Try loggin into an existing account, creating an account, allowing, and even denying</li>
			<li>Once it is complete, it will update the local configuration</li>
			<li>You will want to <a href="https://www.copy.com/applications/my_apps" target="_blank">Revoke Apps</a> that you have allowed if you want to test the process again</li>
		</ul>
	</li>
	<li><a href="api_call.php?method=get&endpoint=user&body=">Perform API Calls</a>
		<ul>
			<li>Change 'method' in the URL to the type of action you want to perform (e.g. get, post, put, delete)</li>
			<li>Change 'endpoint' in the URL to set the name of the API call (e.g. links/LINKID)</li>
			<li>Change 'body' in the URL to be a JSON encoded body (e.g. <code>{"first_name": "Thomas"}</code>)</li>
			<li><a href="https://www.copy.com/developer/documentation#api-calls" target="_blank">Comprehensive API Calls Listing</a></li>
		</ul>
	</li>
</ol>

<hr />

<h2>Current OAuth Configuration</h2>

<p>Consumer info is set above with the <em>Set Consumer Key/Secret</em> link. Access token info is set after the OAuth dance completes.</p>

<pre>
<?php
	$config = json_decode(file_get_contents("keys.json"));

	echo "Consumer Key:        " . @$config->consumer->key . "\n";
	echo "Consumer Secret:     " . @$config->consumer->secret . "\n";
	echo "Access Token:        " . @$config->access->token . "\n";
	echo "Access Token Secret: " . @$config->access->secret . "\n";
?>
</pre>
