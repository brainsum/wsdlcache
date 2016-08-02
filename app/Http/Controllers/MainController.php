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

class MainController extends BaseController {

  /**
   * Display the managed WSDLs
   *
   * @return \Illuminate\View\View
   */
  public function indexAction() {
    $wsdlMap = Custom\getWsdlMapAsArray();

    $today = new \DateTime();
    $today->setTime(0,0,0);
    $today = $today->getTimestamp();

    return view("index", array(
      'wsdlList' => $wsdlMap,
      'today' => $today
    ));
  }

  /**
   * Action which gives back the log file for the given WSDL
   *
   * @param int $WSDL_id
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function downloadWSDLLogByIdAction($WSDL_id) {
    $WSDL_log_path = Custom\getWsdlLogPath($WSDL_id);

    return response()->download($WSDL_log_path);
  }

  /**
   * Helper action for testing and debugging the download functions.
   *
   * @param int $WSDL_id
   *
   * @return \Illuminate\View\View
   *
   * @route("/wsdl/name/{name}")
   */
  public function getWSDLByIdAction($WSDL_id) {
    $WSDL = Custom\getWsdlInfoByID($WSDL_id);
    dump($WSDL);

    try {
      Custom\downloadWsdlFileById($WSDL_id);
    } catch(\Exception $exc) {
      dump($exc);
    }
    return view("debug");
  }

  /**
   * Sandbox action for r&d and testing
   *
   * @route("/sandbox")
   * @return \Illuminate\View\View
   */
  public function sandboxAction() {

    return view("debug");
  }
}