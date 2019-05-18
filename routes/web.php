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

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->post('decrypt', 'decrypt\DecryptController@decrypt');
$router->post('rsa', 'decrypt\DecryptController@rsa');
$router->post('verify', 'decrypt\DecryptController@verify');
$router->post('regDo', 'user\UserController@regDo');
$router->get('loginDo', 'user\UserController@loginDo');



$router->post('register', 'user\RegController@register');
$router->post('login', 'user\RegController@login');
$router->post('goods', 'goods\GoodsController@goods');
$router->post('goodsInfo', 'goods\GoodsController@goodsInfo');
$router->post('cart', 'goods\GoodsController@cart');
$router->post('cartInfo', 'goods\GoodsController@cartInfo');
$router->post('cartDel', 'goods\GoodsController@cartDel');
$router->post('pay', 'goods\GoodsController@pay');
$router->post('payShow', 'goods\GoodsController@payShow');
$router->get('zPay', 'alipay\AlipayController@zPay');
//$router->post('cartAllDel', 'goods\GoodsController@cartAllDel');



//$router->get('userInfo',['uses'=>'user\RegController@userInfo']);

$router->group(['middleware' => 'l_token'], function () use ($router) {
    $router->get('userInfo',['uses'=>'user\RegController@userInfo']);
});