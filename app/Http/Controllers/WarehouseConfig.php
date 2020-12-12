<?php

namespace App\Http\Controllers;

class WarehouseConfig {
    public static $NumberOfEntrance = 2;
    public static $NmberOfExit = 2;
    public static $NumberOfStoringForklift = 1;
    public static $NumberOfPickingForklift = 1;
    
    public static $NumberOfRows = 20;
    public static $ColumnsPerRow = 7;
    public static $StacksPerColumn = 3;
    public static $BagsPerPallet = 49;

    public static $TimeGrouping = 3;        // dalam hari
    public static $DaysToExpiration = 6;   // dalam hari

    public static function PalletsPerRow() {
        return WarehouseConfig::$StacksPerColumn * WarehouseConfig::$ColumnsPerRow;
    }
}