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

    try {
      Custom\downloadWsdlFileByName($WSDL_name);
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
    $fullMap = Custom\getWsdlMapAsArray();

    foreach ($fullMap as $WSDL) {
      Custom\checkAndUpdateWSDLFileWithCurl($WSDL);
    }

    return view("debug");
  }
}