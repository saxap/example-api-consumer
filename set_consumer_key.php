<h1>Set Consumer Key/Secret</h1>

<?php
// Did the user press the button?
if (!empty($_POST)) {
	// Get user provided data
	$consumer_key = $_POST['consumer_key'];
	$consumer_secret = $_POST['consumer_secret'];

	// Open and decode the file
	$data = json_decode(file_get_contents("keys.json"));

	if (!isset($data->consumer) || !isset($data->access)) {
		$data->consumer = null;
		$data->access = null;
	}

	// Setting new data
	$data->consumer->key = $consumer_key;
	$data->consumer->secret = $consumer_secret;

	// Clearing access info, which is most likely not valid for this new consumer
	$data->access->token = '';
	$data->access->secret = '';

	// encode
	$new_data = json_encode($data);

	// write contents to the file
	$handle = fopen("keys.json", 'w');
	fwrite($handle, $new_data);
	fclose($handle);

	// tell user it was ok
	echo "<p>If you don't see any errors, the data was updated successfully.</p>\n";
}
?>

<p>Use this form to set the OAuth Consumer Key and Secret you received while creating a Copy application.</p>

<form method="post" action="">
	<input placeholder="Consumer Key" name="consumer_key" /><br />
	<input placeholder="Consumer Secret" name="consumer_secret" /><br />
	<input type="submit" value="Save Consumer Info" />
</form>

<hr />

<a href="index.php">Return to Main Screen</a>
