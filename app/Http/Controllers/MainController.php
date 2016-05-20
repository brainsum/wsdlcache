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

class MainController extends BaseController {

  public function indexAction() {
      return view("index");
  }

  /**
   * @param \App\Http\Controllers\String $WSDL_name
   * @postponed
   */
  public function getWSDLAction(String $WSDL_name) {
    $basePath = app()->basePath();
    $cachePath = "container/WSDL/cache";

    // @todo: getWsdlDataByName($WSDL_name) {
    // open wsdlMap.xml
    // search for name
    // return WSDL object (defined in WSDL.php)
    //}
    $WSDL_url = "https://0000922995:Pkr80022995@tesztfe64.aegon.hu/dijkalk_webservice/gfb.asmx?WSDL";

    $ch = curl_init($WSDL_url);
    $fp = fopen("$basePath/$cachePath/aegon.test.xml", "w");

  }

  public function testGetAction() {
    $basePath = app()->basePath();
    $cachePath = "container/WSDL/cache";

    $WSDL_url = "https://0000922995:Pkr80022995@tesztfe64.aegon.hu/dijkalk_webservice/gfb.asmx?WSDL";

    $ch = curl_init($WSDL_url);
    $fp = fopen("$basePath/$cachePath/aegon.test.xml", "w+");

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
  }


}