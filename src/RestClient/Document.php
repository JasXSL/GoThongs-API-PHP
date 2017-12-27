<?php

namespace JasX\Got\Api\RestClient;

class Document{

	public $errors = [];
	public $data = [];
	public $included = [];
	public $meta = array(
		"success" => 0,
		"callback" => "",
		"errors" => [],
		"type" => ""
	);
	public $custom_meta = [];
	public $links = NULL;

	public function __construct( array $data ){

		$this->links = new \stdClass(); 

		if( isset($data['errors']) )
			$this->errors = $data['errors'];
		
		else{
			
			$this->data = $this->arrayToResources($data['data']); 


			if( isset($data['meta']) ){

				foreach( $data['meta'] as $key => $val ){
					
					if( isset($this->meta[$key]) )
						$this->meta[$key] = $val;
					else
						$this->custom_meta[$key] = $val;

				}

			}

			if( isset($data['included']) )
				$this->included = $this->arrayToResources($data['included']);

			if( isset($data['links']) )
				$this->links = (object)$data['links'];

			
		}

	}

	private function arrayToResources( array $input ){

		$out = [];
		foreach($input as $r)
			$out[] = new Resource($this, $r);
		
		return $out;

	}

	public function isFatalError(){
		return count($this->errors);
	}

	public function isSuccess(){
		return $this->meta['success'] > 0;
	}

	public function getErrors(){
		return $this->meta['errors'];
	}

	public function getFatalErrors(){
		return $this->errors;
	}

	public function getEndpoint(){
		return $this->meta['type'];
	}

	public function getCallback(){
		return $this->meta['callback'];
	}


	public function getLinked( $id, $type ){

		foreach( $this->included as $resource ){

			if( $resource->id == $id && $resource->type == $type )
				return $resource;
			

		}

		return false;

	}



}
