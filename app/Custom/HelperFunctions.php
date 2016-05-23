<?php
/**
 * Created by PhpStorm.
 * User: mhavelant
 * Date: 2016.05.23.
 * Time: 14:47
 */

namespace App\Custom;

use Nathanmac\Utilities\Parser\Parser;
use App\Models\WSDL;

/**
 * @param string $pathFromRoot
 * @param string $file
 *
 * @return array(WSDL)
 */
function getWsdlMapAsArray($pathFromRoot = "container", $file = "wsdlMap.xml") {
  $basePath = app()->basePath();

  $mapData = file_get_contents("$basePath/$pathFromRoot/$file");

  $parser = new Parser();
  $mapAsArray = $parser->xml($mapData);

  $arrayOfWsdlObjects = array();

  // The map stores each WSDL info between <wsdl></wsdl> tags
  // The parser returns this as array( "wsdl" => array(...));
  // The wsdl array can contain:
  //    1, The info for a single wsdl
  //    2, The info for each wsdl as separate arrays
  // This means, we have to consider these two cases
  // So when the 0 numeric key exists, we have to deal with multiple wsdl descriptions
  // If 0 is not a key, we can be sure (based on the xml structure), that it's a single wsdl description
  if (array_key_exists(0, $mapAsArray["wsdl"])) {
    foreach ($mapAsArray["wsdl"] as $wsdl) {
      $arrayOfWsdlObjects[] = parseWsdlFromArrayToObject($wsdl);
    }
  } else {
    $arrayOfWsdlObjects[] = parseWsdlFromArrayToObject($mapAsArray["wsdl"]);
  }

  return $arrayOfWsdlObjects;
}

/**
 * Helper function to convert an Array from the XML map
 * to the WSDL convenience object
 *
 * @param $wsdlDataArray
 * @return \App\Models\WSDL
 */
function parseWsdlFromArrayToObject($wsdlDataArray) {
  $wsdl = new WSDL($wsdlDataArray["url"]);
  $wsdl->setName($wsdlDataArray["name"]);
  $wsdl->setAvailable(FALSE);
  $wsdl->setLastCheck($wsdlDataArray["checkDate"]);
  $wsdl->setLastModification($wsdlDataArray["modificationDate"]);

  return $wsdl;
}
