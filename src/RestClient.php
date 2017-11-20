<?php

namespace JasX\Got\Api;
use \JasX\Got\Api\RestClient\Document as Document;

class RestClient{

	const URL = 'http://jasx.org/lsl/got/app/mod_api/';

	private $api_key = '';

	// Requests
	public $tasks = [];

	// Response
	public $errors = [];				// Fatal errors
	public $documents = [];				// JsonAPI Documents

	public function __construct( $api_key ){

		$this->api_key = $api_key;
		return $this;

	}

	public function addTask( $type, $target, $data = array(), $callback = '' ){
		
		$this->tasks[] = array(
			"type" => $type,
			"target" => $target,
			"data" => $data,
			"callback" => $callback
		);
		return $this;

	}

	// Sends the HTTP Request, returns an array of documents
	public function exec(){

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, self::URL);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Got-Mod-Token: '.$this->api_key
		]);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, [
			"tasks" => json_encode($this->tasks)
		]);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		
		$raw = curl_exec($ch);
		$server_output = (array)json_decode($raw, true);
		
		curl_close ($ch);
		$this->tasks = [];
		
		if( isset($server_output['errors']) ){
			
			$this->errors = $server_output['errors'];
			return false;

		}

		if( !isset($server_output['jsonapi']) ){

			throw new \Exception("Server error. Server responded with: ".$raw);

		}

		return array_map(function($val){ return new Document($val); }, $server_output['jsonapi']);

	}


}
