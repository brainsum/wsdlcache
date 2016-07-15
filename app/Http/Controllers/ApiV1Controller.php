<?php
/**
 * Created by PhpStorm.
 * User: mhavelant
 * Date: 2016.07.15.
 * Time: 15:10
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models as Models;
use App\Custom as Custom;

class ApiV1Controller extends BaseController {

  /**
   * @route("/api/v1/get/wsdl")
   *
   * Example usage:
   *  http://wsdlcache.mabiasz.hu/api/v1/get/wsdl?url=https://tesztfe64.aegon.hu/dijkalk_webservice/gfb.asmx
   *
   * @param Request $request
   *  The urlencoded link for the wsdl file.
   *
   * @return \Laravel\Lumen\Http\ResponseFactory|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
   */
  public function getWSDLByUrlAction(Request $request) {
    $WSDL_Url = $request->input("url");

    $WSDL = Custom\getWsdlInfoByUrl($WSDL_Url);

    if (FALSE === $WSDL || !($WSDL instanceof Models\WSDL)) {
      return response("WSDL not found.", 404);
    }

    // @todo: path, basepath, etc should come from the WSDL class
    $CachedWsdlPath = app()->basePath() . "/container/WSDL/cache/" . $WSDL->getFilename();

    return response()->download($CachedWsdlPath);
  }

}