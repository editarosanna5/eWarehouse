<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class EntranceController extends Controller {

    public function Create(Request $request){
        $po_number = $request->get('po_number');
        $type_id = $request->get('type_id');
        $production_timestamp = $request->get('production_timestamp');
        
        $device_id = $request->get('device_id');
        $temp = explode("-", $device_id);
        $group_id = $temp[1];
        $device_id = $temp[2];

        $temp = explode("-", $po_number);
        if (count($temp) == 4) {
            if ($temp[0] == "PO" && preg_match('/[0-9]{2}/', $temp[1]) && preg_match('/[0-9]{8}/', $temp[2]) && preg_match('/[0-9]{10}/', $temp[3])) {
                $bag_id = intval($temp[3]);
                for ($i = 1; $i <= 49; $i++) {
                    DB::insert(DB::raw(
                        "INSERT INTO Bags
                        (type_id, pallet_id, status_id, po_number, production_timestamp)
                        VALUES ($type_id, $device_id, 2, \"$po_number\", \"$production_timestamp\") 
                    "));

                    echo "Karung {$po_number} ditambahkan<br>";

                    $temp[3] = str_pad($bag_id+$i, 10, "0", STR_PAD_LEFT);
                    $po_number = implode("-", $temp);

                    DB::update(DB::raw(
                        "UPDATE Pallets
                        SET 
                            type_id = $type_id,
                            bag_count = $i,
                            oldest_bag_timestamp = \"$production_timestamp\"
                        WHERE
                            id = $device_id
                    "));
                }
            } else {
                echo "Invalid PO Number.";
            }
        } else {
            echo "Invalid PO Number.";
        }
    }

    public function Update(Request $request) {
        $new_pallet_id = $request->new_pallet_id;
        $temp = explode("-", $new_pallet_id);
        $new_pallet_id = intval($temp[1]);
        
        $device_id = $request->device_id;
        $temp = explode("-", $device_id);
        $group_id = intval($temp[1]);
        $old_pallet_id = intval($temp[2]);

        if ($temp[0] == "DEV" && preg_match('/[0-9]{2}/', $temp[1]) && preg_match('/[0-9]{3}/', $temp[2])) {
            $device_check = DB::select(DB::raw(
                "SELECT COUNT(*)
                FROM Devices
                WHERE
                    group_id = $group_id
                    AND id = $old_pallet_id
            "));

            if ($device_check != null) {
                $group_name = DB::select(DB::raw(
                    "SELECT group_name
                    FROM DeviceGroups
                    WHERE
                        id = $group_id
                "));
                $group_name = $group_name[0]->group_name;
        
                if ($group_name == "PACKAGING_ZONE") {
                    $pallet_id_check = DB::select(DB::raw(
                        "SELECT id
                        FROM Pallets
                        WHERE
                            id = $new_pallet_id
                            AND status_id = 2
                    "));
            
                    if ($pallet_id_check != null) {
                        $bag_data = DB::select(DB::raw(
                            "SELECT
                                type_id,
                                bag_count,
                                oldest_bag_timestamp
                            FROM Pallets
                            WHERE
                                id = $old_pallet_id
                                AND status_id = 1
                        "));
                        $type_id = $bag_data[0]->type_id;
                        $bag_count = $bag_data[0]->bag_count;
                        $oldest_bag_timestamp = $bag_data[0]->oldest_bag_timestamp;

                        DB::update(DB::raw(
                            "UPDATE Bags
                            SET
                                pallet_id = $new_pallet_id,
                                status_id = 3
                            WHERE
                                pallet_id = $old_pallet_id
                        "));
            
                        DB::update(DB::raw(
                            "UPDATE Pallets
                            SET
                                type_id = $type_id,
                                status_id = 3,
                                bag_count = $bag_count,
                                oldest_bag_timestamp = \"$oldest_bag_timestamp\"
                            WHERE
                                id = $new_pallet_id
                        "));
            
                        DB::update(DB::raw(
                            "UPDATE Pallets
                            SET
                                type_id = null,
                                bag_count = 0,
                                oldest_bag_timestamp = null
                            WHERE
                                id = $old_pallet_id
                        "));
                        
                        $new_pallet_id_qr = "P" . str_pad($new_pallet_id, 10, "0", STR_PAD_LEFT);
                        echo "Pallet {$new_pallet_id_qr} updated.";                                
                    } else {
                        $new_pallet_id_qr = "P" . str_pad($new_pallet_id, 10, "0", STR_PAD_LEFT);
                        echo "Pallet {$new_pallet_id_qr} is currently unavailable for this task.";
                    }
                } else {
                    echo "Device has no permission to perform such task.";
                }
            } else {
                echo "Device not found.";
            }    
        } else {
            echo "Invalid Device ID.";
        }
    }
}