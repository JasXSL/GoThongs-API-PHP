<?php
/*

	This is an example of setting up a server that accepts webhook calls:
	https://github.com/JasXSL/GoThongs/wiki/JSON-Webhooks

 */

// Uncomment when done debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Require the composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Use the GoT Api server library
use JasX\Got\Api\WebhookServer as WebhookServer;

// Extend the server
class Handler extends WebhookServer{

	// This is the input method in which you build and return your custom asset data
	static function onCall($type, $asset, $uuid){ 
		
		// A book asset was requested
		if($type === 'GotBook'){

			$pages = [];

			// ID of book. You can see this in the URL of the book editor
			if($asset === 59){

				$pages = ['Page one', 'Page two'];
				// If requested by Jas, we output different book content
				if( $uuid === 'cf2625ff-b1e9-4478-8e6b-b954abde056b' )
					$pages = ['Hello there you sexy shoober you'];

			}
			
			// Return the data
			return array(
				'pages' => $pages
			);

		}

		// If we get to this point, we have no way of handling this asset, return empty data
		return array(); 
	
	}

}

// initialize the server with your JSON KEY from your mod
Handler::ini("4.dd1e1e923155a3b5f991cfe92979e9a9d3378ff83d8f5ed7e648e10a9a79d5");
