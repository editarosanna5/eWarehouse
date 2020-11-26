<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class EntranceController extends Controller {

    // idenya (pallet update):
    // saat scan qr code palet, diperoleh $pallet_id.
    // $pallet_id dimasukin ke data 49 karung terakhir.

    // idenya (bag update):
    // query 1 karung yang memiliki pallet_id yg sesuai, dan status
    // ready.
    //     berarti status karung diupdate duluan dibanding palet

    public function Update(Request $request){
        $pallet_id_check = DB::select(DB::raw("SELECT COUNT(pallet_id) FROM Pallets WHERE pallet_id = $request->new_pallet_id"));

        if ($pallet_id_check > 0){
            $bag_count = DB::select(DB::raw(
                "SELECT COUNT(pallet_id)
                FROM Bags
                WHERE
                    pallet_id = $request->old_pallet_id
                    AND status_id = 2
            "));

            if ($bag_count > 0){
                $bag_data = DB::select(DB::raw("SELECT type_id, production_timestamp FROM Bags WHERE pallet_id = $request->old_pallet_id AND status_id = 2 LIMIT 1"));
                $type_id = $bag_data[0]->type_id;
                $oldest_bag_timestamp = $bag_data[0]->production_timestamp;

                DB::update(DB::raw(
                    "UPDATE Pallets
                    SET
                        type_id = $type_id,
                        status_id = 2,
                        bag_count = $bag_count,
                        oldest_bag_timestamp = $oldest_bag_timestamp
                    WHERE
                        id = $request->new_pallet_id;
                "));

                echo "Pallet updated.<br>";
                echo "pallet_id = {$request->new_pallet_id}, oldest_bag_timestamp = {$oldest_bag_timestamp} <br>";
                
                DB::update(DB::raw(
                    "UPDATE Bags
                    SET
                        pallet_id = $request->new_pallet_id,
                        status_id = 3
                    WHERE
                        pallet_id = $request->old_pallet_id
                "));

                echo "<br>Bags updated.<br>";
            } else {
                echo "Pallet empty.";
            }
        } else {
            echo "pallet_id = {$request->new_pallet_id} not found.";
        }
    }
}