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
	'GetAssetData', 
	// Targets
	['cf2625ff-b1e9-4478-8e6b-b954abde056b'],
	// Data
	array(
		// User is our basetype
		// See type and field definitions here: https://github.com/JasXSL/GoThongs/wiki/JSON-REST-API---Data-Fetch-Types
		'type' => 'user',
		// Fields we want to get
		'fields' => array(
			"id" => "",					// Get the user ID as an attribute
			"active_thong" => array(	// Fetch a related object with data about the asset
				"level" => "",				// Fetch the thong level as an attribute
				"thong" => array(			// Fetch a related object with the thong class
					'name' => '',				// Fetch the name of the class
					'image' => ''				// Fetch the image URL of the class
				)
			)
		),
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
				echo '---- Resource '.$resource->type.' #'.$resource->id.'<br />';
				// Output the attributes we requested from the user
				echo '------ Attributes: '.json_encode($resource->attributes).'<br />';
				// Output some info about the relationships we requested
				echo '------ Relationships: '.json_encode($resource->relationships).'<br />';
				// Links and meta are not currently used
				echo '------ Links: '.json_encode($resource->links).'<br />';
				echo '------ Meta: '.json_encode($resource->meta).'<br />';
				
				// Fetch the related active_thong asset
				$active_thong = $resource->getRelated('active_thong')[0];
				echo '------ active_thong:<br />';
				// Output the active_thong level attribute
				echo '-------- level: '.$active_thong->attributes['level'].'<br />';
				
				// We requested to get the thong class of the active_thong. Fetch it from the active_thong relations
				$thongclass = $active_thong->getRelated('thong')[0];
				echo '-------- class: <br />';
				// Output the attributes we requested
				echo '---------- name: '.$thongclass->attributes['name'].'<br />';
				echo '---------- image: '.$thongclass->attributes['image'].'<br />';


			}

		}

	}

}
