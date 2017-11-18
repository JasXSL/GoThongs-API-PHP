<?php

// Uncomment when done debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load from composer
require __DIR__.'/../vendor/autoload.php';

// Use the rest client
use JasX\Got\Api\RestClient as Client;

// Paste your API key here
$client = new Client('JSON_KEY_HERE');

/*
	Here we add a task to our call
 */
$client->addTask(
	// Endpoint: https://github.com/JasXSL/GoThongs/wiki/JSON%20REST%20API%20-%20Endpoint%20Identifiers
	'SetGeneric', 
	// Targets
	['cf2625ff-b1e9-4478-8e6b-b954abde056b'],
	// Data
	array(
		'add_gold' => -10		// Remove 1 silver (10 copper) from all targets
	)
);

// Execute the call
$response = $client->exec();

// Request was a fail
if( !$response ){

	echo 'Request failed: '.json_encode($client->errors)."<br />";

}
else{

	// Go through all task responses
	foreach( $response as $document ){

		// This task had a fatal error
		if( $document->isFatalError() )
			echo 'Document failed: '.json_encode($document->getFatalErrors()).'<br />';
		
		// No fatal errors
		else{

			// Non-fatal errors and notices
			$nonfatal = $document->getErrors();
			if( $nonfatal )
				echo 'The following nonfatal errors occurred: '.implode(', ', $nonfatal).'<br />';
			
			// Get some info about what the task was
			echo 'Call <strong>'.$document->getEndpoint().'</strong>:<br />';
			echo '-- Success: '.json_encode($document->isSuccess()).'<br />';
			echo '-- Callback: '.json_encode($document->getCallback()).'<br />';
			
			// Get the user objects here
			echo '-- Data:<br />';
			foreach( $document->data as $resource ){

				// A resource always has an ID and type
				echo '---- Resource '.$resource->type.'<br />';
				echo '---- Character UUID: '.$resource->id.'<br />';
				
				if( $resource->attributes['add_gold'] === 0 )
					echo '---- INSUFFICIENT FUNDS';
				else
					echo '---- Copper added: '.$resource->attributes['add_gold'].'<br />';

			}

		}

	}

}
