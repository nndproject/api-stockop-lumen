<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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


$router->group(['prefix' => 'api'], function () use ($router) {

    $router->post('/login','\App\Http\Controllers\Users\UserController@login');


    $router->group(['prefix' => 'stock-opname'], function () use ($router) {
        $router->get('/', '\App\Http\Controllers\StockOpname\StockOpnameController@index');
        $router->get('monitoring/{id}', '\App\Http\Controllers\StockOpname\StockOpnameController@monitoring');

        $router->get('list-item/{bulan}/{tahun}', '\App\Http\Controllers\StockOpname\DetailItemController@listitem');
        $router->get('detail-item/{bulan}/{tahun}/{itemno}', '\App\Http\Controllers\StockOpname\DetailItemController@detailitem');
        $router->post('item/update', '\App\Http\Controllers\StockOpname\DetailItemController@updatestockitem');

    });


    /* $router->group(
        ['middleware' => 'auth'], 
        function() use ($router) {
            $router->get('users', function() {
                $users = \App\Models\User::all();
                return response()->json($users);
            });
        }
    ); */


});