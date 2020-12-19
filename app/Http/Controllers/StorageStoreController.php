<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class StorageStoreController extends Controller {    
    public function MovingToStorageUpdate(Request $request) {
        $pallet_id = $request->get('pallet_id');
        $temp = explode("-", $pallet_id);
        $pallet_id = intval($temp[1]);

        $device_id = $request->get('device_id');
        $temp = explode("-", $device_id);
        $group_id = $temp[1];
        $device_id = $temp[2];

        $PalletsPerRow = WarehouseConfig::PalletsPerRow();

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
                
                if ($group_name == "FORKLIFT_STORING") {
                    $pallet_id_check = DB::select(DB::raw(
                        "SELECT type_id
                        FROM Pallets
                        WHERE
                            id = $pallet_id
                            AND status_id = 3
                    "));

                    $type_id = $pallet_id_check[0]->type_id;

                    if ($pallet_id_check != null) {
                        $row_numbers = DB::select(DB::raw(
                            "SELECT
                                DISTINCT Pallets.row_number AS id
                            FROM `Rows` JOIN Pallets
                                ON Rows.id = Pallets.row_number
                            WHERE
                                Rows.pallet_count < $PalletsPerRow
                            AND
                                Pallets.type_id = $type_id
                        "));

                        if ($row_numbers == null) {
                            $row_numbers = DB::select(DB::raw(
                                "SELECT id
                                FROM `Rows`
                                WHERE
                                    pallet_count = 0
                            ")); 
                        }

                        if ($row_numbers == null) {
                            echo "Currently no available rows. Please try again in a moment.";
                        } else {
                            echo "Available rows:<br>";
                            foreach ($row_numbers as $row) {
                                DB::INSERT(DB::raw(
                                    "INSERT INTO StorageOptions (
                                        device_number,
                                        row_id,
                                        pallet_id
                                    )
                                    VALUES (
                                        $device_id,
                                        $row->id,
                                        $pallet_id
                                    )
                                "));
                                echo "ROW-" . str_pad($row->id, 4, "0", STR_PAD_LEFT) . "<br>";
                            }
                            
                            DB::update(DB::raw(
                                "UPDATE Pallets
                                SET
                                    status_id = 4
                                WHERE
                                    id = $pallet_id
                            "));
                        }

                    } else {
                        $pallet_id_qr = "P" . str_pad($new_pallet_id, 10, "0", STR_PAD_LEFT);
                        echo "Pallet {$pallet_id_qr} is currently unavailable for this task.";
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

    public function OnStorageUpdate (Request $request) {
        $row_id = $request->row_id;
        $temp = explode("-", $row_id);
        $row_id = intval($temp[1]);
        
        $device_id = $request->device_id;
        $temp = explode("-", $device_id);
        $group_id = $temp[1];
        $device_id =$temp[2];

        $PalletsPerRow = WarehouseConfig::PalletsPerRow();

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
                
                if ($group_name == "FORKLIFT_STORING") {
                    $pallet_id = DB::select(DB::raw(
                        "SELECT pallet_id
                        FROM StorageOptions
                        WHERE
                            device_number = $device_id
                            AND row_id = $row_id
                    "));

                    if ($pallet_id != null) {
                        $pallet_id = $pallet_id[0]->pallet_id;
                        $row_data = DB::select(DB::raw(
                            "SELECT
                                column_number,
                                stack_number
                            FROM Pallets
                            WHERE
                                row_number = $row_id
                            ORDER BY
                                column_number DESC,
                                stack_number DESC
                            LIMIT 1
                        "));
                        
                        if ($row_data == null) {
                            $column_number = 1;
                            $stack_number = 1;
                        } else {
                            $column_number = $row_data[0]->column_number;
                            $stack_number = $row_data[0]->stack_number;
                
                            if ($stack_number == 3) {
                                $stack_number = 1;
                                $column_number += 1;
                            } else {
                                $stack_number += 1;
                            }
                        }

                        DB::update(DB::raw(
                            "UPDATE `Rows`
                            SET
                                pallet_count = pallet_count + 1
                            WHERE
                                id = $row_id
                        "));
                        
                        DB::update(DB::raw(
                            "UPDATE Pallets
                            SET
                                status_id = 5,
                                row_number = $row_id,
                                column_number = $column_number,
                                stack_number = $stack_number
                            WHERE
                                id = $pallet_id
                        "));

                        DB::delete(DB::raw(
                            "DELETE FROM StorageOptions
                            WHERE
                                device_number = $device_id
                        "));

                        $type_id = DB::select(DB::raw(
                            "SELECT type_id
                            FROM Pallets
                            WHERE
                                id = $pallet_id
                        "));
                        $type_id = $type_id[0]->type_id;

                        $is_option_used = DB::select(DB::raw(
                            "SELECT COUNT(*)
                            FROM StorageOptions JOIN Pallets
                                ON StorageOptions.pallet_id = Pallets.id
                            WHERE
                                StorageOptions.row_id = $row_id
                                AND Pallets.type_id <> $type_id
                        "));

                        $pallet_count = DB::select(DB::raw(
                            "SELECT pallet_count
                            FROM `Rows`
                            WHERE
                                id = $row_id
                        "));

                        $pallet_count = $pallet_count[0]->pallet_count;

                        if ($pallet_count >= $PalletsPerRow || $is_option_used != null) {
                            DB::delete(DB::raw(
                                "DELETE FROM StorageOptions
                                WHERE
                                    row_id = $row_id
                            "));
                        }
                    } else {
                        echo "Row is not available for the current pallet.";
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