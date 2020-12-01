<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class LoadingController extends Controller {
    public function PalletReadyUpdate(Request $request){
        $pallet_id = $request->pallet_id;

        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = 7
            WHERE
                id = $request->pallet_id
        "));
    }

    public function OnLoadingUpdate(Request $request) {
        $bag_id = $request->bag_id;

        $bag_data = DB::select(DB::raw(
            "SELECT
                pallet_id
            FROM Bags
            WHERE
                id = $request->bag_id
            "));

        $pallet_id = $bag_data[0]->pallet_id;

        $pallet_data = DB::select(DB::raw(
            "SELECT
                bag_count,
                oldest_bag_timestamp
            FROM Pallets
            WHERE
                pallet_id = $pallet_id
        "));

        $bag_count = $pallet_data[0]->bag_count;
        $oldest_bag_timestamp = $pallet_data[0]->oldest_bag_timestamp;

        if ($request->bags_to_load > 1) {
            DB::update(DB::raw(
                "UPDATE Bags
                SET
                    pallet_id = null,
                    status_id = 3
                WHERE
                    id = $request->bag_id
            "));
            if ($bag_count > 1) {
                $new_pallet_status = 8;
            } else {
                $new_pallet_status = 2;
            }
        } else {
            if ($bag_count > 1) {
                $new_pallet_status = 7;
            } else {
                $new_pallet_status = 2;
            }
        }

        if ($new_pallet_status == 2) {
            $oldest_bag_timestamp = null;
        }

        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = $new_pallet_status,
                bag_count = bag_count - 1,
                oldest_bag_timestamp = $oldest_bag_timestamp
            WHERE
                id = $pallet_id
        "));
    }
}