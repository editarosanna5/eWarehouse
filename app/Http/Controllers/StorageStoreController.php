<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;
require '../../config/warehouse.php';

class StorageStoreController extends Controller {

    // urutan prioritas:
    // 1. yang sejenis dan dalam satu kelompok waktu (3 hari)
    // 2. yang kosong
    // 3. yang sejenis

    // catatan:
    // - umur pada row = umur palet tertua
    
    public function GetDisplay($pallet_id) {
        $type_id = DB::select(DB::raw(
            "SELECT type_id
                FROM Pallets
                WHERE
                    pallet_id = $pallet_id
        "));
        $row_numbers_1 = DB::select(DB::raw(
            "SELECT DISTINCT Pallets.row_number
                FROM `Rows` JOIN Pallets
                    ON Rows.id = Pallets.row_number
                WHERE
                    Rows.pallet_count < Warehouse::StacksPerColumn()
                AND
                    Pallets.type_id = $type_id[0]->type_id
                AND
                    Pallets.oldest_bag_timestamp > date_sub(now(), interval Warehouse::$TimeGrouping day)
            "));
        
        if ($row_numbers_1 == null) {
            $row_numbers_2 = DB::select(DB::raw(
                "SELECT id
                FROM `Rows`
                WHERE
                    Rows.pallet_count < Warehouse::StacksPerColumn()
            "));

            if ($row_numbers_2 == null){
                $row_numbers_3 = DB::select(DB::raw(
                    "SELECT DISTINCT Pallets.row_number
                        FROM `Rows` JOIN Pallets
                            ON Rows.id = Pallets.row_number
                        WHERE
                            Rows.pallet_count < Warehouse::StacksPerColumn()
                        AND
                            Pallets.type_id = $type_id[0]->type_id
                "));
            }
        }
    }

    public function MovingToStorageUpdate(Request $request) {
        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = 3
            WHERE
                pallet_id = $request->pallet_id
        "));
    }

    public function OnStorage (Requst $request) {
        $row_data = select(DB::raw(
            "SELECT column_number, stack_number
            FROM Pallets
            WHERE
                row_number = $request->row_id
            ORDER BY
                column_number DESC,
                stack_number DESC
            LIMIT 1
        "));
        
        DB::update(DB::raw(
            "UPDATE `Rows`
            SET
                pallet_count += 1
        "));
        
        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = 4,
                row_number = $request->row_id,
                column_number = $row_data[0]->column_number,
                stack_number = $row_data[0]->stack_number
            WHERE
                id = $request->pallet_id
        "));
    }
}