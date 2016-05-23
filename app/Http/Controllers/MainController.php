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

      return view("index", array(
        'wsdlList' => $wsdlMap
      ));
  }

  /**
   * @param \App\Http\Controllers\String $WSDL_name
   *
   * @return \Illuminate\View\View
   *
   * @route("/getwsdl/{name}")
   */
  public function getWSDLAction(String $WSDL_name) {
    Custom\downloadWsdlFileByName($WSDL_name);

    dump($WSDL_name);

    return view("debug");
  }
}