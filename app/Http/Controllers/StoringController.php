<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class StoringController extends Controller {
    // mengambil data semua palet untuk disimpan
    public function StoringFetch () {
        $pallets_to_store = DB::select(DB::raw(
            "SELECT
                id,
                production_date
            FROM
                Pallets
            WHERE
                status_id = 2
        "));

        foreach ($pallets_to_store as $pallet_to_store) {
            $pallet_to_store = "P-" . str_pad($pallet_to_store, 10, "0", STR_PAD_LEFT);
            echo "{$pallet_to_store}<br>";
        }
        return ComponentCheck::CurrentTime();
    }

    // update status palet yang siap disimpan
    // status palet menjadi = waiting_to_be_stored
    // public function StoringUpdate ($device_id, $pallet_id) {
    public function StoringUpdate (Request $request) {
        $device_id = $request->get('device_id');
        $pallet_id = $request->get('pallet_id');

        // periksa device ID
        $member_id = ComponentCheck::DeviceID($device_id, 1);
        // periksa pallet ID
        $pallet_id_int = ComponentCheck::PalletID($pallet_id, 1);

        if ($member_id != -1) {
            if ($pallet_id_int != -1) {
                $pallet_data = DB::select(DB::raw(
                    "SELECT
                        po_number,
                        type_id,
                        bag_count,
                        production_date
                    FROM
                        ProductionData
                    WHERE
                        member_id = $member_id
                "));
                
                // data update pallet
                $po_number = $pallet_data[0]->po_number;
                $type_id = $pallet_data[0]->type_id;
                $status_id = 2; // WAITING_TO_BE_STORED
                $bag_count = $pallet_data[0]->bag_count;
                $production_date = '"' . $pallet_data[0]->production_date . '"';

                DB::update(DB::raw(
                    "UPDATE Pallets
                    SET
                        po_number = $po_number,
                        type_id = $type_id,
                        status_id = $status_id,
                        bag_count = $bag_count,
                        production_date = $production_date
                    WHERE
                        id = $pallet_id_int
                "));

                // delete data dari ProductionData
                DB::delete(DB::raw(
                    "DELETE from ProductionData
                    WHERE member_id = $member_id
                "));

                echo "Pallet {$pallet_id} updated.<br>";
                return ComponentCheck::CurrentTime();
            } else {
                echo "Pallet {$pallet_id} invalid or unavailable for storing.<br>";
                return ComponentCheck::CurrentTime();
            }
        } else {
            echo "Unauthorized device.<br>";
            return ComponentCheck::CurrentTime();
        }
    }
}