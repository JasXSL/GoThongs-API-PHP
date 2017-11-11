# GoThongs-Dev-PHP

PHP Library for GoThongs JSON hooks.

## Install
`composer require jasxsl/got-api`

## Usage

Include the composer autoload: `require __DIR__.'/vendor/autoload.php'`

Create a class and initialize it:

```
require __DIR__.'/vendor/autoload.php';
use JasX\Got\JsonAssetRest as JsonAssetRest;

class Handler extends JsonAssetRest{

  /*
    This is where you handle your request logic
    $type = type of asset, see below for a full list of featured assets
    $asset = ID of the asset. You can see this in the address bar of the GoThongs mod editor for eahc asset
    $uuid = (Not always present) An SL UUID of the agent making the request 
  */
  static function onCall($type, $asset, $uuid){ 
        
  // Example of overriding a book
  if($type === 'GotBook'){

      // This was asset #1 (You can see the asset ID in the URL bar of the GoT mod tool editor)
      if($asset === 1)
        
          // We can return one or more parameters to be overriden by the defaults set in the mod tool editor.
          // Books only accept 'pages' though which is an array of text to put on each page of the book
          return array(
              'pages' => [
                  'Page one', 'Page two'
              ]
          );

      }

      return array(); 
    
  }

}

// Put your JSON KEY from your got mod settings here
Handler::ini("1.47d81e2682b3f97911da4f8dc18b3fd87a614338cd20b268676ac73dd9758");
```

Copy the URL where you hosted your PHP file and paste that into the JSON Webhook box on your mod's settings page on jasx.org/lsl/got and hit save.

## Accepted types and data fields

### GotBook

| Field ID | Type | Explanation |
| --- | --- | --- |
| pages | array | An array of strings, each representing the text of a single page. |
