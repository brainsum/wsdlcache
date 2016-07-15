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
 * @param string $file
 *
 * @return array(WSDL)
 */
function getWsdlMapAsArray($pathFromRoot = "container", $mapFile = "wsdlMap.xml", $statusFile = "wsdlStatus.xml") {
  $basePath = app()->basePath();

  $mapData = file_get_contents("$basePath/$pathFromRoot/$mapFile");
  $parser = new Parser();
  $mapAsArray = $parser->xml($mapData);

  /** @var WSDL[] $arrayOfWsdlObjects */
  $arrayOfWsdlObjects = array();

  // The map stores each WSDL info between <wsdl></wsdl> tags
  // The parser returns this as array( "wsdl" => array(...));
  // The wsdl array can contain:
  //    1, The info for a single wsdl
  //    2, The info for each wsdl as separate arrays
  // This means, we have to consider these two cases
  // So when the 0 numeric key exists, we have to deal with multiple wsdl descriptions
  // If 0 is not a key, we can be sure (based on the xml structure), that it's a single wsdl description

  $mapContainsMultipleWsdlData = array_key_exists(0, $mapAsArray["wsdl"]);

  if ($mapContainsMultipleWsdlData) {
    foreach ($mapAsArray["wsdl"] as $wsdl) {
      $arrayOfWsdlObjects[] = parseWsdlFromArrayToObject($wsdl);
    }
  } else {
    $arrayOfWsdlObjects[] = parseWsdlFromArrayToObject($mapAsArray["wsdl"]);
  }

  $statusData = file_get_contents("$basePath/$pathFromRoot/$statusFile");
  $parser2 = new Parser();
  $statusAsArray = $parser2->xml($statusData);

  $statusContainsMultipleWsdlData = array_key_exists(0, $statusAsArray["wsdl"]);

  if ($mapContainsMultipleWsdlData && $statusContainsMultipleWsdlData && count($statusAsArray["wsdl"]) !== count($mapAsArray["wsdl"])) {
    throw new UnexpectedValueException("The count of wsdls in the map and statuses don't match!");
  }

  /**
   * @fixme @todo refactor needed.
   */
  if (TRUE === $mapContainsMultipleWsdlData && TRUE === $statusContainsMultipleWsdlData) {
    foreach($arrayOfWsdlObjects as $wsdl) {
      foreach ($statusAsArray["wsdl"] as $status) {
        if ($status["id"] == $wsdl->getId()) {
          $wsdl->setLastCheck(new \DateTime($status["checkDate"]));
          $wsdl->setLastModification(new \DateTime($status["modificationDate"]));
          $wsdl->setStatusCode((int) $status["statusCode"]);
        }
      }
    }
  } else {
    $arrayOfWsdlObjects[0]->setLastCheck($statusAsArray["wsdl"]["checkDate"]);
    $arrayOfWsdlObjects[0]->setLastModification($statusAsArray["wsdl"]["modificationDate"]);
    $arrayOfWsdlObjects[0]->setStatusCode((int) $statusAsArray["wsdl"]["statusCode"]);
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
  $wsdl->setId($wsdlDataArray["id"]);
  $wsdl->setName($wsdlDataArray["name"]);
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
    if ($url == $wsdl->getWsdl() || $url == $wsdl->getWsdl(true)) {
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

  $ch = curl_init($WSDL->getWsdl($APPENDED_URL));
  $lp = fopen($logWsdlPath, "a+");
  fwrite($lp, "\n[".date("Y-m-d H:i:s")."]\n");

  $headers = array();

  if (!empty($WSDL->getUserName()) || $WSDL->getUserName() != "null") {
    $headers[] = 'Authorization: Basic '. $WSDL->getCombinedUserPass($PASS_AS_ENCODED);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $WSDL->getCombinedUserPass($PASS_AS_ENCODED));
  }

  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_STDERR, $lp);
  curl_setopt($ch, CURLOPT_URL, $WSDL->getWsdl($APPENDED_URL));
  curl_setopt($ch, CURLOPT_VERBOSE, true);
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
  curl_setopt($ch, CURLOPT_FILETIME, true);
  curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
  curl_setopt($ch, CURLOPT_SSLVERSION, $WSDL->getCurlSslVersion());
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Needed!! Mb for k&h only
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

  if (TRUE === $DEBUG_MODE) {
    curl_setopt($ch, CURLOPT_CERTINFO, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
  }

  $result = curl_exec($ch);
  $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

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

      // We also have to send a mail about the differences.
      Mail::send("Emails.wsdl_modification_info",
        array(
          "datetimeOfCheck" => date("Y-m-d H:i:s"),
          "WSDLDiff" => htmlentities($fileDiff),
          "WSDLName" => $WSDL->getName(),
          "WSDLUrl" => $WSDL->getWsdl(TRUE)
        ),
        function($msg) use ($WSDL) {
          $msg->to("mhavelant+lumen2@brainsum.com")
            ->subject("Attention! The " . $WSDL->getName() . " WSDL file has been updated!");
        });
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
  } catch (\Dotenv\Exception\InvalidFileException $exc) {
    dump($exc->getMessage());
    return;
  }

  $currDate = new \DateTime();

  /* We only update the current wsdl object data. */
  for ($i = 0, $count = count($mapObject->wsdl); $i < $count; ++$i) {
    if ((string) $mapObject->wsdl[$i]->id == $WSDL->getId()) {
      $mapObject->wsdl[$i]->statusCode = $WSDL->getStatusCode();
      $mapObject->wsdl[$i]->checkDate = $currDate->format("Y-m-d H:i:s");

      if (0 < $diffCount) {
        $mapObject->wsdl[$i]->modificationDate = $currDate->format("Y-m-d H:i:s");
      }

      // When a modification has been done and the status is an error, we send an email
      // When a host becomes unavailable, the file gets overwritten even when the result is empty.
      if (strtotime($mapObject->wsdl[$i]->modificationDate) == $currDate->getTimestamp() && !$WSDL->isAvailable()) {
        Mail::send("Emails.wsdl_unavailable",
        array(
          "WSDLStatusCode" => $WSDL->getStatusCode(),
          "WSDLName" => $WSDL->getName(),
          "WSDLUrl" => $WSDL->getWsdl(TRUE)
        ),
        function($msg) use ($WSDL) {
          $msg->to("mhavelant+lumen2@brainsum.com")
            ->subject("WARNING! The " . $WSDL->getName() . " WSDL host is unavailable!");
        });
      }

      break;
    }
  }

  if (FALSE === updateWsdlMap($mapObject)) {
    dump("update failed");
  }
}


