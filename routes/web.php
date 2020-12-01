<?php

// Router http://e-warehouse/
$router->get('/', function () use ($router) {
    echo "Home";
});

//Router http://e-warehouse/setup/
// $router->group(['prefix' => 'reset']), function() use ($router) {
//     $router->put('reset', 'SetupController@Reset');
// }

// Router http://e-warehouse/entrance/
$router->group(['prefix' => 'entrance'], function() use ($router) {
    $router->put('update', 'EntranceController@Update');                                    // request: new_pallet_id, old_pallet_id
});

// Router http://e-warehouse/storage/store
$router->group(['prefix' => 'storage/store'], function() use ($router) {
    $router->get('{pallet_id}', 'StorageStoreController@GetDisplay');
    $router->put('movingtostorage/update', 'StorageStoreController@MovingToStorageUpdate'); // request: pallet_id
    $router->put('onstorage/update', 'StorageStoreController@OnStorageUpdate');             // request: pallet_id, row_id
});

// Router http://e-warehouse/storage/pickup
$router->group(['prefix' => 'storage/pickup'], function() use ($router) {
    $router->get('{type_id}&{bag_amount}', 'StoragePickupController@GetDisplay');
    $router->put('update', 'StoragePickupController@PalletUpdate');                         // request: pallet_id
});

// Router http://e-warehouse/loading/
$router->group(['prefix' => 'loading'], function() use ($router) {
    $router->put('palletready/update', 'LoadingController@PalletReadyUpdate');              // request: pallet_id
    $router->put('onloading/update', 'LoadingController@OnLoadingUpdate');                  // request: bag_id, bags_to_load
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