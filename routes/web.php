<?php

// Router http://e-warehouse/
$router->get('/', function () use ($router) {
    echo "Home";
});

// Router http://e-warehouse/entrance/
$router->group(['prefix' => 'entrance'], function() use ($router) {
    $router->put('pallet/update/{pallet_id}', 'EntranceController@PallletUpdate');
    $router->put('bag/update/{pallet_id}', 'EntranceController@BagUpdate');
});

// Router http://e-warehouse/storage/store
$router->group(['prefix' => 'storage/store'], function() use ($router) {
    $router->get('', 'StorageStoreController@GetDisplay');
    $router->put('update', 'StorageStoreController@PalletUpdate');
});

// Router http://e-warehouse/storage/pickup
$router->group(['prefix' => 'storage/pickup'], function() use ($router) {
    $router->get('', 'StoragePickupController@GetDisplay');
    $router->put('update', 'StoragePickupController@PalletUpdate');
});

// Router http://e-warehouse/loading/
$router->group(['prefix' => 'loading'], function() use ($router) {
    $router->put('pallet/update', 'LoadingController@PalletUpdate');
    $router->put('bag/update', 'LoadingController@BagUpdate');
});

////////////////////////////// TEST //////////////////////////////

// Router http://e-warehouse/type/
$router->group(['prefix' => 'type'], function () use ($router) {
    $router->get('', 'TypeController@index');
    $router->post('', 'TypeController@create');

    $router->get('{id}', 'TypeController@show');
    $router->put('{id}', 'TypeController@update');
    $router->delete('{id}', 'TypeController@delete');
});