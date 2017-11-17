<?php
namespace JasX\Got\Api;
header('Content-Type: application/json');

abstract class WebhookServer{

	// This should be overwritten and return an array with data to overwrite the JSON object with 
	// It's an abstract static method, but php5 generates an improper error for its use
	// Remake into abstract once JasX is updated to PHP7
	static function onCall( $type, $asset, $uuid ){}

	// Output data
	static $out = array(
		"errors" => [],
		"notices" => [],
		"data" => [],
	);

	// Adds a single error message
	static function addError( $error ){
		self::$out['errors'][] = $error;
	}

	// Adds a single notice
	static function addNotice( $notice ){
		self::$out['notices'][] = $notice;
	}

	// Adds an asset to respond with
	static function addResponse( $id, $data ){
		
		self::$out['data'][] = array(
			"id" => $id,
			"data" =>$data
		);

	}

	// Runs the whole thing
	static function ini( $authToken ){

		$headers = self::getHeaders();

		if( !isset($headers['Got-Mod-Token']) ){

			self::addError("A mod creator has forgotten to use a token for authorization. Tell them to fix it!");
			self::finish();

		}

		if( $headers['Got-Mod-Token'] !== $authToken ){

			self::addError("A mod creator has used the wrong token for authorization. Tell them to fix it!");
			self::finish();

		}

		
		// Post data is just JSON. We'll have to do this to get the data
		$post = json_decode(file_get_contents('php://input'), true);

		if( !is_array($post) ){

			self::addError("Data received is not valid JSON.");
			self::finish();

		}

		if( !isset($post['data']) ){

			self::addError("DATA field missing");
			self::finish();

		}

		foreach( $post['data'] as $data ){

			// Make sure the required params are in there
			if( !is_array($data) || !isset($data['id']) || !isset($data['type']) || !isset($data['asset_id']) )
				continue;

			$id = $data['id'];
			$type = $data['type'];
			$asset_id = (int)$data['asset_id'];
			$uuid = '';
			if( isset($data['uuid']) )
				$uuid = $data['uuid'];

			// Let the extending class handle the call
			$fetch = static::onCall($type, $asset_id, $uuid);
			
			// If the response is proper, we'll add it as a response
			if( is_array($fetch) )
				self::addResponse($id, $fetch);

		}

		self::finish();
	}

	// Outputs the data and exits
	static function finish(){

		die(json_encode(self::$out));

	}

	// nginx-safe version of getting request headers
	static function getHeaders(){

		$headers = []; 
		foreach( $_SERVER as $name => $value ){ 
			if( substr($name, 0, 5) == 'HTTP_' ){ 
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
			} 
		} 
		return $headers; 

	}


}
