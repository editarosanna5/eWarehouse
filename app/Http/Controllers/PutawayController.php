<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class PutawayController extends Controller {
    // mengambil semua opsi penyimpanan
    // menyimpan opsi penyimpanan ke database
    public function PutawayMovingFetch ($pallet_id_int) {
        // import nilai max pallet per row
        $pallets_per_row = WarehouseConfig::PalletsPerRow();

        $pallet_data = DB::select(DB::raw(
            "SELECT
                type_id,
                production_date
            FROM
                Pallets
            WHERE
                id = $pallet_id_int;
        "));

        $type_id = $pallet_data[0]->type_id;
        $production_date = $pallet_data[0]->production_date;
        
        $options = DB::select(DB::raw(
            "SELECT
                DISTINCT Rows.id AS id,
                Rows.pallet_count AS pallet_count
            FROM `Rows` JOIN Pallets
                ON Rows.id = Pallets.row_number
            WHERE                        
                Rows.pallet_count < $pallets_per_row
                AND Pallets.production_date = $production_date
                AND Pallets.type_id = $type_id
                -- pallet pertama pada line penyimpanan dianggap sebagai identity
                AND Pallets.row_number = 1
                AND Pallets.column_number = 1
                AND Pallets.stack_number = 1
            ORDER BY
                Rows.pallet_count DESC
        "));

        $option_set_1 = array();
        $option_set_2 = array();
        $option_set_3 = array();
        $option_set_4 = array();
        $option_set_5 = array();
        $option_set_6 = array();
        $final_options = array();

        foreach ($options as $option) {
            // prioritas 1: cari row dengan palet bertipe sama semua
            $option_data = DB::select(DB::raw(
                "SELECT COUNT(*) as pallet_count
                FROM Pallets
                WHERE
                    row_number = $option->id
                    AND type_id = $type_id
            "));

            if ($option_data[0]->pallet_count == $option->pallet_count) {
                array_push($option_set_1, $option->id);
            }
        }
        
        if (count($option_set_1) == 0) {
            // prioritas 2: cari row dengan:
            //     1. identity type sama dengan palet yang akan disimpan
            //     2. jumlah kolom terisi penuh = (jumlah kolom - 1)
            //     3. tidak semua palet dalam kolom bertipe identity type
            foreach ($options as $option) {
                if ($option->pallet_count >= $pallets_per_row - WarehouseConfig::$StacksPerColumn) {
                    array_push($option_set_2, $option->id);
                } else
                    break;
            }

            // untuk n: 2 <= n <= 6
            //     prioritas n = prioritas n + row kosong
            $empty_rows = DB::select(DB::raw(
                "SELECT id
                FROM `Rows`
                WHERE
                    pallet_count = 0
            "));

            if (count($option_set_2) == 0) {
                $options = DB::select(DB::raw(
                    "SELECT
                        DISTINCT Rows.id AS id,
                        Rows.pallet_count AS pallet_count,
                        Pallets.type_id AS type_id
                    FROM `Rows` JOIN Pallets
                        ON Rows.id = Pallets.row_number
                    WHERE                        
                        Rows.pallet_count < $pallets_per_row
                        AND Pallets.production_date = $production_date
                        -- pallet pertama pada line penyimpanan dianggap sebagai identity
                        AND Pallets.row_number = 1
                        AND Pallets.column_number = 1
                        AND Pallets.stack_number = 1
                    ORDER BY
                        Rows.pallet_count DESC
                "));
                
                $many_rows = array();
                foreach ($options as $option_search_1) {
                    $count = 0;
                    foreach ($options as $option_search_2) {
                        if ($option_search_1->id == $option_search_2->id) {
                            $count++;
                        }
                    }
                    if ($count > 1) {
                        array_push($many_rows, $option_search_1->id);
                    }                        
                }

                foreach ($options as $option) {
                    if ($option->pallet_count >= $pallets_per_row - WarehouseConfig::$StacksPerColumn) {
                        if (array_search($option->id, $many_rows)) {
                            // prioritas 3:
                            //     lebih dari 1 row utk tipe tersebut
                            //     terisi = (jumlah kolom - 1)
                            array_push($option_set_3, $option->id);
                        } else {
                            // prioritas 4:
                            //     hanya 1 row utk tipe tersebut
                            //     terisi = (jumlah kolom - 1)
                            array_push($option_set_4, $option->id);
                        }
                    } else {
                        if (array_search($option->id, $many_rows)) {
                            // prioritas 5:
                            //     lebih dari 1 row utk tipe tersebut
                            //     terisi < (jumlah kolom - 1)
                            array_push($option_set_5, $option->id);
                        } else {
                            // prioritas 6:
                            //     hanya 1 row utk tipe tersebut
                            //     terisi < (jumlah kolom - 1)
                            array_push($option_set_6, $option->id);
                        }
                    }
                }

                if (count($option_set_3) != 0) {
                    $final_options = $option_set_3;
                } elseif (count($option_set_4) != 0) {
                    $final_options = $option_set_4;
                } elseif (count($option_set_5) != 0) {
                    $final_options = $option_set_5;
                } else {
                    $final_options = $option_set_6;
                }

                foreach ($empty_rows as $empty_row) {
                    array_push ($final_options, $empty_row->id);
                }

            } else {
                $final_options = $option_set_2;
                foreach ($empty_rows as $empty_row) {
                    array_push($final_options, $empty_row->id);
                }
            }
        } else {
            $final_options = $option_set_1;
        }

        return $final_options;
    }

    public function PutawayMovingUpdate ($device_id, $pallet_id) {
        // verifikasi device id
        $member_id  = ComponenetCheck::DeviceID($device_id, 2);

        if ($member_id != -1) {
            // verifikasi pallet id
            $pallet_id_int = ComponentCheck::PalletID($pallet_id, 2);

            if ($pallet_id_int != -1) {
                $final_options = PutawayController::PutawayMovingFetch($pallet_id_int);

                if (count($final_options) != 0) {
                    foreach($final_options as $final_option) {
                        DB::insert(DB::raw(
                            "INSERT INTO StorageOptions (
                                member_id,
                                row_id,
                                pallet_id
                            ) VALUES (
                                $member_id,
                                $final_option,
                                $pallet_id_int
                            )
                        "));

                        echo "Row {$final_option}<br>";
                    }
        
                    // update status palet menjadi MOVING_TO_STORAGE_ZONE (3)
                    DB::update(DB::raw(
                        "UPDATE Pallets
                        SET
                            status_id = 3
                        WHERE
                            if = $pallet_id_int
                    "));
                } else {
                    echo "No available rows.<br>";
                }
            } else {
                echo "Pallet {$pallet_id} invalid or unavailable for put away.<br>";
            }
        } else {
            echo "Unauthorized device.<br>";
        }
    }

    public function PutawayArrivalUpdate ($device_id, $row_id) {
        // verifikasi device id
        $member_id = ComponentCheck::DeviceID($device_id, 2);

        if ($member_id != -1) {
            // verifikasi row id
            $row_id_int = ComponentCheck::RowID($row_id);

            if ($row_id_int != -1) {
                $temp = DB::select(DB::raw(
                    "SELECT COUNT(*)
                    FROM StorageOptions
                    WHERE
                        row_id = $row_id_int
                "));

                if ($temp != null) {
                    $row_data = DB::select(DB::raw(
                        "SELECT pallet_count
                        FROM `Rows`
                        WHERE
                            id = $row_id_int
                    "));

                    // data row baru
                    $pallet_count = $row_data[0]->pallet_count + 1;
                    
                    // data pallet baru
                    $status_id = 4; // update status palet menjadi ON_STORAGE (4)
                    $column_number = $pallet_count / WarehouseConfig::$StacksPerColumn;
                    $stack_number = $pallet_count % WarehouseConfig::$StacksPerColumn;

                    DB::update(DB::raw(
                        "UPDATE `Rows`
                        SET
                            pallet_count = $pallet_count
                    "));
                    
                    DB::update(DB::raw(
                        "UPDATE Pallets
                        SET
                            -- update status palet menjadi ON_STORAGE (4)
                            status_id = 4,
                            row_number = $row_id_int,
                            column_number = $column_number,
                            stack_number = $stack_number
                    "));
                }
            } else {
                echo "Invalid row ID.<br>";
            }
        } else {
            echo "Unauthorized device.<br>";
        }
    }
}