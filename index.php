<?php
	$config = json_decode(file_get_contents("keys.json"));
?>
<h1>OAuth Example Consumer</h1>

<p>This tool will allow you to test the OAuth API access within the Copy server.</p>

<ol>
	<li><a href="https://www.copy.com/developer/create" target="_blank">Create Application</a></li>
	<li><a href="set_consumer_key.php">Set Consumer Key/Secret</a>
		<ul>
			<?php if (@$config->access->token || @$config->access->secret) { ?><li><em>This step will clear the Access Token info, since it's no longer valid</em></li><?php } ?>
			<li>Protip: Set both to blank if you want to reset all keys</li>
		</ul>
	</li>
	<li><?php if (@$config->consumer->key && @$config->consumer->secret) { ?>Perform OAuth Dance | <a href="get_request_token.php">Default App Permissions</a> | <a href="get_request_token.php?scope=filesystem-read">Filesystem Read</a> | <a href="get_request_token.php?scope=profile-only">Profile</a> | <a href="get_request_token.php?scope=all">All</a> | <a href="get_request_token.php?scope=none">None</a>
		<ul>
			<?php if (@$config->access->token || @$config->access->secret) { ?><li><em>The existing Access Token &amp; Secret will be ignored &amp; overwriten</em></li><?php } ?>
			<li>Once the process completes, the Access Token &amp; Secret will be updated in this tool</li>
			<li>You will want to <a href="https://www.copy.com/profile/apps" target="_blank">Revoke Apps</a> that you have previously allowed if you want to test them again</li>
		</ul>
	<?php } else { ?>
	<strong>You need to set a Consumer Key / Secret before you can perform the OAuth dance.</strong>
	<?php } ?>
	</li>
	<li><?php if (@$config->access->token && @$config->access->secret) { ?><a href="api_call.php?method=get&endpoint=user&body=">Perform API Calls</a>
		<ul>
			<li>Change <code>method</code> in the URL to the type of action you want to perform (e.g. <code>get</code>, <code>post</code>, <code>put</code>, <code>delete</code>)</li>
			<li>Change <code>endpoint</code> in the URL to set the name of the API call (e.g. <code>links/LINKID</code>)</li>
			<li>Change <code>body</code> in the URL to be a JSON encoded body (e.g. <code>{"first_name": "Thomas"}</code>)</li>
			<li><a href="https://www.copy.com/developer/documentation#api-calls" target="_blank">Comprehensive API Calls Listing</a></li>
		</ul>
	<?php } else { ?>
	<strong>You need to perform the OAuth dance before you can make API calls.</strong>
	<?php } ?>
	</li>
</ol>

<hr />

<h2>Current OAuth Configuration</h2>

<p>Consumer info is set above with the <em>Set Consumer Key/Secret</em> link. Access token info is set after the OAuth dance completes.</p>

<pre>
<?php

	echo "Consumer Key:        " . @$config->consumer->key . "\n";
	echo "Consumer Secret:     " . @$config->consumer->secret . "\n";
	echo "Access Token:        " . @$config->access->token . "\n";
	echo "Access Token Secret: " . @$config->access->secret . "\n";
?>
</pre>
