<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class StorageController extends Controller {
    
    // mengambil data storage untuk pemetaan
    public function StorageFetch () {
        $NumberOfRows = WarehouseConfig::$NumberOfRows;
        $ColumnsPerRow = WarehouseConfig::$ColumnsPerRow;
        
        for ($i=1; $i<=$NumberOfRows; $i++) {
            for ($j=1; $j<=$ColumnsPerRow; $j++) {
                $query = DB::select(DB::raw(
                    "SELECT COUNT(*) AS pallet_count
                    FROM Pallets
                    WHERE
                        row_number = $i
                        AND column_number = $j
                "));
                $pallet_count[$i][$j] = $query[0]->pallet_count;
            }
        }

        echo '<html>';
            echo '<head>';
                echo '<meta charset="utf-8">';
                echo '<meta name="author" content="Ronoto">';
                echo '<meta name="description" content="e-warehouse warehouse map">';
                echo '<title>Storage | e-Warehouse</title>';
                echo '<meta http-equiv="refresh" content="5">';
                echo '<link rel="shortcut icon" href="../client/components/favicon.ico" type="image/x-icon">';
                echo '<link rel="stylesheet" href="../client/css/Loading.css">';
                echo '<script src="../client/components/anychart-installation-package-8.10.0/js/anychart-core.min.js"></script>';
                echo '<script src="../client/components/anychart-installation-package-8.10.0/js/anychart-heatmap.min.js"></script>';
                echo '<style>';
                    echo '#group1, #group2, #group3 {';
                        echo 'height: 78%;';
                    echo '}';
                    echo '#group1 {';
                        echo 'width:27%;';
                        echo 'margin-top:16%;';
                        echo 'margin-left:25%;';
                        echo 'float:left;';
                    echo '}';
                    echo '#group2, #group3 {';
                        echo 'width:30.5%;';
                        echo 'margin-right:10%;';
                        echo 'float:right;';
                    echo '}';
                    echo '#group3 {';
                        echo 'margin-top:1%;';
                    echo '}';
                    
                    echo '.map {';
                        echo 'height: 420px;';
                        echo 'width:100%;';
                        echo 'margin-top:20px;';
                        echo 'overflow-y: auto;';
                        echo 'border: 1px solid #969696;';
                    echo '}';
                echo '</style>';
            echo '</head>';
            echo '<body>';
                echo '<div id="nav">';
                    echo '<ul>';
                        echo '<li><a href="http://e-warehouse">HOME</a></li>';
                        echo '<li><a href="http://e-warehouse/warehouse" onclick="return false;">STORAGE</a></li>';
                    echo '</ul>';
                echo '</div>';
                    
                    echo '<h1>STORAGE</h1>';
                    
                echo '<div class="map">';
                    echo '<div id="group1"></div>';
                    echo '<div id="group2"></div>';
                    echo '<div id="group3"></div>';
                echo '</div>';
                
                $group1_min = 1;
                $group1_max = 6;
                $group2_min = 7;
                $group2_max = 13;
                $group3_min = 14;
                $group3_max = 20;

                echo '<script>';
                    echo 'anychart.onDocumentReady(function () {';
                        // create the data
                        echo 'var data1 = [';
                        for ($i=$group1_min; $i<=$group1_max; $i++) {
                            for ($j=1; $j<=$ColumnsPerRow; $j++) {
                                echo '{ x: "L-000' . $i . '", y: "' . $j . '", heat: ' . $pallet_count[$i][$j] . ' },';
                            }
                        }
                        echo '];';

                        echo 'var data2 = [';
                        for ($i=$group2_min; $i<=$group2_max; $i++) {
                            for ($j=1; $j<=$ColumnsPerRow; $j++) {
                                echo '{ x: "L-000' . $i . '", y: "' . $j . '", heat: ' . $pallet_count[$i][$j] . ' },';
                            }
                        }
                        echo '];';

                        echo 'var data3 = [';
                        for ($i=$group3_min; $i<=$group3_max; $i++) {
                            for ($j=1; $j<=$ColumnsPerRow; $j++) {
                                echo '{ x: "L-000' . $i . '", y: "' . $j . '", heat: ' . $pallet_count[$i][$j] . ' },';
                            }
                        }
                        echo '];';
                        
                        // create and configure the color scale.
                        echo 'var customColorScale = anychart.scales.ordinalColor();';
                        echo 'customColorScale.ranges([';
                            echo '{ less: 0.99, name: \'Empty\', color: \'LightBlue\' },';
                            echo '{ from: 1, to: 2, name: \'Occupied\', color: \'YellowGreen\' },';
                            echo '{ greater: 2, name: \'Occupied-full\', color: \'SeaGreen\' }';
                        echo ']);';
                        
                        // create the chart and set the data
                        echo 'map1 = anychart.heatMap(data1);';
                        echo 'map2 = anychart.heatMap(data2);';
                        echo 'map3 = anychart.heatMap(data3);';
                        
                        // set the color scale as the color scale of the chart
                        echo 'map1.colorScale(customColorScale);';
                        echo 'map2.colorScale(customColorScale);';
                        echo 'map3.colorScale(customColorScale);';
                        
                        // set the container id
                        echo 'map1.container("group1");';
                        echo 'map2.container("group2");';
                        echo 'map3.container("group3");';
                        
                        // initiate drawing the chart
                        echo 'map1.draw();';
                        echo 'map2.draw();';
                        echo 'map3.draw();';
                    echo '});';
                echo '</script>';
            echo '</body>';
        echo '</html>';
    }
}