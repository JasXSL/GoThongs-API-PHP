<?php
namespace JasX\Got;
header('Content-Type: application/json');

class JsonAssetRest{

	// This should be overwritten and return an array with data to overwrite the JSON object with 
	static function onCall($type, $asset, $uuid){ return array(); }

	static $out = array(
		"errors" => [],
		"notices" => [],
		"data" => [],
	);

	static function addError($error){
		self::$out['errors'][] = $error;
	}
	static function addNotice($notice){
		self::$out['notices'][] = $notice;
	}
	static function addResponse($id, $data){
		self::$out['data'][] = array(
			"id" => $id,
			"data" =>$data
		);
	}

	static function ini($authToken){

		$headers = self::getHeaders();

		if(!isset($headers['Got-Mod-Token'])){
			self::addError("A mod creator has forgotten to use a token for authorization. Tell them to fix it!");
			self::finish();
		}

		if($headers['Got-Mod-Token'] !== $authToken){
			self::addError("A mod creator has used the wrong token for authorization. Tell them to fix it!");
			self::finish();
		}

		
		// Post data is just JSON. We'll have to do this to get the data
		$post = json_decode(file_get_contents('php://input'), true);

		if(!is_array($post)){
			self::addError("JSON Data sent from JasX is invalid.");
			self::finish();
		}

		foreach($post as $data){
			// Make sure the required params are in there
			if(!is_array($data) || !isset($data['id']) || !isset($data['type']) || !isset($data['asset_id']))
				continue;

			$id = $data['id'];
			$type = $data['type'];
			$asset_id = (int)$data['asset_id'];
			$uuid = '';
			if(isset($data['uuid']))
				$uuid = $data['uuid'];

			// Let the extending class handle the call
			$fetch = static::onCall($type, $asset_id, $uuid);
			
			// If the response is proper, we'll add it as a response
			if(is_array($fetch))
				self::addResponse($id, $fetch);

		}



		self::finish();
	}

	static function finish(){
		die(json_encode(self::$out));
	}

	static function getHeaders(){
		$headers = []; 
		foreach ($_SERVER as $name => $value) 
		{ 
			if (substr($name, 0, 5) == 'HTTP_') 
			{ 
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
			} 
		} 
		return $headers; 
	}


}
