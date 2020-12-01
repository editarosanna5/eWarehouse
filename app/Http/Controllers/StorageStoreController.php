<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class StorageStoreController extends Controller {

    // urutan prioritas:
    // 1. yang sejenis dan dalam satu kelompok waktu (3 hari)
    // 2. yang kosong
    // 3. yang sejenis

    // catatan:
    // - umur pada row = umur palet tertua
    
    public function GetDisplay($pallet_id) {
        $TimeGrouping = WarehouseConfig::$TimeGrouping;
        $PalletsPerRow = WarehouseConfig::PalletsPerRow();
        
        echo "OPSI LINE PENYIMPANAN<br>";
        
        $pallet_data = DB::select(DB::raw(
            "SELECT type_id, oldest_bag_timestamp
                FROM Pallets
                WHERE
                    id = $pallet_id
        "));

        $type_id = $pallet_data[0]->type_id;
        $oldest_bag_timestamp = $pallet_data[0]->oldest_bag_timestamp;

        $row_numbers_1 = DB::select(DB::raw(
            "SELECT
                DISTINCT Pallets.row_number AS id
            FROM `Rows` JOIN Pallets
                ON Rows.id = Pallets.row_number
            WHERE
                Rows.pallet_count < $PalletsPerRow
            AND
                Pallets.type_id = $type_id
            AND
                Pallets.oldest_bag_timestamp > date_sub(\"$oldest_bag_timestamp\", interval $TimeGrouping day)
        "));
        
        if ($row_numbers_1 == null) {
            $row_numbers_2 = DB::select(DB::raw(
                "SELECT
                    id
                FROM `Rows`
                WHERE
                    Rows.pallet_count = 0
            "));
            
            echo "<br>Prioritas 2:<br>";
            foreach ($row_numbers_2 as $row) {
                echo "Row " . $row->id . "<br>";
            }

            if ($row_numbers_2 == null){
                $row_numbers_3 = DB::select(DB::raw(
                    "SELECT
                        DISTINCT Pallets.row_number
                    FROM `Rows` JOIN Pallets
                        ON Rows.id = Pallets.row_number
                    WHERE
                        Rows.pallet_count < $PalletsPerRow
                    AND
                        Pallets.type_id = $type_id
                "));

                echo "<br>ROW 3";
                print_r($row_numbers_3);

                echo "<br>Prioritas 3:<br>";
                foreach ($row_numbers_3 as $row) {
                    echo "Row " . $row->id . "<br>";
                }
            }
        } else {
            echo "<br>Prioritas 1:<br>";
            foreach ($row_numbers_1 as $row) {
                echo "Row " . $row->id . "<br>";
            }
        }
    }

    public function MovingToStorageUpdate(Request $request) {
        $pallet_id = $request->pallet_id;
        
        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = 4
            WHERE
                id = $pallet_id
        "));
    }

    public function OnStorageUpdate (Request $request) {
        $row_id = $request->row_id;
        $pallet_id = $request->pallet_id;

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
    }
}