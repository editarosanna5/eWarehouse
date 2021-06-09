<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class PickingController extends Controller {
    // menentukan opsi pickup
    public function PickupOptionsFetch ($type_id) {
        // fetch from config
        $DaysToExpiration = WarehouseConfig::$DaysToExpiration;
        $StacksPerColumn = WarehouseConfig::$StacksPerColumn;
        
        // opsi pengambilan
        $option_set_1 = array();    // prioritas 1: terluar jika tidak setipe dengan identity row
        $option_set_2 = array();    // prioritas 2: terluar
        $option_set_3 = array();    // prioritas 3: terdekat jika tidak setipe dengan identity row
        $option_set_4 = array();    // prioritas 4: terdekat
        $final_options = array();

        $row_counter = 0;
        // $options = array_fill_keys(array('row_id', 'pallet_id', 'column_number', 'stack_number', 'pallet_count'), '');
        $options = array();

        do {
            // echo "<br>" . count($options, 0) . "<br>";
            // print_r($options);
            $temp = DB::select(DB::raw(
                "SELECT
                    Rows.id AS row_id,
                    Pallets.id AS pallet_id,
                    Pallets.column_number AS column_number,
                    Pallets.stack_number AS stack_number,
                    Rows.pallet_count AS pallet_count
                FROM `Rows` JOIN Pallets
                    ON Rows.id = Pallets.row_number
                WHERE
                    Pallets.type_id = $type_id
                    AND Pallets.production_date > date_sub(now(), interval $DaysToExpiration day)
                    AND Rows.id > $row_counter
                ORDER BY
                    Pallets.column_number DESC,
                    Pallets.stack_number DESC
                LIMIT 1
            "));
            
            if ($temp != null) {
                array_push($options, $temp[0]);
                $row_counter = $temp[0]->row_id;
            }                
        } while ($temp != null);

        foreach ($options as $option) {
            $pallet_count_position = ($option->column_number - 1) * $StacksPerColumn + $option->stack_number;
            if ($pallet_count_position == $option->pallet_count) {
                // terluar
                $identity = DB::select(DB::raw(
                    "SELECT Pallets.type_id AS type_id
                    FROM `Rows` JOIN Pallets
                    WHERE
                        Rows.id = $option->row_id
                        AND Pallets.column_number = 1
                        AND Pallets.stack_number = 1
                "));
                if ($type_id == $identity[0]->type_id) {
                    // terluar dan setipe (prioritas 2)
                    array_push($option_set_2, $option->pallet_id);
                } else {
                    // terluar tidak setipe (prioritas 1)
                    array_push($option_set_1, $option->pallet_id);
                }
            } else {
                // terdekat / bukan terluar
                $identity = DB::select(DB::raw(
                    "SELECT Pallets.type_id AS type_id
                    FROM `Rows` JOIN Pallets
                    WHERE
                        Rows.id = $option->row_id
                        AND Pallets.column_number = 1
                        AND Pallets.stack_number = 1
                "));
                if ($type_id == $identity->type_id) {
                    // terdekat dan setipe (prioritas 4)
                    array_push($option_set_4, $option->pallet_id);
                } else {
                    // terdekat tidak setipe (prioritas 3)
                    array_push($option_set_3, $option->pallet_id);
                }
            }
        }

        if (count($option_set_1) != 0) {
            $final_options = $option_set_1;
        } elseif (count($option_set_2) != 0) {
            $final_options = $option_set_2;
        } elseif (count($option_set_3) != 0) {
            $final_options = $option_set_3;
        } elseif (count($option_set_4) != 0) {
            $final_options = $option_set_4;
        }

        return $final_options;
    }

    // menyimpan data order dari input ke database
    public function PickingUpdate (Request $request) {
        // tanggal hari ini
        $current_date = ComponentCheck::CurrentDate();
        
        $do_number = $request->get('do_number');
        $type_id = $request->get('type_id');
        $bag_count = $request->get('bag_count');
        $loading_line = $request->get('loading_line');

        // ubah format
        //     'input_1','input_2','input_3'
        // atau
        //     'input_1', 'input_2', 'input_3'
        // menjadi array
        $type_id = ComponentCheck::MultipleInputsToArray($type_id, true);
        $bag_count = ComponentCheck::MultipleInputsToArray($bag_count, true);

        DB::insert(DB::raw(
            "INSERT INTO OrderData (
                member_id,
                do_number,
                order_date
            ) VALUES (
                $loading_line,
                $do_number,
                $current_date
            )
        "));

        $delivery_id = DB::select(DB::raw(
            "SELECT id
            FROM OrderData
            WHERE
                do_number = $do_number
        "));

        $order_id = $delivery_id[0]->id;

        for ($i = 0; $i < count($type_id); $i++) {
            $type = $type_id[$i];
            $quantity = $bag_count[$i];
            $pallet_options = PickingController::PickupOptionsFetch($type);
            DB::insert(DB::raw(
                "INSERT INTO OrderDetails (
                    order_id,
                    type_id,
                    quantity
                ) VALUES (
                    $order_id,
                    $type,
                    $quantity
                )
            "));

            foreach($pallet_options as $pallet_option) {
                DB::insert(DB::raw(
                    "INSERT INTO PickupOptions(
                        id,
                        pallet_id
                    ) VALUES (
                        $order_id,
                        $pallet_option
                    )
                "));
            }
        }

        echo "Order successfully recorded.";
    }

    // memilih order yang akan dieksekusi
    public function PickingSelect (Request $request) {
        $order_id = ComponentCheck::OrderID($request->get('order_id'), 1);
        $member_id = ComponentCheck::DeviceID($request->get('device_id'), 3);

        if ($order_id != -1) {
            DB::update(DB::raw(
                "UPDATE OrderData
                SET
                    status_id = 2
                WHERE
                    id = $order_id
            "));

            $orderdetails = DB::select(DB::raw(
                "SELECT id
                FROM OrderDetails
                WHERE
                    order_id = $order_id
            "));

            foreach($orderdetails as $orderdetail) {
                $id = $orderdetail->id;

                DB::insert(DB::raw(
                    "INSERT INTO LoadingStatus (
                        id,
                        member_id
                    ) VALUES (
                        $id,
                        $member_id
                    )
                "));
            }

            echo "Order successfully taken.";
        } else {
            echo "Order busy.";
        }        
    }

    // verifikasi row id
    public function PickingLineUpdate (Request $request) {
        $device_id = $request->get('device_id');
        $row_id = $request->get('row_id');
        $member_id = ComponentCheck::DeviceID($device_id, 3);

        if ($member_id != -1) {
            $row_id_int = ComponentCheck::RowID($row_id);

            if ($row_id_int != -1) {
                $temp = DB::select(DB::raw(
                    "SELECT COUNT(*) AS found
                    FROM LoadingStatus
                        JOIN OrderDetails
                            ON LoadingStatus.id = OrderDetails.id
                        JOIN PickupOptions
                            ON OrderDetails.id = PickupOptions.id
                        JOIN Pallets
                            ON PickupOptions.pallet_id = Pallets.id
                    WHERE
                        LoadingStatus.member_id = $member_id
                        AND Pallets.row_number = $row_id_int
                "));

                $count = $temp[0]->found;

                if ($count > 0) {
                    echo "Row available for pickup. {$count} matches found.";
                } else {
                    echo "Row unavailable for pickup.";
                }
            }
        } else {
            echo "Unauthorized device.";
        }
    }

    // update status palet menjadi MOVING_TO_LOADING_ZONE ketika palet discan
    public function PickingMovingUpdate (Request $request) {
        $device_id = $request->get('device_id');
        $pallet_id = $request->get('pallet_id');
        $member_id = ComponentCheck::DeviceID($device_id, 3);

        if ($member_id != -1) {
            // verifikasi pallet id
            $pallet_id_int = ComponentCheck::PalletID($pallet_id, 4);

            if ($pallet_id_int != -1) {
                // pastikan member_id device dan pallet_id sesuai
                $temp = DB::select(DB::raw(
                    "SELECT
                        Pallets.bag_count AS bag_count,
                        Pallets.row_number AS row_id,
                        OrderDetails.id AS id
                    FROM Pallets
                        JOIN PickupOptions
                            ON Pallets.id = PickupOptions.pallet_id
                        JOIN OrderDetails
                            ON PickupOptions.id = OrderDetails.id
                        JOIN OrderData
                            ON OrderDetails.order_id = OrderData.id
                    WHERE
                        OrderData.member_id = $member_id
                        AND PickupOptions.pallet_id = $pallet_id_int
                "));

                if ($temp != null) {
                    $bag_count = $temp[0]->bag_count;
                    $id = $temp[0]->id;
                    $row_id = $temp[0]->row_id;
                    
                    // update jumlah karung yang sudah siap
                    DB::update(DB::raw(
                        "UPDATE LoadingStatus
                        SET
                            available_bag_count = available_bag_count + $bag_count
                        WHERE
                            id = $id
                            AND member_id = $member_id
                    "));

                    // update status palet dari ON_STORAGE menjadi MOVING_TO_LOADING_ZONE
                    DB::update(DB::raw(
                        "UPDATE Pallets
                        SET
                            status_id = 5,
                            row_number = NULL,
                            column_number = NULL,
                            stack_number = NULL
                        WHERE
                            id = $pallet_id_int
                    "));

                    // update data row
                    DB::update(DB::raw(
                        "UPDATE `Rows`
                        SET
                            pallet_count = pallet_count - 1
                        WHERE
                            id = $row_id
                    "));

                    // hapus semua opsi penyimpanan dengan pallet_id = 12
                    DB::delete(DB::raw(
                        "DELETE FROM PickupOptions
                        WHERE
                            pallet_id = $pallet_id_int
                    "));

                    echo "Pallet successfully taken. Moving to loading zone.";
                } else {
                    echo "Pallet not in option.";
                }
            } else {
                echo "Invalid pallet ID.";
            }
        } else {
            echo "Unauthorized device.";
        }
    }

    // update status palet menjadi WAITING_TO_BE_LOADED ketika palet sampai dan discan di loading zone
    public function PickingArrivalUpdate ($pallet_id_int) {
        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = 6
            WHERE
                id = $pallet_id_int    
        "));

        echo "Pallet arrived at loading zone. Waiting to be loaded.";
    }
    
    // update status palet menjadi LOADING
    public function PickingLoadingSelect ($pallet_id_int, $member_id) {
        DB::update(DB::raw(
            "UPDATE Pallets
            SET
                status_id = 7
            WHERE
                id = $pallet_id_int                        
        "));

        $temp = DB::select(DB::raw(
            "SELECT id as order_id
            FROM OrderData
            WHERE
                member_id = $member_id
                AND status_id = 2
        "));

        $order_id = $temp[0]->order_id;

        $temp = DB::select(DB::raw(
            "SELECT
                type_id,
                production_date
            FROM Pallets
            WHERE
                id = $pallet_id_int
        "));

        $type_id = $temp[0]->type_id;
        $production_date = '"' . $temp[0]->production_date . '"';

        DB::insert(DB::raw(
            "INSERT INTO DeliveryDetails (
                order_id,
                pallet_id,
                type_id,
                production_date,
                picking_line
            ) VALUES (
                $order_id,
                $pallet_id_int,
                $type_id,
                $production_date,
                $member_id
            )
        "));

        echo "Loading pallet {$pallet_id_int}.<br>";
    }

    // handheld scanner loading zone
    public function PickingPalletUpdate (Request $request) {
        $device_id = $request->get('device_id');
        $pallet_id = $request->get('pallet_id');
        $member_id = ComponentCheck::DeviceID($device_id, 4);

        if ($member_id != -1) {
            // periksa status pallet
            $pallet_id_int = ComponentCheck::PalletID($pallet_id, 5);

            if ($pallet_id_int != -1) {
            // palet memasuki area loading
                // status palet awal = MOVING_TO_LOADING_ZONE (5)
                // status palet akhir = WAITING_TO_BE_LOADED (6)
                
                PickingController::PickingArrivalUpdate($pallet_id_int);

            } else {
                $pallet_id_int = ComponentCheck::PalletID($pallet_id, 6);

                if ($pallet_id_int != -1) {
                // palet yang akan diloading
                    // status palet awal = WAITING_TO_BE_LOADED (6)
                    // status palet akhir = LOADING (7)

                    PickingController::PickingLoadingSelect ($pallet_id_int, $member_id);

                } else {
                    echo "Pallet unavailable for this operation.";
                }
            }
        } else {
            echo "Unauthorized device.";
        }
    }

    // counter loading karung
    public function PickingBagUpdate (Request $request) {
        $device_id = $request->get('device_id');
        $member_id = ComponentCheck::DeviceID($device_id, 5);

        if ($member_id != -1) {
            $temp = DB::select(DB:raw(
                "SELECT
                    Pallets.id AS pallet_id,
                    OrderDetails.id AS order_detail_id
                FROM Pallets
                    JOIN OrderDetails
                        ON Pallets.type_id = OrderDetails.type_id
                    JOIN OrderData
                        ON OrderDetails.order_id = OrderData
                WHERE
                    OrderData.member_id = $member_id
                    AND OrderData.status_id = 2
                    AND Pallets.status_id = 7
            "));

            $pallet_id = $temp[0]->pallet_id;
            $order_detail_id = $temp[0]->order_detail_id;

            // periksa jumlah karung tersisa pada palet
            $temp = DB::select(DB::raw(
                "SELECT pallet_bag_count
                FROM Pallets
                WHERE
                    id = $pallet_id
            "));

            $pallet_bag_count = $temp[0]->pallet_bag_count;

            if ($pallet_bag_count == 1) {   // sisa karung = 1
                // update data pallet
                    // bag_count =- 1 (= 0)
                    // status_id = 1 (EMPTY)
                DB::update(DB::raw(
                    "UPDATE Pallets
                    SET
                        po_number = NULL,
                        type_id = NULL,
                        status_id = 1,
                        bag_count = bag_count - 1,
                        production_date = NULL
                "));
            } else {    // sisa karung > 1
                // periksa jumlah loaded_bag_count
                $temp = DB::select(DB::raw(
                    "SELECT loaded_bag_count
                    FROM LoadingStatus
                    WHERE
                        
                "));
            }
            
            // update jumlah karung tersisa pada pallet
            DB::update(DB::raw(
                "UPDATE Pallets
                SET
                    
                WHERE
                    id = $pallet_id
            "));

            // loaded_bag_count terpenuhi
            //     pallet kosong
            //     pallet belum kosong
            // loaded_bag_count belum terpenuhi
            //     pallet kosong
            //     pallet belum kosong
        } else {
            echo "Unauthorized device.";
        }
    }
}