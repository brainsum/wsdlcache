<?php
/**
 * Created by PhpStorm.
 * User: mhavelant
 * Date: 2016.05.23.
 * Time: 14:47
 */

namespace App\Custom;

use Doctrine\Instantiator\Exception\UnexpectedValueException;
use Dotenv\Exception\InvalidFileException;
use Nathanmac\Utilities\Parser\Parser;
use App\Models\WSDL;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Illuminate\Support\Facades\Mail;

/**
 * @param string $pathFromRoot
 * @param string $mapFile
 * @param string $statusFile
 * @return \App\Models\WSDL[]
 *
 */
function getWsdlMapAsArray($pathFromRoot = "container", $mapFile = "wsdlMap.xml", $statusFile = "wsdlStatus.xml") {
  $basePath = app()->basePath();

  $mapData = file_get_contents("$basePath/$pathFromRoot/$mapFile");
  $parser = new Parser(); // @todo: switch to simpleXML
  $mapAsArray = $parser->xml($mapData);
/*
  $tmpArr = getXMLAsSimpleXML("container", "wsdlMap.xml");
  dump($tmpArr);
*/

  if (!isset($mapAsArray["wsdl"])) {
    print "The WSDL map is empty. Before using the application, please add elements to the WSDL map.";
    die();
  }

  /** @var WSDL[] $arrayOfWsdlObjects */
  $arrayOfWsdlObjects = array();

  // When we have multiple WSDL definitions in the map, we iterate through them.
  // If it's a single one, then we don't need to iterate.
  if (sizeof($mapAsArray["wsdl"]) > 1) {
    foreach ($mapAsArray["wsdl"] as $wsdl) {
      $arrayOfWsdlObjects[] = parseWsdlFromArrayToObject($wsdl);
    }
  }
  else {
    $arrayOfWsdlObjects[] = parseWsdlFromArrayToObject($mapAsArray["wsdl"]);
  }

  $mapObject = getXMLAsSimpleXML();

  $updateHasHappened = FALSE;

  for ($i = 0; $i < sizeof($arrayOfWsdlObjects); ++$i) {
    try {
      // We try to get the element with the given ID
      // For his to work the ID and the Array index must be the same
      // If we do't temper with it, however, it should be ok, as IDs in the status
      // file start from 0 and increase by one.
      $mapId = $mapId = (int) $mapObject->wsdl[$i]->id;

      // If the execution reaches this point, the status exists.
      // This means we update the WSDL object with the stored status
      $arrayOfWsdlObjects[$i]->setStatusCode(
        (int) $mapObject->wsdl[$i]->statusCode
      );
      $arrayOfWsdlObjects[$i]->setLastCheck(
        new \DateTime($mapObject->wsdl[$i]->checkDate)
      );
      $arrayOfWsdlObjects[$i]->setLastModification(
        new \DateTime($mapObject->wsdl[$i]->modificationDate)
      );
    } catch (\ErrorException $e) {
      // @todo: this should go into the logs
      dump(
        "Create mode. WSDL with id $i has no status, so we create one for it."
      );
      $startingDate = new \DateTime("0000-00-00 00:00:00");
      $updateHasHappened = TRUE;
      $arrayOfWsdlObjects[$i]->setStatusCode(0);
      $arrayOfWsdlObjects[$i]->setLastCheck($startingDate);
      $arrayOfWsdlObjects[$i]->setLastModification($startingDate);
      createNewStatus($mapObject, $arrayOfWsdlObjects[$i]);
    }
  }
  if ($updateHasHappened) {
    if (FALSE === updateWsdlMap($mapObject)) {
      dump("update failed");
    }
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
function getXMLAsSimpleXML($pathFromRoot = "container", $file = "wsdlStatus.xml") {
  $basePath = app()->basePath();

  $mapObject = simplexml_load_file("$basePath/$pathFromRoot/$file");

  if (FALSE === $mapObject) {
    throw new InvalidFileException("Loading the WSDL status map failed.");
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
function updateWsdlMap(\SimpleXMLElement $mapObject, $pathFromRoot = "container", $file = "wsdlStatus.xml") {
  $basePath = app()->basePath();
  try {
    // $mapObject->saveXML("$basePath/$pathFromRoot/$file");

    $dom = new \DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($mapObject->asXML());
    $dom->save("$basePath/$pathFromRoot/$file");

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
  $wsdl->setId($wsdlDataArray["id"]);
  $wsdl->setName($wsdlDataArray["name"]);
  $wsdl->setUserName($wsdlDataArray["userName"]);
  $wsdl->setPassword($wsdlDataArray["password"]);
  $wsdl->setCurlSslVersion($wsdlDataArray["curlSslVersion"]);
  $wsdl->setIsTest($wsdlDataArray["isTest"]);
  $wsdl->setIsKgfb($wsdlDataArray["isKgfb"]);
  $wsdl->setIsCalculation($wsdlDataArray["isCalc"]);
  $wsdl->setDescription($wsdlDataArray["description"]);

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

function getWsdlInfoByID($id, $container = null) {
  $wsdlList = (empty($container) ? getWsdlMapAsArray() : $container);

  /** @var WSDL $wsdl */
  foreach ($wsdlList as $wsdl) {
    if ($id == $wsdl->getId()) {
      return $wsdl;
    }
  }

  return false;
}

function getWsdlInfoByUrl($url, $container = null) {
  $wsdlList = (empty($container) ? getWsdlMapAsArray() : $container);

  /** @var WSDL $wsdl */
  foreach ($wsdlList as $wsdl) {
    if ($url == $wsdl->getWsdl() || $url == $wsdl->getWsdl(true)) {
      return $wsdl;
    }
  }

  return false;
}

/**
 * Gets an returns the path to the log for the WSDL
 *
 * @param $WSDL_id
 * @param null $filename
 * @return string
 *  The path to the log file.
 */
function getWsdlLogPath($WSDL_id, $filename = null) {
  $WSDL = getWsdlInfoByID($WSDL_id);

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
    throw new NotFoundResourceException("The WSDL $WSDL_id is not managed by the wsdl map.");
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

  $httpStatus = checkAndUpdateWSDLFileWithCurl($WSDL);
}

function downloadWsdlFileById($WSDL_id, $filename = null) {
  $WSDL = getWsdlInfoByID($WSDL_id);

  if ($WSDL === false) {
    throw new NotFoundResourceException("The WSDL $WSDL_id is not managed by the wsdl map.");
  }

  if (empty($filename)) {
    $WSDL->generateFileName();
  } else {
    $WSDL->setFilename($filename);
  }

  $httpStatus = checkAndUpdateWSDLFileWithCurl($WSDL);
}

/**
 * Downloads the supplied WSDL to the given filename.
 *
 * @param WSDL $WSDL
 * @return Int $responseCode
 */
function checkAndUpdateWSDLFileWithCurl($WSDL) {
  /*
   * @todo: logging at the level of HTTP request, SSL handshake, etc.
   *
   * @try http://stackoverflow.com/questions/17092677/how-to-get-info-on-sent-php-curl-request
   * @try http://stackoverflow.com/questions/3757071/php-debugging-curl
   */

  $PASS_AS_ENCODED = TRUE;
  $APPENDED_URL = TRUE;
  $DEBUG_MODE = FALSE;

  $basePath = app()->basePath() . "/container/WSDL";
  $cachePath = "$basePath/cache";
  $logPath = "$basePath/logs";
  $backupPath = "$basePath/backup";

  $cachedWsdlPath = "$cachePath/" . $WSDL->getFilename();
  $logWsdlPath = $logPath . "/" . $WSDL->getFilename() . "-log.txt";
  $backupFilePath = $backupPath . "/" . $WSDL->getBackupFilename();

  /** @todo: maybe try guzle instead of curl https://github.com/guzzle/guzzle */
  $ch = curl_init($WSDL->getWsdl($APPENDED_URL));
/*
  if(file_exists($logWsdlPath)) {
    if (substr(sprintf('%o', fileperms($logWsdlPath)), -4) != "0664") {
      //dump("fileperms is not 0664");
      //dump(substr(sprintf('%o', fileperms($logWsdlPath)), -4));
      //chmod($logWsdlPath, 0664);// rw for owner + group, r for others
    }

    $lp = fopen($logWsdlPath, "a+");
  } else {

    //dump("fiel is new");
    //dump(substr(sprintf('%o', fileperms($logWsdlPath)), -4));
    //chmod($logWsdlPath, 0664);
  }
*/
  $lp = fopen($logWsdlPath, "a+");
  fwrite($lp, "\n[".date("Y-m-d H:i:s")."]\n");

  /** @todo: set this at a WSDL level */
  $curl_settings = array(
    CURLOPT_STDERR => $lp,
    CURLOPT_URL => $WSDL->getWsdl($APPENDED_URL),
    CURLOPT_VERBOSE => TRUE,
    CURLOPT_HTTPGET => TRUE,
    CURLOPT_FORBID_REUSE => TRUE,
    CURLOPT_FILETIME => TRUE,
    CURLOPT_FRESH_CONNECT => TRUE,
    CURLOPT_SSLVERSION => $WSDL->getCurlSslVersion(),
    CURLOPT_SSL_VERIFYPEER => FALSE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_FOLLOWLOCATION => TRUE,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 0
  );

  if (!empty($WSDL->getUserName()) || $WSDL->getUserName() != "null") {
    $headers = array();
    $headers[] = 'Authorization: Basic '. $WSDL->getCombinedUserPass($PASS_AS_ENCODED);
    $curl_settings[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
    $curl_settings[CURLOPT_USERPWD] = $WSDL->getCombinedUserPass($PASS_AS_ENCODED);
    $curl_settings[CURLOPT_HTTPHEADER] = $headers;
  }

  if (TRUE === $DEBUG_MODE) {
    $curl_settings[CURLOPT_CERTINFO] = true;
    $curl_settings[CURLOPT_HEADER] = true;
  }

  curl_setopt_array($ch, $curl_settings);

  $result = curl_exec($ch);
  $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err     = curl_errno($ch);
  $errmsg  = curl_error($ch);
  curl_close($ch);
  fclose($lp);

  $WSDL->setStatusCode($responseCode);

  /**
   * Get diff.
   */
  $wsdlIsAlreadyInCache = TRUE;

  try {
    // Try to get the old file.
    $oldFileContents = file_get_contents($cachedWsdlPath);
  } catch (\Exception $exc) {
    // When we can't open the old file, it's probably because it's new.
    // So we create the cached file for it.
    $newCache = fopen($cachedWsdlPath, "w+");
    fwrite($newCache, $result);
    fclose($newCache);

    // Diffcount is set to 1 (> 0) so we set the modification date as well for new files.
    wsdlStatusUpdateWrapper($WSDL, 1);

    $wsdlIsAlreadyInCache = FALSE;
  }

  // When the file already exists in the cache.
  if (TRUE === $wsdlIsAlreadyInCache) {
    $differ = new CustomDiffer;
    $differ->setOldFilePath($cachedWsdlPath);
    $differ->setNewFilePath("File from remote server");
    $fileDiff = $differ->diff($oldFileContents, $result);

    // If there are diffs, the cache and remote files are not in sync
    if (0 < $differ->getDiffCount()) {
      // So we create a backup of the old file
      $backup = fopen($backupFilePath, "w+");
      fwrite($backup, $oldFileContents);
      fclose($backup);

      // So we save the new file in the cache
      $newCache = fopen($cachedWsdlPath, "w+");
      fwrite($newCache, $result);
      fclose($newCache);

      $template = "Emails.wsdl_modification_info";
      $options = array(
        "datetimeOfCheck" => date("Y-m-d H:i:s"),
        "WSDLDiff" => htmlentities($fileDiff),
        "WSDLName" => $WSDL->getName(TRUE,TRUE,TRUE),
        "WSDLUrl" => $WSDL->getWsdl(TRUE)
      );
      $messageSubject = "Attention! The " . $options["WSDLName"] . " WSDL file has been updated!";
      // Send mail about diffs
      sendCustomMail($template, $options, $messageSubject);
    }

    wsdlStatusUpdateWrapper($WSDL, $differ->getDiffCount());
  }
  return $responseCode;
}

/**
 * @param WSDL $WSDL
 * @param $diffCount
 */
function wsdlStatusUpdateWrapper($WSDL, $diffCount) {
  try {
    /** @var \SimpleXMLElement $mapObject */
    $mapObject = getXMLAsSimpleXML();
  } catch (\Exception $exc) {
    dump($exc->getMessage());
    return;
  }

  $currDate = new \DateTime();
  $WSDL->setLastCheck($currDate);
  $WSDL->setLastModification($currDate);

  dump($WSDL);

  try {
    // We try to get the element with the given ID
    // For his to work the ID and the Array index must be the same
    // If we do't temper with it, however, it should be ok, as IDs in the status
    // file start from 0 and increase by one.
    $mapId = (int) $mapObject->wsdl[$WSDL->getId()]->id;
    // If we can get the element, we update it
    $mapObject->wsdl[$WSDL->getId()]->statusCode = $WSDL->getStatusCode();
    $mapObject->wsdl[$WSDL->getId()]->checkDate = $WSDL->getLastCheck()->format("Y-m-d H:i:s");

    if (0 < $diffCount) {
      $mapObject->wsdl[$WSDL->getId()]->modificationDate = $WSDL->getLastModification()->format("Y-m-d H:i:s");
    }

  } catch (\ErrorException $exc) {
    // If we can't access the element, we create it
    dump($exc);
    createNewStatus($mapObject, $WSDL);
  }

  // When there was a modification, and the status is unavailable, we send a warning email
  if (0 < $diffCount && !$WSDL->isAvailable()) {
    $template = "Emails.wsdl_unavailable";
    $options = array(
      "WSDLStatusCode" => $WSDL->getStatusCode(),
      "WSDLName" => $WSDL->getName(TRUE,TRUE,TRUE),
      "WSDLUrl" => $WSDL->getWsdl(TRUE)
    );
    $messageSubject = "WARNING! The " . $options["WSDLName"] . " WSDL host is unavailable!";
    sendCustomMail($template, $options, $messageSubject);
  }

  if (FALSE === updateWsdlMap($mapObject)) {
    dump("update failed");
  }
}

/**
 * The $mapObject is a SimpleXMLElement, so we can expand it here by adding
 * a new "wsdl" child to it with the new status data.
 *
 * @param \SimpleXMLElement $mapObject
 * @param WSDL $WSDL
 * @return \SimpleXMLElement
 */
function createNewStatus(&$mapObject, $WSDL) {
  $element = $mapObject->addChild("wsdl");

  $element->addChild("id", $WSDL->getId());
  $element->addChild("statusCode", $WSDL->getStatusCode());
  $element->addChild("checkDate", $WSDL->getLastCheck()->format("Y-m-d H:i:s"));
  $element->addChild("modificationDate", $WSDL->getLastModification()->format("Y-m-d H:i:s"));
}

function sendCustomMail($template, $options, $messageSubject) {
  Mail::send($template,
    $options,
    function($msg) use ($messageSubject) {
      $msg->to(env("MAIL_TO_ADDRESS"))
        ->subject($messageSubject);
    });

}

// @todo: move these to the Jobs folder

function wsdlUpdateJob() {
  $fullMap = getWsdlMapAsArray();

  foreach ($fullMap as $WSDL) {
    checkAndUpdateWSDLFileWithCurl($WSDL);
  }
}

function reminderForAppUpdate() {
  Mail::send("Emails.update_reminder",
    function($msg) {
      $msg->to(env("MAIL_TO_ADDRESS"))
        ->subject("Reminder - Check for updates!");
    });
}