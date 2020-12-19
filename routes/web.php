<?php

// Home http://e-warehouse/
$router->get('/', function () use ($router) {
    include 'client/Home.html';
});

// Router http://e-warehouse/packing/
$router->group(['prefix' => 'packing'], function() use ($router) {
    $router->get('', function() use ($router) {
        include 'client/Packing.html';
    });

    $router->get('create', 'EntranceController@Create');
});

// Router http://e-warehouse/entrance/
$router->group(['prefix' => 'entrance'], function() use ($router) {
    $router->put('update', 'EntranceController@Update');
});

// Router http://e-warehouse/storage/
$router->group(['prefix' => 'storage'], function() use ($router) {
    $router->get('', function() use ($router) {
        include 'client/Storage.html';
    });
    $router->get('map', 'StorageStoreController@MovingToStorageUpdate');
    $router->put('onstorage/update', 'StorageStoreController@OnStorageUpdate');
});

// Router http://e-warehouse/pickup
$router->group(['prefix' => 'pickup'], function() use ($router) {
    $router->get('', function() use ($router) {
        include 'client/Loading.html';
    });
    $router->get('map', 'StoragePickupController@GetDisplay');
    $router->put('update', 'StoragePickupController@PalletUpdate');
});

// Router http://e-warehouse/loading/
$router->group(['prefix' => 'loading'], function() use ($router) {
    $router->put('palletready/update', 'LoadingController@PalletReadyUpdate');
    $router->put('onloading/update', 'LoadingController@OnLoadingUpdate');
});