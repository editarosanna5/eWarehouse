<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class StoragePickupController extends Controller {
    public function GetDisplay(Request $request) {
        $do_number = $request->get('do_number');
        $type_id = $request->get('type_id');
        $bag_amount = $request->get('bag_amount');
        
        $device_id = $request->get('device_id');
        $temp = explode("-", $device_id);
        $group_id = $temp[1];
        $device_id = $temp[2];

        $BagsPerPallet = WarehouseConfig::$BagsPerPallet;
        $DaysToExpiration = WarehouseConfig::$DaysToExpiration;

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
                    $ready_bags = DB::select(DB::raw(
                        "SELECT COUNT(*) AS bag_count
                        FROM Bags JOIN Pallets
                            ON Pallets.id = Bags.pallet_id
                        WHERE
                            Bags.type_id = $type_id
                            AND Pallets.status_id = 7
                            AND Bags.production_timestamp > date_sub(now(), interval $DaysToExpiration day)
                    "));

                    $ready_bags = $ready_bags[0]->bag_count;
            
                    $required_bag_count = $bag_amount - $ready_bags;
                    if ($required_bag_count > 0) {
                        $required_pallet_count = ceil($required_bag_count / $BagsPerPallet);
            
                        $pallet_options = DB::select(DB::raw(
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
                        "));
                
                        if (count($pallet_options) >= $required_pallet_count) {
                            echo "Available pallets:<br>";
                
                            foreach ($pallet_options as $pallet) {
                                DB::insert(DB::raw(
                                    "INSERT INTO PickupOptions (
                                        device_number,
                                        pallet_id
                                    )
                                    VALUES (
                                        $device_id,
                                        $pallet->id
                                    )
                                "));
                                echo "Pallet P-" . str_pad($pallet->id, 10, "0", STR_PAD_LEFT) . "; Row ROW-" . str_pad($pallet->row_number, 4, "0", STR_PAD_LEFT) . "; Column " . $pallet->column_number . "; Stack " . $pallet->stack_number . "<br>";
                            }

                            DB::insert(DB::raw(
                                "INSERT INTO LoadingStatus (
                                    device_number,
                                    required_bag_count
                                )
                                VALUES (
                                    $device_id,
                                    $bag_amount
                                )
                            "));
                
                            DB::insert(DB::raw(
                                "INSERT INTO PickupStatus (
                                    device_number,
                                    required_pallet_count
                                )
                                VALUES (
                                    $device_id,
                                    $required_pallet_count
                                )
                            "));
                        } else {
                            echo "Insufficient stock. Please try again in a moment.";
                        }
                    } else {
                        echo "Stocks available at loading zone.";
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

    public function PalletUpdate(Request $request) {
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
                
                if ($group_name == "FORKLIFT_PICKING") {
                    $pallet_id_check = DB::select(DB::raw(
                        "SELECT COUNT(*)
                        FROM PickupOptions
                        WHERE
                            device_number = $device_id
                            AND pallet_id = $pallet_id
                    "));

                    if ($pallet_id_check != null) {
                        $row_id = DB::select(DB::raw(
                            "SELECT row_number
                            FROM Pallets
                            WHERE
                                id = $pallet_id
                        "));
                        $row_id = $row_id[0]->row_number;

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
                                id = $row_id
                        "));

                        DB::delete(DB::raw(
                            "DELETE FROM PickupOptions
                            WHERE
                                pallet_id = $pallet_id
                        "));

                        $required_pallet_count = DB::select(DB::raw(
                            "SELECT required_pallet_count
                            FROM PickupStatus
                            WHERE
                                device_number = $device_id
                        "));

                        $required_pallet_count = $required_pallet_count[0]->required_pallet_count - 1;
                        
                        if ($required_pallet_count == 0) {
                            DB::delete(DB::raw(
                                "DELETE FROM PickupStatus
                                WHERE
                                    device_number = $device_id
                            "));

                            DB::delete(DB::raw(
                                "DELETE FROM PickupOptions
                                WHERE
                                    device_number = $device_id
                            "));
                            echo "Pallet pickups for current order complete.";
                        } else {
                            DB::update(DB::raw(
                                "UPDATE PickupStatus
                                SET
                                    required_pallet_count = $required_pallet_count
                                WHERE
                                    device_number = $device_id
                            "));
                            echo "Pallet P-" . str_pad($pallet_id, 10, "0", STR_PAD_LEFT) . " picked. {$required_pallet_count} pallet(s) left to pick.";
                        }
                    } else {
                        echo "Pallet is not available for the current order.";
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