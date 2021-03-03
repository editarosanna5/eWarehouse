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

        $options = DB::select(DB::raw(
            "SELECT    
                DISTINCT Rows.id AS row_id,
                Pallets.id AS pallet_id,
                Pallets.column_number AS column_number,
                Pallets.stack_number AS stack_number,
                Rows.pallet_count AS pallet_count,
            OVER (
                ORDER BY
                    Pallets.column_number DESC,
                    Pallets.stack_number DESC
            ) FROM `Rows` JOIN Pallets
                ON Rows.id = Pallets.row_number
            WHERE
                Pallets.type_id = $type
                AND Pallets.production_date > date_sub(now(), interval $DaysToExpiration day)
        "));

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
                if ($type == $identity->type_id) {
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
                if ($type == $identity->type_id) {
                    // terdekat dan setipe (prioritas 4)
                    array_push($option_set_4, $option->pallet_id);
                } else {
                    // terdekat tidak setipe (prioritas 3)
                    array_push($option_set_3, $option->pallet_id);
                }
            }
        }
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
                order_date,
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
        }
    }

    // update status palet menjadi MOVING_TO_LOADING_ZONE ketika palet discan
    public function PickingMovingUpdate ($member_id) {
        $member_id = ComponentCheck::DeviceID($member_id, 3);

        if ($member_id != -1) {
            // verifikasi pallet id
            $pallet_id_int = ComponentCheck::PalletID($pallet_id, 4);

            if ($pallet_id_int != -1) {
                $delivery_data = DB::select(DB::raw(
                    "SELECT PickupOptions.pallet_id
                    
                "));
            }
        }
    }

    public function PickingArrivalUpdate () {
        
    }

    public function PickingLoadingUpdate () {
        
    }
}