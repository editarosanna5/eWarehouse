<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class ComponentCheck {
    public static function CurrentDate () {
        date_default_timezone_set('Asia/Jakarta');
        
        return '"' . date('Y-m-d') . '"';
    }

    public static function DeviceID ($device_id, $required_group_id) {
        $temp = explode("-", $device_id);
        $group_id = intval($temp[0]);
        $device_number = intval($temp[1]);

        if ($group_id == $required_group_id)
            return $device_number;
        else
            return -1;
    }

    public static function ProductionDate ($production_date) {
        $temp = (strtotime(ComponentCheck::CurrentDate())-strtotime($production_date))/(60*60*24);

        if ($temp >= 0)
            return $temp;
        else
            return -1;
    }

    public static function RowID ($row_id) {
        $temp = explode("-", $row_id);
        
        if ($temp[0] == "L") {
            if (preg_match('/[0-9]{4}/', $temp[1]))
                return intval($temp[1]);
        }
        
        return -1;
    }

    public static function PalletID ($pallet_id, $required_pallet_status) {
        $temp = explode("-", $pallet_id);
        
        if ($temp[0] == "P" && preg_match('/[0-9]{10}/', $temp[1])) {
            $pallet_id_int = intval($temp[1]);
            
            $temp = DB::select(DB::raw(
                "SELECT COUNT(*) AS n
                FROM Pallets
                WHERE
                    id = $pallet_id_int
                    AND status_id = $required_pallet_status
            "));
            
            if ($temp[0]->n != null)
                return $pallet_id_int;
            else
                return -1;
        }
    }

    public static function MultipleInputsToArray ($input, $toInt) {
        $input = str_replace(' ', '', $input);
        $temp = explode("','", $input);
        
        foreach ($temp as &$item) {
            $item = str_replace("'", "", $item);
            
            if ($toInt)
                $item = intval($item);
        }

        return $temp;
    }

    public static function OrderID ($order_id, $required_order_status) {       
        $temp = DB::select(DB::raw(
            "SELECT COUNT(*) AS n
            FROM OrderData
            WHERE
                id = $order_id
                AND status_id = $required_order_status
        "));
        
        if ($temp[0]->n != 0)
            return $order_id;
        else
            return -1;
    }
}