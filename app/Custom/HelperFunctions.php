<?php
/**
 * Created by PhpStorm.
 * User: mhavelant
 * Date: 2016.05.23.
 * Time: 14:47
 */

/**
 * @todo: Parse ok, but we need to be able to update the map.
 * @todo: Maybe add a DownloadByWsdlObject function. (for easier persist)
 */

namespace App\Custom;

use Dotenv\Exception\InvalidFileException;
use Nathanmac\Utilities\Parser\Parser;
use App\Models\WSDL;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

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
 * Gets the wsdlMap from the given path with the given filename.
 *
 * @param string $pathFromRoot
 * @param string $file
 * @return \SimpleXMLElement
 */
function getWsdlMapAsSimpleXML($pathFromRoot = "container", $file = "wsdlMap.xml") {
  $basePath = app()->basePath();

  $mapObject = simplexml_load_file("$basePath/$pathFromRoot/$file");

  if (FALSE === $mapObject) {
    throw new InvalidFileException("Loading the WSDL map failed.");
  }

  return $mapObject;
}

/**
 * Saves the modified wsdlMap object to the given path with the given filename.
 *
 * @param \SimpleXMLElement $mapObject
 * @param string $pathFromRoot
 * @param string $file
 * @return bool
 */
function updateWsdlMap(\SimpleXMLElement $mapObject, $pathFromRoot = "container", $file = "wsdlMap.xml") {
  $basePath = app()->basePath();

  try {
    $mapObject->saveXML("$basePath/$pathFromRoot/$file");
  } catch (\Exception $exc) {
    dump($exc);
    return false;
  }

  return true;
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
  $wsdl->setStatusCode((int) $wsdlDataArray["statusCode"]);
  $wsdl->setType($wsdlDataArray["type"]);
  $wsdl->setUserName($wsdlDataArray["userName"]);
  $wsdl->setPassword($wsdlDataArray["password"]);
  $wsdl->setCurlSslVersion($wsdlDataArray["curlSslVersion"]);

  return $wsdl;
}

/**
 * Helper function to get a WSDL object from the map or given array.
 * Returns false when no match has been found.
 *
 * @param $name
 * @param null $container
 *    Can be used to increase performance (by eliminating the need to re-parse the map) or load a different WSDL-source.
 * @return \App\Models\WSDL|bool
 */
function getWsdlInfoByName($name, $container = null) {
  $wsdlList = (empty($container) ? getWsdlMapAsArray() : $container);

  /** @var WSDL $wsdl */
  foreach ($wsdlList as $wsdl) {
    if ($name == $wsdl->getName()) {
      return $wsdl;
    }
  }

  return false;
}

function getWsdlInfoByUrl($url, $container = null) {
  $wsdlList = (empty($container) ? getWsdlMapAsArray() : $container);

  /** @var WSDL $wsdl */
  foreach ($wsdlList as $wsdl) {
    if ($url == $wsdl->getWsdl()) {
      return $wsdl;
    }
  }

  return false;
}

/**
 * Gets an returns the path to the log for the WSDL
 *
 * @param $WSDL_name
 * @param null $filename
 * @return string
 *  The path to the log file.
 */
function getWsdlLogPath($WSDL_name, $filename = null) {
  $WSDL = getWsdlInfoByName($WSDL_name);

  if ($WSDL !== false) {
    if (empty($filename)) {
      $WSDL->generateFileName();
    } else {
      $WSDL->setFilename($filename);
    }

    $basePath = app()->basePath();
    $logPath = "container/WSDL/logs";

    return "$basePath/$logPath/".$WSDL->getFilename() . "-log.txt";
  } else {
    throw new NotFoundResourceException("The WSDL $WSDL_name is not managed by the wsdl map.");
  }
}

/**
 * Downloads a WSDL by its name as the given filename.
 *  If no filename has been given we generate one based on the name and type.
 *
 * @param $WSDL_name
 *  Case sensitive.
 * @param null $filename
 *  The name of the desired file. Must contain the extension!
 *
 * @throws NotFoundResourceException
 *  When the name is not in he wsdl map.
 */
function downloadWsdlFileByName($WSDL_name, $filename = null) {
  $WSDL = getWsdlInfoByName($WSDL_name);

  if ($WSDL === false) {
    throw new NotFoundResourceException("The WSDL $WSDL_name is not managed by the wsdl map.");
  }

  if (empty($filename)) {
    $WSDL->generateFileName();
  } else {
    $WSDL->setFilename($filename);
  }

  $httpStatus = downloadWsdlFileByUrlWithCurl($WSDL);

  try {
    /** @var \SimpleXMLElement $mapObject */
    $mapObject = getWsdlMapAsSimpleXML();
  } catch (\Dotenv\Exception\InvalidFileException $exc) {
    dump($exc->getMessage());
    return;
  }

  for ($i = 0, $count = count($mapObject->wsdl); $i < $count; ++$i) {
    if ((string) $mapObject->wsdl[$i]->name == $WSDL->getName()) {
      $mapObject->wsdl[$i]->statusCode = $httpStatus;
      $mapObject->wsdl[$i]->checkDate = date("Y-m-d H:i:s");
      $mapObject->wsdl[$i]->modificationDate = date("Y-m-d H:i:s");

      break;
    }
  }

  updateWsdlMap($mapObject);
}

/**
 * Downloads the supplied WSDL to the given filename.
 *
 * @param WSDL $WSDL
 * @return Int $responseCode
 */
function downloadWsdlFileByUrlWithCurl($WSDL) {
  /*
   * @todo: logging at the level of HTTP request, SSL handshake, etc.
   *
   * @try http://stackoverflow.com/questions/17092677/how-to-get-info-on-sent-php-curl-request
   * @try http://stackoverflow.com/questions/3757071/php-debugging-curl
   */
  dump($WSDL);

  $PASS_AS_ENCODED = TRUE;
  $APPENDED_URL = TRUE;
  $DEBUG_MODE = FALSE;

  $basePath = app()->basePath();
  $cachePath = "container/WSDL/cache";
  $logPath = "container/WSDL/logs";

  $ch = curl_init($WSDL->getWsdl($APPENDED_URL));
  $fp = fopen("$basePath/$cachePath/" . $WSDL->getFilename(), "w+");
  $lp = fopen("$basePath/$logPath/" . $WSDL->getFilename() . "-log.txt", "w+");

  $headers = array();

  if (!empty($WSDL->getUserName()) || $WSDL->getUserName() != "null") {
    $headers[] = 'Authorization: Basic '. $WSDL->combinedUserPass($PASS_AS_ENCODED);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $WSDL->combinedUserPass($PASS_AS_ENCODED));
  }

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_STDERR, $lp);
  curl_setopt($ch, CURLOPT_URL, $WSDL->getWsdl($APPENDED_URL));
  curl_setopt($ch, CURLOPT_VERBOSE, true);
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
  curl_setopt($ch, CURLOPT_FILETIME, true);
  curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
  curl_setopt($ch, CURLOPT_SSLVERSION, $WSDL->getCurlSslVersion());
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Needed!! Mb for k&h only

  if (TRUE === $DEBUG_MODE) {
    curl_setopt($ch, CURLOPT_CERTINFO, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
  }

  $result = curl_exec($ch);
  $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  dump($responseCode);
  // dump($ch);
  curl_close($ch);
  fclose($fp);

  if (TRUE === $DEBUG_MODE) {
    print "<pre>$result</pre>";
  }

  print "<pre>" . (($result == TRUE) ? "SUCCESS" : "ERROR") . "</pre>";

  return $responseCode;
}

function getWsdlContentByName($WSDL_name, $filename = null) {
  $WSDL = getWsdlInfoByName($WSDL_name);

  if ($WSDL !== false) {
    $finalFileName = empty($filename) ? $WSDL->generateFileName() : $filename;

    downloadWsdlFileByUrlWithFileGetContents($WSDL->getWsdl(), $finalFileName);
  } else {
    throw new NotFoundResourceException("The WSDL $WSDL_name is not managed by the wsdl map.");
  }
}

function downloadWsdlFileByUrlWithFileGetContents($WSDL_url, $filename) {
  $context = stream_context_create(array(
    'ssl' => array(
      // set some SSL/TLS specific options
      'verify_peer' => false,
      'verify_peer_name' => false,
      'allow_self_signed' => true
    )
  ));

  $data = file_get_contents($WSDL_url, null, $context);

  if ($data === FALSE) {
    throw new NotFoundResourceException("$WSDL_url failed to open.");
  }

  $basePath = app()->basePath();
  $cachePath = "container/WSDL/cache";

  file_put_contents("$basePath/$cachePath/$filename", $data);
}

function wgetWsdlFileByName($WSDL_name, $filename = null) {
  $WSDL = getWsdlInfoByName($WSDL_name);

  if ($WSDL !== false) {
    $finalFileName = empty($filename) ? $WSDL->generateFileName() : $filename;

    downloadWsdlFileByUrlWithWget($WSDL->getWsdl(), $finalFileName);
  } else {
    throw new NotFoundResourceException("The WSDL $WSDL_name is not managed by the wsdl map.");
  }
}

function downloadWsdlFileByUrlWithWget($WSDL_url, $filename) {
  exec("wget -S $WSDL_url", $data, $response);

  dump($response);
  dump($data);
}