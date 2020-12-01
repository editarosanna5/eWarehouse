<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class EntranceController extends Controller {
    public function Update(Request $request){
        $new_pallet_id = $request->new_pallet_id;
        $old_pallet_id = $request->old_pallet_id;

        $pallet_id_check = DB::select(DB::raw(
            "SELECT id
            FROM Pallets
            WHERE
                id = $new_pallet_id
                AND status_id = 2
        "));

        if ($pallet_id_check != null){
            $bag_data = DB::select(DB::raw(
                "SELECT
                    COUNT(pallet_id) AS bag_count,
                    type_id,
                    MIN(production_timestamp) AS oldest_bag_timestamp
                FROM Bags
                WHERE
                    pallet_id = $old_pallet_id
                    AND status_id = 2
                GROUP BY
                    pallet_id,
                    type_id
            "));
            $bag_count = $bag_data[0]->bag_count;
            $type_id = $bag_data[0]->type_id;
            $oldest_bag_timestamp = $bag_data[0]->oldest_bag_timestamp;

            if ($bag_count > 0){
                DB::update(DB::raw(
                    "UPDATE Pallets
                    SET
                        type_id = $type_id,
                        status_id = 3,
                        bag_count = $bag_count,
                        oldest_bag_timestamp = \"$oldest_bag_timestamp\"
                    WHERE
                        id = $new_pallet_id;
                "));

                DB::update(DB::raw(
                    "UPDATE Pallets
                    SET
                        type_id = null,
                        bag_count = 0,
                        oldest_bag_timestamp = null
                    WHERE
                        id = $old_pallet_id;
                "));

                echo "Pallet updated.<br>";
                echo "pallet_id = {$new_pallet_id}, oldest_bag_timestamp = {$oldest_bag_timestamp} <br>";
                
                DB::update(DB::raw(
                    "UPDATE Bags
                    SET
                        pallet_id = $new_pallet_id,
                        status_id = 3
                    WHERE
                        pallet_id = $old_pallet_id
                "));

                echo "<br>Bags updated.<br>";
            } else {
                echo "Pallet empty.";
            }
        } else {
            echo "pallet_id = {$new_pallet_id} not found.";
        }
    }
}