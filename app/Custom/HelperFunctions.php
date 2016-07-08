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
  $wsdl->setAvailable($wsdlDataArray["lastStatus"] == 1 ? true : false);
  $wsdl->setType($wsdlDataArray["type"]);
  $wsdl->setUserName($wsdlDataArray["userName"]);
  $wsdl->setPassword($wsdlDataArray["password"]);

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

  if ($WSDL !== false) {
    if (empty($filename)) {
      $WSDL->generateFileName();
    } else {
      $WSDL->setFilename($filename);
    }

    downloadWsdlFileByUrlWithCurl($WSDL);
  } else {
    throw new NotFoundResourceException("The WSDL $WSDL_name is not managed by the wsdl map.");
  }
}

/**
 * Downloads the supplied WSDL to the given filename.
 *
 * @param WSDL $WSDL
 */
function downloadWsdlFileByUrlWithCurl($WSDL) {
  /*
   * @todo: logging at the level of HTTP request, SSL handshake, etc.
   *
   * @try http://stackoverflow.com/questions/17092677/how-to-get-info-on-sent-php-curl-request
   * @try http://stackoverflow.com/questions/3757071/php-debugging-curl
   */
  dump($WSDL);

  $basePath = app()->basePath();
  $cachePath = "container/WSDL/cache";
  $logPath = "container/WSDL/logs";

  $ch = curl_init($WSDL->getWsdl());
  $fp = fopen("$basePath/$cachePath/" . $WSDL->getFilename(), "w+");
  $lp = fopen("$basePath/$logPath/" . $WSDL->getFilename() . "-log.txt", "w+");

  $headers = array(
    'Authorization: Basic '. base64_encode($WSDL->getUserName() . ":" . $WSDL->getPassword())
  );
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_STDERR, $lp);
  curl_setopt($ch, CURLOPT_URL, $WSDL->getWsdl());
  curl_setopt($ch, CURLOPT_VERBOSE, true);
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
  curl_setopt($ch, CURLOPT_FILETIME, true);
  curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
  curl_setopt($ch, CURLOPT_SSLVERSION, 4); // 4 is the answer for KandH
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  // curl_setopt($ch, CURLOPT_CERTINFO, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Needed!!
  curl_setopt($ch, CURLOPT_USERPWD, base64_encode($WSDL->getUserName() . ":" . $WSDL->getPassword()));

  $result = curl_exec($ch);
  dump($ch);
  curl_close($ch);
  fclose($fp);

  print "<pre>" . ($result) . "</pre>";
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