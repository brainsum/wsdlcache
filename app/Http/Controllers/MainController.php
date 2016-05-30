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
   * @route("/wsdl/name/{name}")
   */
  public function getWSDLByNameAction(String $WSDL_name) {
    $WSDL = Custom\getWsdlInfoByName($WSDL_name);
    dump($WSDL);

    $mode = "wget";

    try {
      switch ($mode) {
        case "curl":
          Custom\downloadWsdlFileByName($WSDL_name);
          break;
        case "get_content":
          Custom\getWsdlContentByName($WSDL_name);
          break;
        case "wget":
          Custom\wgetWsdlFileByName($WSDL_name);
          break;
        default:
          Custom\downloadWsdlFileByName($WSDL_name);
      }
    } catch(\Exception $exc) {
      dump($exc);
    }
    return view("debug");
  }

  /**
   * @route("/wsdl/url/{url}")
   *
   */
  public function getWSDLByUrlAction(String $WSDL_url) {
    /** @var Models\WSDL $WSDL */
    $WSDL = Custom\getWsdlInfoByUrl(urldecode($WSDL_url));

    dump($WSDL);

    // We use the filename one so we don't have to worry about additional info
    // like the filename
    Custom\downloadWsdlFileByName($WSDL->getName());

    return view("debug");
  }

}