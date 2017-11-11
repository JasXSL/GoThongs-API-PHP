<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__.'/vendor/autoload.php';

use JasX\Got\JsonAssetRest as JsonAssetRest;

class Handler extends JsonAssetRest{

	static function onCall($type, $asset, $uuid){ 
		
		if($type === 'GotBook'){

			if($asset === 58)
				return array(
					'pages' => [
						'Page one', 'Page two'
					]
				);

			


		}

		return array(); 
	
	}

}

Handler::ini("67.47d81e2682b3f97911da4f8dc18b3fd87a614338cd20b268676ac73dd9758");
