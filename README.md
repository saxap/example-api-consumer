# Copy PHP OAuth Consumer Example

This project is simply an example implementation to make things easier for developers.
We will be releasing client libraries for different platforms in the future, which will
be maintained and licensed in a way that you can incorporate them into your projects easily.

We don't recommend integrating this example consumer code into your applications.

## How To Use

* `sudo pecl install oauth`
* `chmod +w keys.json`
* Update config.php and change $callbackURL to point to your local web server
* Browse to index.php
* Create an application
* Add your consumer keys and consumer secret to the application
* Go through the OAuth handshake
* Make some sample API calls

## keys.json

If you ever break your keys.json, you can recreate it starting with this:

	{
	  "consumer": {
		"key": "",
		"secret": ""
	  },
	  "access": {
		"token": "",
		"secret": ""
	  }
	}

## License

This is licensed under the BSD license.

This is a derivitive work of the following project: 
[github/flijten/OAuth-consumer](https://github.com/flijten/OAuth-consumer)

Also contains a function for highlighting JSON in pre PHP5.4:
[daveperrett.com: Format JSON With PHP](http://www.daveperrett.com/articles/2008/03/11/format-json-with-php/)

