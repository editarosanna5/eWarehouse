<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class ComponentCheck {

    public static function DeviceID ($device_id, $required_group_id) {
        $temp = explode("-", $device_id);
        $group_id = $temp[0];
        $device_number = $temp[1];

        if ($group_id == $required_group_id)
            return $device_number;
        else
            return -1;
    }

    public static function ProductionDate ($production_date) {
        date_default_timezone_set('Asia/Jakarta');

        $temp = (strtotime($today)-strtotime($date))/(60*60*24);

        if ($temp >= 0)
            return $temp;
        else
            return -1;
    }

    public static function LineStoringID ($line_id) {
        $temp = explode("-", $line_id);
        
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
                "SELECT COUNT(*)
                FROM Pallets
                WHERE
                    pallet_id = $pallet_id_int
                    AND status_id = $required_pallet_status
            "));
            
            if ($temp != null)
                return $pallet_id_int;
            else
                return -1;
        }
    }
}