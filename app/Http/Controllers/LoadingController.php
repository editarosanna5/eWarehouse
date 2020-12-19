<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class LoadingController extends Controller {
    public function PalletReadyUpdate(Request $request){
        $pallet_id = $request->pallet_id;
        $temp = explode("-", $pallet_id);
        $pallet_id = intval($temp[1]);
        
        $device_id = $request->get('device_id');
        $temp = explode("-", $device_id);
        $group_id = $temp[1];
        $device_id = $temp[2];

        if ($temp[0] == "DEV" && preg_match('/[0-9]{2}/', $group_id) && preg_match('/[0-9]{3}/', $device_id)) {
            $group_id = intval($group_id);
            $device_id = intval($device_id);
            
            $device_check = DB::select(DB::raw(
                "SELECT COUNT(*)
                FROM Devices
                WHERE
                    group_id = $group_id
                    AND id = $device_id
            "));

            if ($device_check != null) {
                $group_name = DB::select(DB::raw(
                    "SELECT group_name
                    FROM DeviceGroups
                    WHERE
                        id = $group_id
                "));

                $group_name = $group_name[0]->group_name;
                
                if ($group_name == "LOADING_ZONE") {
                    DB::update(DB::raw(
                        "UPDATE Pallets
                        SET
                            status_id = 7
                        WHERE
                            id = $pallet_id
                    "));
                    echo "Pallet P-" . str_pad($pallet_id, 10, "0", STR_PAD_LEFT) . " status updated to \"READY_LOADING_ZONE\"";
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

    public function OnLoadingUpdate(Request $request) {
        $bag_id = $request->bag_id;
        $temp = explode("-", $bag_id);
        $bag_id = intval($temp[1]);
        
        $device_id = $request->get('device_id');
        $temp = explode("-", $device_id);
        $group_id = $temp[1];
        $device_id = $temp[2];

        if ($temp[0] == "DEV" && preg_match('/[0-9]{2}/', $group_id) && preg_match('/[0-9]{3}/', $device_id)) {
            $group_id = intval($group_id);
            $device_id = intval($device_id);

            $device_check = DB::select(DB::raw(
                "SELECT COUNT(*)
                FROM Devices
                WHERE
                    group_id = $group_id
                    AND id = $device_id
            "));

            if ($device_check != null) {
                $group_name = DB::select(DB::raw(
                    "SELECT group_name
                    FROM DeviceGroups
                    WHERE
                        id = $group_id
                "));

                $group_name = $group_name[0]->group_name;
                
                if ($group_name == "LOADING_ZONE") {
                    $bag_id_check = DB::select(DB::raw(
                        "SELECT
                            Bags.pallet_id AS pallet_id,
                            Pallets.status_id AS pallet_status,
                            Pallets.bag_count AS bag_count
                        FROM Bags JOIN Pallets
                            ON Bags.pallet_id = Pallets.id
                        WHERE
                            Bags.id = $bag_id
                    "));
                    $pallet_id = $bag_id_check[0]->pallet_id;
                    $pallet_status = $bag_id_check[0]->pallet_status;
                    $bag_count = $bag_id_check[0]->bag_count - 1;

                    if ($bag_id_check != null) {
                        if ($pallet_status == 7) {
                            DB::update(DB::raw(
                                "UPDATE Pallets
                                SET
                                    status_id = 8
                                WHERE
                                    id = $pallet_id
                            "));
                        }
                        if ($bag_count > 0) {
                            DB::update(DB::raw(
                                "UPDATE Pallets
                                SET
                                    bag_count = $bag_count
                                WHERE
                                    id = $pallet_id
                            "));
                        } else {
                            DB::update(DB::raw(
                                "UPDATE Pallets
                                SET
                                    type_id = null,
                                    status_id = 2,
                                    bag_count = $bag_count,
                                    oldest_bag_timestamp = null
                                WHERE
                                    id = $pallet_id
                            "));
                        }                            
                        DB::update(DB::raw(
                            "UPDATE Bags
                            SET
                                pallet_id = null,
                                status_id = 4
                            WHERE
                                id = $bag_id
                        "));

                        $required_bag_count = DB::select(DB::raw(
                            "SELECT required_bag_count
                            FROM LoadingStatus
                            WHERE
                                device_number = $device_id
                        "));
                        $required_bag_count = $required_bag_count[0]->required_bag_count - 1;
                        
                        if ($required_bag_count > 0) {
                            DB::update(DB::raw(
                                "UPDATE LoadingStatus
                                SET
                                    required_bag_count = $required_bag_count
                                WHERE
                                    device_number = $device_id
                            "));
                            echo "Bag B-" . str_pad($bag_id, 10, "0", STR_PAD_LEFT) . " loaded.";
                        } else {
                            DB::delete(DB::raw(
                                "DELETE FROM LoadingStatus
                                WHERE
                                    device_number = $device_id
                            "));

                            if ($bag_count > 0) {
                                DB::update(DB::raw(
                                    "UPDATE Pallets
                                    SET
                                        status_id = 7
                                    WHERE
                                        id = $pallet_id
                                "));
                            }
                            echo "Bag loading complete.";
                        }
                    } else {
                        echo "Invalid Bag ID. Please try again.";
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