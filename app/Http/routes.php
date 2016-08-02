<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/** Main */

$app->get('/', "MainController@indexAction");

$app->get('/wsdl/id/{id}', array(
  'as' => "getWSDLById",
  'uses' => "MainController@getWSDLByIdAction"
));

$app->get('/log/{id}/download', array(
  'as' => "downloadWSDLLogById",
  'uses' => "MainController@downloadWSDLLogByIdAction"
));

if (app()->environment("local")) {
    $app->get(
      '/dev',
      function () {
          return view("dev");
      }
    );

    $app->get(
      '/info',
      function () {
          return view("info");
      }
    );

    $app->get('/sandbox', "MainController@sandboxAction");
}
/** API v1 */

$app->get('/api/v1/get/wsdl', array(
  'as' => "apiV1GetWsdlByUrl",
  'uses' => "ApiV1Controller@getWSDLByUrlAction"
));