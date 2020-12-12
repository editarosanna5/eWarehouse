<?php

// HTML
$router->get('/', function () use ($router) {
    include 'client/Home.html';
});
$router->get('/packing', function() use ($router) {
    include 'client/Packing.html';
});
$router->get('/storage', function() use ($router) {
    include 'client/Storage.html';
});
$router->get('/pickup', function() use ($router) {
    include 'client/Loading.html';
});

//Router http://e-warehouse/setup/
// $router->group(['prefix' => 'reset']), function() use ($router) {
//     $router->put('reset', 'SetupController@Reset');
// }

$router->group(['prefix' => 'packing'], function() use ($router) {
    // $router->put('update', 'PackingController@Update');
});

// Router http://e-warehouse/entrance/
$router->group(['prefix' => 'entrance'], function() use ($router) {
    // http://e-warehouse/entrance/update
    // Request:
    //     new_pallet_id : id palet tempat peletakan karung
    //     old_pallet_id : pallet_id reserved yang dimiliki karung sebelum
    //                     diletakkan pada palet
    // Deskripsi:
    //     Update id palet pada data karung ketika karung dari robot
    //     palletizer diletakkan pada palet
    $router->put('update', 'EntranceController@Update');
});

// Router http://e-warehouse/storage/store
$router->group(['prefix' => 'storage'], function() use ($router) {
    // http://e-warehouse/storage/{pallet_id}
    // Deskripsi:
    //     Mengambil data pilihan line penyimpanan untuk penyimpanan palet
    //     dengan id {pallet_id}
    $router->get('{pallet_id}', 'StorageStoreController@GetDisplay');

    // http://e-warehouse/storage/movingtostorage/update
    // Request:
    //     pallet_id : id palet yang discan sesaat sebelum penyimpanan
    // Deskripsi:
    //     Menandakan bahwa palet tersebut akan segera disimpan.
    //     Terjadi update status palet dari "WAITING_TO_BE_STORED"
    //     menjadi "MOVING_TO_STORAGE_ZONE"
    $router->put('movingtostorage/update', 'StorageStoreController@MovingToStorageUpdate');

    // http://e-warehouse/storage/onstorage/update
    // Request:
    //     pallet_id : id palet yang terakhir discan (id palet yang
    //                 sedang dibawa ke line penyimpanan)
    //     row_id    : id line penyimpanan tempat palet tersebut
    //                 disimpan
    // Deskripsi:
    //     Menandakan bahwa palet dengan id "pallet_id" sudah disimpan
    //     pada line penyimpanan dengan id "row_id".
    //     Status palet berubah dari "MOVING_TO_STORAGE_ZONE" menjadi
    //     "ON_STORAGE".
    //     Ditambahkan data row, column, dan stack pada data palet.
    //     Jumlah palet pada data line penyimpanan dengan id "row_id"
    //     bertambah 1.
    $router->put('onstorage/update', 'StorageStoreController@OnStorageUpdate');
});

// Router http://e-warehouse/pickup
$router->group(['prefix' => 'pickup'], function() use ($router) {

    // http://storage/{type_id}&{bag_amount}
    // Deskripsi:
    //     Menerima data DO, jenis karung, dan jumlah karung untuk
    //     diload.
    //     Untuk saat ini, input diterima via forms.
    //     Data jumlah karung juga dikirim ke scanner karung pada
    //     loading zone.
    //     Data jenis karung yang ditandai oleh {type_id} dan data
    //     jumlah karung yang ditandai oleh {bag_amount} digunakan
    //     untuk menampilkan opsi palet yang dapat diambil pada
    //     line penyimpanan.
    $router->get('{type_id}&{bag_amount}', 'StoragePickupController@GetDisplay');

    // http://e-warehouse/pickup/update
    // Request:
    //     pallet_id : id palet yang akan diangkut ke loading zone
    // Deskripsi:
    //     Menandakan bahwa palet dengan id "pallet_id" sedang
    //     diangkut ke loading zone.
    //     Status palet berubah dari "ON_STORAGE" menjadi
    //     "MOVING_TO_LOADING_ZONE".
    $router->put('update', 'StoragePickupController@PalletUpdate');
});

// Router http://e-warehouse/loading/
$router->group(['prefix' => 'loading'], function() use ($router) {

    // http://e-warehouse/loading/palletready/update
    // Request:
    //     pallet_id : id palet yang sudah sampai di loading zone
    //     dan siap untuk loading karung
    // Deskripsi:
    //     Menandakan bahwa palet dengan id "pallet_id" sudah
    //     sampai di loading zone dan siap untuk proses loading
    //     karung.
    // Status palet berubah dari "MOVING_TO_LOADING_ZONE" menjadi
    // "READY_LOADING_ZONE".
    $router->put('palletready/update', 'LoadingController@PalletReadyUpdate');

   //  http://e-warehouse/loading/onloading/update
   //  Request:
   //      bag_id       : id karung yang sedang diload
   //      bags_to_load : jumlah karung tersisa yang harus diangkut
   // Deskripsi:
   //      Menandakan proses pengangkutan karung ke truk konsumen.
   //      Status karung berubah dari "ON_PALLET" menjadi "LOADED".
   //      Status palet berubah dari "READY_LOADING_ZONE" menjadi
   //      "LOADING" ketika karung pertama pada palet tersebut
   //      diangkut.
   //      bag_count pada palet mengalami decrement.
   //      Ketika karung pada palet habis, status palet berubah dari
   //      "LOADING" menjadi "READY".
   //      Ketika bags_to_load habis tetapi masih ada karung tersisa
   //      pada palet, status palet berubah dari "LOADING" menjadi
   //      "READY_LOADING_ZONE".
   //      bags_to_load diperoleh dari data forms DO yang sebelumnya
   //      disimpan juga pada scanner karung. Nilai bags_to_load
   //      mengalami decrement pada scanner setiap pembacaan QR code
   //      karung.
    $router->put('onloading/update', 'LoadingController@OnLoadingUpdate');
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