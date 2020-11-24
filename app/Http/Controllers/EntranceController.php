<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class EntranceController extends Controller {
    public function PalletUpdate($pallet_id){
        DB::update(DB::raw("UPDATE Pallets SET
            type_id = DB::
        "));
    }

    public function BagUpdate($pallet_id) {
        
    }
}