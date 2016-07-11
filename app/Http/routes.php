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

$app->get('/', "MainController@indexAction");

$app->get('/info', function () {
    return view("info");
});

$app->get('/wsdl/name/{name}', array(
  'as' => "getWSDLByName",
  'uses' => "MainController@getWSDLByNameAction"
));

$app->get('/wsdl/name/{name}', array(
  'as' => "getWSDLByName",
  'uses' => "MainController@getWSDLByNameAction"
));

$app->get('/log/{name}/download', array(
  'as' => "getWSDLLogByName",
  'uses' => "MainController@getWSDLLogByNameAction"
));

$app->get('/wsdl/url/{url}', "MainController@getWSDLByUrlAction");

$app->get('/sandbox', "MainController@sandboxAction");