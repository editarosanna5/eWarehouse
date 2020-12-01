<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class StoragePickupController extends Controller {
    public function GetDisplay($type_id, $bag_amount) {
        $BagsPerPallet = WarehouseConfig::$BagsPerPallet;
        $DaysToExpiration = WarehouseConfig::$DaysToExpiration;

        echo "OPSI LINE PENGAMBILAN<br>";
        echo "pallet_id; (line, kolom, stack)<br>";
        
        $pallet_amount = ceil($bag_amount / $BagsPerPallet);

        $pallets_to_pick = DB::select(DB::raw(
            "SELECT
                id,
                row_number,
                column_number,
                stack_number
            FROM Pallets
            WHERE
                type_id = $type_id
                AND oldest_bag_timestamp > date_sub(now(), interval $DaysToExpiration day)
            ORDER BY
                oldest_bag_timestamp ASC,
                row_number ASC,
                column_number ASC,
                stack_number DESC
            LIMIT $pallet_amount
        "));

        foreach ($pallets_to_pick as $pallet) {
            echo "<br> pallet_id = " . $pallet->id . "; (" . $pallet->row_number . "," . $pallet->column_number . "," . $pallet->stack_number . ")";
        }
    }

    public function PalletUpdate(Request $request) {
        $pallet_id = $request->pallet_id;
        
        $pallet_data = DB::select(DB::raw(
            "SELECT
                row_number
            FROM Pallets
            WHERE
                id = $pallet_id
        "));

        $row_number = $pallet_data[0]->row_number;
        
        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = 6,
                row_number = null,
                column_number = null,
                stack_number = null
            WHERE
                id = $pallet_id
        "));

        DB::update(DB::raw(
            "UPDATE `Rows`
            SET
                pallet_count = pallet_count - 1
            WHERE
                id = $row_number
        "));
    }
}