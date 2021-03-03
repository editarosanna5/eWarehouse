<?php

// Home http://e-warehouse
$router->get('/', function() use ($router) {
    include 'client/Home.html';
});

// Router http://e-warehouse/login

// Router http://e-warehouse/receiving
$router->group(['prefix' => 'receiving'], function() use ($router) {
    // Router http://e-warehouse/receiving
    $router->put('', 'ReceivingController@ReceivingUpdate');

    // Router http://e-warehouse/receiving/form
    $router->get('form', function() use ($router) {
        include 'client/ReceivingForm.html';
    });
});

// Router http://e-warehouse/storing
$router->group(['prefix' => 'storing'], function() use ($router) {
    $router->get('', 'StoringController@StoringFetch');
    $router->put('', 'StoringController@StoringUpdate');
});

// Router http://e-warehouse/putaway
$router->group(['prefix' => 'storing'], function() use ($router) {
    // Router http://e-warehouse/putaway/moving
    $router->get('moving', 'PutawayController@PutawayMovingFetch');
    $router->put('moving', 'PutawayController@PutawayMovingUpdate');

    // Router http://e-warehouse/putaway/arrival
    $router->put('arrival', 'PutawayController@PutawayArrivalUpdate');
});

// Router http://e-warehouse/picking
$router->group(['prefix' => 'picking'], function() use ($router) {
    // Router http://e-warehouse/picking
    $router->put('', 'PickingController@PickingUpdate');

    // Router http://e-warehouse/picking/form
    $router->get('form', function() use ($router) {
        include 'client/PickingForm.html';
    });

    // Router http://e-warehouse/picking/:order_id

    // Router http://e-warehouse/picking/moving
    $router->put('moving', 'PickingController@PickingMovingUpdate');

    // Router http://e-warehouse/picking/arrival
    $router->put('arrival', 'PickingController@PickingArrivalUpdate');

    // Router http://e-warehouse/picking/loading
    $router->put('loading', 'PickingController@PickingLoadingUpdate');
});