<?php
/**
 * Created by PhpStorm.
 * User: mhavelant
 * Date: 2016.05.20.
 * Time: 16:40
 */

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models as Models;
use App\Custom as Custom;
use SebastianBergmann\Diff;

class MainController extends BaseController {

  /**
   * Display the managed WSDLs
   *
   * @return \Illuminate\View\View
   */
  public function indexAction() {
      $wsdlMap = Custom\getWsdlMapAsArray();

      return view("index", array(
        'wsdlList' => $wsdlMap
      ));
  }

  /**
   * @param \App\Http\Controllers\String $WSDL_name
   *
   * @return \Illuminate\View\View
   *
   * @route("/wsdl/name/{name}")
   */
  public function getWSDLByNameAction(String $WSDL_name) {
    $WSDL = Custom\getWsdlInfoByName($WSDL_name);
    dump($WSDL);

    $mode = "curl";

    try {
      switch ($mode) {
        case "get_content":
          Custom\getWsdlContentByName($WSDL_name);
          break;
        case "wget":
          Custom\wgetWsdlFileByName($WSDL_name);
          break;
        default: // When mode is curl
          Custom\downloadWsdlFileByName($WSDL_name);
      }
    } catch(\Exception $exc) {
      dump($exc);
    }
    return view("debug");
  }

  public function downloadWSDLLogByNameAction(String $WSDL_name) {
    $WSDL_log_path = Custom\getWsdlLogPath($WSDL_name);

    return response()->download($WSDL_log_path);
  }

  /**
   * @route("/sandbox")
   * @return \Illuminate\View\View
   */
  public function sandboxAction() {
    $oldFile = file_get_contents(app()->basePath() . "/container/wsdlMap.xml");
    $newFile = file_get_contents(app()->basePath() . "/container/wsdlStatus.xml");
    /*
    dump($oldFile);
    dump($newFile);
    */

    // @todo: https://github.com/chrisboulton/php-diff


    $differ = new Custom\CustomDiffer;
    $fileDiff = $differ->diff($oldFile, $newFile);

    dump($fileDiff);

    return view("debug");
  }

  public function placeholder() {
    try {
      /** @var \SimpleXMLElement $mapObject */
      $mapObject = Custom\getWsdlMapAsSimpleXML();
    } catch (\Dotenv\Exception\InvalidFileException $exc) {
      dump($exc->getMessage());
    }

    dump(Custom\getWsdlInfoByName("Aegon"));

    if(!empty($mapObject)) {
      dump($mapObject);

      for ($i = 0, $count = count($mapObject->wsdl); $i < $count; ++$i) {
        dump(array(
          $mapObject->wsdl[$i]->checkDate,
          $mapObject->wsdl[$i]->modificationDate
        ));

        $mapObject->wsdl[$i]->checkDate = date("Y-m-d H:i:s");
        $mapObject->wsdl[$i]->modificationDate = date("Y-m-d H:i:s");

      }

      dump($mapObject);
      Custom\updateWsdlMap($mapObject);
    }

    dump(Custom\getWsdlInfoByName("Aegon"));

    dump("--------------------------");

  }

}