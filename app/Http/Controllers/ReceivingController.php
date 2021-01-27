<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class ReceivingController extends Controller {
    public function ReceivingUpdate () {
        // simpan data packaging sementara di database
        $po_number = $request->get('po_number');
        $type_id = $request->get('type_id');
        $bag_count = $request->get('bag_count');
        $production_date = $request->get('production_date');
        $packaging_line = $request->get('packaging_line');

        // periksa apakah terdapat aktivitas di packaging line
        $temp = DB::select(DB::raw(
            "SELECT COUNT(*)
            FROM ProductionData
            WHERE
                member_id = $packaging_line
        "));

        if ($temp == null) {
            // tidak ada aktivitas di packaging line
            DB::insert(DB::raw(
                "INSERT INTO ProductionData (
                    member_id,
                    po_number,
                    type_id,
                    bag_count,
                    production_date
                )
                VALUES (
                    $packaging_line,
                    $po_number,
                    $type_id,
                    $bag_count,
                    $production_date
                )
            "));

            echo "Production data stored.<br>";
        } else
            // terdapat aktivitas di packaging line
            echo "Line {$packaging_line} busy.<br>";
    }
}