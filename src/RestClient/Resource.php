<?php

namespace JasX\Got\Api\RestClient;

class Resource{

	// Required
	public $id = '';
	public $type = '';

	// Optional
	public $attributes = array();
	public $relationships = array();
	public $links = array();
	public $meta = array();
	
	private $_parent = NULL;


	public function __construct( $parent, array $data ){

		$this->_parent = $parent;

		if( !isset($data['type']) || !isset($data['id']) )
			throw new \Exception('Type and id not present on resource');
		
		$this->id = $data['id'];
		$this->type = $data['type'];

		if( isset($data['attributes']) )
			$this->attributes = (array)$data['attributes'];

		if( isset($data['relationships']) )
			$this->relationships = (array)$data['relationships'];

		if( isset($data['links']) )
			$this->links = (array)$data['links'];
		if( isset($data['meta']) )
			$this->meta = (array)$data['meta'];
	
		// Make sure the relationships are proper
		foreach( $this->relationships as $key => $val ){

			if( !is_array($val) )
				throw new \Exception('Relationships must be assoc arrays: '.$key);

			if( !array_key_exists('data', $val) )
				throw new \Exception('Data is not present on relationship: '.$key.' got: '.json_encode($val, true));
			
			if( !is_array($val['data']) && $val['data'] !== NULL )
				$val['data'] = [$val['data']];


			if( self::isAssoc($val['data']) )
				$this->relationships[$key]['data'] = [$val];
			
		}

	}

	private static function isAssoc( $arr ){
		if( !is_array($arr) )
			return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	public function parent(){
		return $this->_parent; 
	}

	public function getRelated( $type ){

		if( !isset($this->relationships[$type]) )
			throw new \Exception('No such relationship: '.$type);

		$all = $this->relationships[$type]['data'];
		
		$out = [];
		foreach( $all as $linkage ){

			if( isset($linkage['data']) )
				$linkage = $linkage['data'];
			
			$att = $this->parent()->getLinked($linkage['id'], $linkage['type']);
			if( $att )
				$out[] = $att; 

		}

		return $out;

	}
	


}
