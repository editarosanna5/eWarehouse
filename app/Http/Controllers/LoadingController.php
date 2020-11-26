<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class LoadingController extends Controller {
    public function PalletReadyUpdate(Request $request){
        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = 6
            WHERE
                id = $request->pallet_id
        "));
    }

    public function OnLoadingUpdate(Request $request) {
        $bag_data = DB::select(DB::raw(
            "SELECT pallet_id
            FROM Bags
            WHERE
                id = $request->bag_id
            "));

        $pallet_data = DB::select(DB::raw(
            "SELECT bag_count, oldest_bag_timestamp
            FROM Pallets
            WHERE
                pallet_id = $bag_data[0]->pallet_id
        "));

        if ($request->bags_to_load > 1) {
            DB::update(DB::raw(
                "UPDATE Bags
                SET
                    pallet_id = null,
                    status_id = 4
                WHERE
                    id = $request->bag_id
            "));
            if ($pallet_data[0]->bag_count > 1) {
                $new_pallet_status = 7;
            } else {
                $new_pallet_status = 1;
            }
        } else {
            if ($pallet_data[0]->bag_count > 1) {
                $new_pallet_status = 6;
            } else {
                $new_pallet_status = 1;
            }
        }

        if ($new_pallet_status == 1) {
            $oldest_bag_timestamp = null;
        } else {
            $oldest_bag_timestamp = $pallet_data[0]->oldest_bag_timestamp;
        }

        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = $new_pallet_status,
                bag_count = bag_count - 1,
                oldest_bag_timestamp = $oldest_bag_timestamp
            WHERE
                id = $bag_data[0]->pallet_id
        "));
    }
}