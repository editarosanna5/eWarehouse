<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;
require '../../config/warehouse.php';

class StoragePickupController extends Controller {
    public function GetDisplay($type_id, $bags_to_load){
        $pallets_to_load = ceil($bags_to_load / Warehouse::$BagsPerPallet);

        $pallet_to_pick = DB::select(DB::raw(
            "SELECT row_number, column_number, stack_number
            FROM Pallets
            WHERE
                type_id = $type_id
                AND oldest_bag_timestamp > date_sub(now(), interval Warehouse::$DaysToExpiration day)
            ORDER BY
                oldest_bag_timestamp ASC
            LIMIT $pallets_to_load
        "));
    }

    public function PalletUpdate(Request $request) {
        $pallet_data = DB::select(DB::raw(
            "SELECT row_number
            FROM Pallets
            WHERE
                id = $request->pallet_id
        "));
        
        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = 5,
                row_number = null,
                column_number = null,
                stack_number = null
            WHERE
                pallet_id = $request->pallet_id
        "));

        DB::update(DB::raw(
            "UPDATE `Rows`
            SET
                bag_count -= 1
            WHERE
                row_number = $pallet_data[0]->row_number
        "));
    }
}