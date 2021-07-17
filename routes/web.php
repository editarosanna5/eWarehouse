<?php

// Home http://e-warehouse
$router->get('/', function() use ($router) {
    include 'client/Home.html';
});

// Router http://e-warehouse/warehouse
$router->get('/warehouse', 'StorageController@StorageFetch');

// Router http://e-warehouse/receiving
$router->group(['prefix' => 'receiving'], function() use ($router) {
    // Router http://e-warehouse/receiving
    $router->get('', 'ReceivingController@ReceivingUpdate');

    // Router http://e-warehouse/receiving/form
    $router->get('form', function() use ($router) {
        include 'client/Packaging.html';
    });
});

// Router http://e-warehouse/storing
$router->group(['prefix' => 'storing'], function() use ($router) {
    $router->get('', 'StoringController@StoringFetch');
    $router->put('', 'StoringController@StoringUpdate');
});

// Router http://e-warehouse/putaway
$router->group(['prefix' => 'putaway'], function() use ($router) {
    // Router http://e-warehouse/putaway/moving
    // $router->get('moving', 'PutawayController@PutawayMovingFetch');
    $router->put('moving', 'PutawayController@PutawayMovingUpdate');
    // Router http://e-warehouse/putaway/map
    $router->get('map', 'PutawayController@PutawayMap');

    // Router http://e-warehouse/putaway/arrival
    $router->put('arrival', 'PutawayController@PutawayArrivalUpdate');
});

// Router http://e-warehouse/picking
$router->group(['prefix' => 'picking'], function() use ($router) {
    // Router http://e-warehouse/picking
    $router->get('', 'PickingController@PickingUpdate');

    // Router http://e-warehouse/picking/form
    $router->get('form', function() use ($router) {
        include 'client/Order.html';
    });

    // Router http://e-warehouse/picking/select
    $router->get('select', 'PickingController@PickingSelect');

    // Router http://e-warehouse/picking/line
    $router->put('line', 'PickingController@PickingLineUpdate');

    // Router http://e-warehouse/picking/moving
    $router->put('moving', 'PickingController@PickingMovingUpdate');

    // Router http://e-warehouse/picking/pallet
    $router->put('pallet', 'PickingController@PickingPalletUpdate');

    // Router http://e-warehouse/picking/bag
    $router->put('bag', 'PickingController@PickingBagUpdate');

    // Router http://e-warehouse/picking/list
    $router->get('list', 'PickingController@PickingList');

    // Router http://e-warehouse/picking/map
    $router->get('map', 'PickingController@PickingMap');

    // Router http://e-warehouse/picking/counter
    $router->get('counter', 'PickingController@LoadingCounter');

    // Router http://e-warehouse/picking/linecheck/true
    $router->get('linecheck/true', function() use ($router) {
        include 'client/AvailableForPickup.html';
    });

    // Router http://e-warehouse/picking/linecheck/false
    $router->get('linecheck/false', function() use ($router) {
        include 'client/UnavailableForPickup.html';
    });
});