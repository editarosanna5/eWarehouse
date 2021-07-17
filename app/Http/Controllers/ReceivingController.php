<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class ReceivingController extends Controller {
    public function ReceivingUpdate (Request $request) {
        // simpan data packaging sementara di database
        $po_number = $request->get('po_number');
        $type_id = $request->get('type_id');
        $bag_count = $request->get('bag_count');
        $production_date = '"' . $request->get('production_date') . '"';
        $packaging_line = $request->get('packaging_line');

        // periksa apakah terdapat aktivitas di packaging line
        $temp = DB::select(DB::raw(
            "SELECT COUNT(*) as foundcount
            FROM ProductionData
            WHERE
                member_id = $packaging_line
        "));

        echo '<html>';
            echo '<head>';
                echo '<meta charset="utf-8">';
                echo '<meta name="author" content="Ronoto">';
                echo '<meta name="description" content="e-warehouse loading page">';
                echo '<meta http-equiv="refresh" content="3; url=http://e-warehouse/receiving/form" />';
                
                echo '<link rel="shortcut icon" href="http://e-warehouse/client/components/favicon.ico" type="image/x-icon">';
                echo '<link rel="stylesheet" href="http://e-warehouse/client/css/style.css">';
                
                echo '<title>Packaging | e-warehouse</title>';
            echo '</head>';
            echo '<body>';        
                if ($temp[0]->foundcount == null) {
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
                    // tidak terdapat aktivitas di packaging line
                    echo '<p>Production data stored</p>';            

                } else {
                    // terdapat aktivitas di packaging line
                    echo '<p>Line ' . $packaging_line . ' is busy.</p>';
                }                
            echo '</body>';
        echo '</html>';
    }
}