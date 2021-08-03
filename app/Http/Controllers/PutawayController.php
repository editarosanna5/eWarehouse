<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;

class PutawayController extends Controller {
    // mengambil semua opsi penyimpanan
    // menyimpan opsi penyimpanan ke database
    public function PutawayMovingFetch ($member_id, $pallet_id_int) {
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
        $production_date = '"' . $pallet_data[0]->production_date . '"';
        
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

        if (count($final_options) != 0) {
            DB::delete(DB::raw(
                "DELETE FROM StorageOptions
                WHERE
                    member_id = $member_id
            "));

            foreach ($final_options as $final_option) {
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
            }
        }

        return $final_options;
    }

    public function PutawayMovingUpdate (Request $request) {
        $device_id = $request->get('device_id');
        $pallet_id = $request->get('pallet_id');

        // verifikasi device id
        $member_id  = ComponentCheck::DeviceID($device_id, 2);

        if ($member_id != -1) {
            // verifikasi pallet id
            $pallet_id_int = ComponentCheck::PalletID($pallet_id, 2);

            if ($pallet_id_int != -1) {
                // echo "Tes";
                // return;
                $final_options = PutawayController::PutawayMovingFetch($member_id, $pallet_id_int);

                if (count($final_options) != 0) {
                    // update status palet menjadi MOVING_TO_STORAGE_ZONE (3)
                    DB::update(DB::raw(
                        "UPDATE Pallets
                        SET
                            status_id = 3
                        WHERE
                            id = $pallet_id_int
                    "));
                    echo "Pallet " .  $pallet_id_int . "available for putaway";
                    return ComponentCheck::CurrentTime();
                } else {
                    echo "No available rows.<br>";
                    return ComponentCheck::CurrentTime();
                }
            } else {
                echo "Pallet {$pallet_id} invalid or unavailable for put away.<br>";
                return ComponentCheck::CurrentTime();
            }
        } else {
            echo "Unauthorized device.<br>";
            return ComponentCheck::CurrentTime();
        }
    }

    public function PutawayArrivalUpdate (Request $request) {
        $device_id = $request->get('device_id');
        $row_id = $request->get('row_id');

        // verifikasi device id
        $member_id = ComponentCheck::DeviceID($device_id, 2);

        if ($member_id != -1) {
            // verifikasi row id
            $row_id_int = ComponentCheck::RowID($row_id);

            if ($row_id_int != -1) {
                $temp = DB::select(DB::raw(
                    "SELECT pallet_id
                    FROM StorageOptions
                    WHERE
                        row_id = $row_id_int
                        AND member_id = $member_id
                "));

                if ($temp != null) {
                    $pallet_id = $temp[0]->pallet_id;
                    
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
                    $column_number = intdiv($pallet_count, WarehouseConfig::$StacksPerColumn) + 1;                  
                    $stack_number = $pallet_count % WarehouseConfig::$StacksPerColumn;
                    
                    if ($stack_number == 0) {
                        $column_number -= 1;
                    }

                    DB::update(DB::raw(
                        "UPDATE `Rows`
                        SET
                            pallet_count = $pallet_count
                        WHERE
                            id = $row_id_int
                    "));
                    
                    DB::update(DB::raw(
                        "UPDATE Pallets
                        SET
                            -- update status palet menjadi ON_STORAGE (4)
                            status_id = 4,
                            row_number = $row_id_int,
                            column_number = $column_number,
                            stack_number = $stack_number
                        WHERE
                            id = $pallet_id
                    "));

                    DB::delete(DB::raw(
                        "DELETE FROM StorageOptions
                        WHERE
                            member_id = $member_id
                            OR row_id = $row_id_int
                    "));

                    echo "Item has been stored on Row " . $row_id_int;
                    return ComponentCheck::CurrentTime();
                } else {
                    echo "Row not recommended.";
                    return ComponentCheck::CurrentTime();
                }
            } else {
                echo "Invalid row ID.";
                return ComponentCheck::CurrentTime();
            }
        } else {
            echo "Unauthorized device.";
            return ComponentCheck::CurrentTime();
        }
    }

    public function PutawayMap () {
        echo '<html>';
            echo '<head>';
                echo '<meta charset="utf-8">';
                echo '<meta name="author" content="Ronoto">';
                echo '<meta name="description" content="e-warehouse warehouse map">';
                echo '<title>Putaway | e-Warehouse</title>';
                echo '<meta http-equiv="refresh" content="5">';
                echo '<link rel="shortcut icon" href="http://e-warehouse/client/components/favicon.ico" type="image/x-icon">';
                echo '<link rel="stylesheet" href="http://e-warehouse/client/css/style.css">';
                echo '<script src="http://e-warehouse/client/components/anychart-installation-package-8.10.0/js/anychart-core.min.js"></script>';
                echo '<script src="http://e-warehouse/client/components/anychart-installation-package-8.10.0/js/anychart-heatmap.min.js"></script>';
            echo '</head>';
            echo '<body>';
                echo '<div id="nav">';
                    echo '<ul class="content">';
                        echo '<li><a href="http://e-warehouse">HOME</a></li>';
                        echo '<li><a href="http://e-warehouse/warehouse">STORAGE</a></li>';
                    echo '</ul>';
                echo '</div>';
                    
                    echo '<h1 class="content">PUTAWAY</h1>';
                    $queries = DB::select(DB::raw(
                        "SELECT
                            StorageOptions.row_id AS row_id,
                            Rows.pallet_count AS pallet_count
                        FROM StorageOptions JOIN `Rows`
                            ON StorageOptions.row_id = Rows.id
                    "));
                    echo '<h4 style="text-align:right">Recommended Putaway Line: '; foreach ($queries as $value) {echo $value->row_id . ", ";} echo '</h4>';
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

                for ($i = 1; $i <= 20; $i++) {
                    $map[$i] = array_fill(1,7,0);
                }

                foreach ($queries as $query) {
                    $i = $query->row_id;
                    $j = ceil($query->pallet_count / 3);
                    if ($j % 3 == 0) {
                        $j++;
                    }
                    $map[$i][$j] = 1;
                }

                echo '<script>';
                    echo 'anychart.onDocumentReady(function () {';
                        // create the data
                        echo 'var data1 = [';
                        for ($i=$group1_min; $i<=$group1_max; $i++) {
                            for ($j=1; $j<=7; $j++) {
                                echo '{ x: "L-000' . $i . '", y: "' . $j . '", heat: ' . $map[$i][$j] . ' },';
                            }
                        }
                        echo '];';

                        echo 'var data2 = [';
                        for ($i=$group2_min; $i<=$group2_max; $i++) {
                            for ($j=1; $j<=7; $j++) {
                                echo '{ x: "L-000' . $i . '", y: "' . $j . '", heat: ' . $map[$i][$j] . ' },';
                            }
                        }
                        echo '];';

                        echo 'var data3 = [';
                        for ($i=$group3_min; $i<=$group3_max; $i++) {
                            for ($j=1; $j<=7; $j++) {
                                echo '{ x: "L-000' . $i . '", y: "' . $j . '", heat: ' . $map[$i][$j] . ' },';
                            }
                        }
                        echo '];';
                        
                        // create and configure the color scale.
                        echo 'var customColorScale = anychart.scales.ordinalColor();';
                        echo 'customColorScale.ranges([';
                            echo '{ less: 0.99, name: \'Not available\', color: \'LightBlue\' },';
                            echo '{ greater: 0.99, name: \'Available\', color: \'Gold\' },';
                        echo ']);';
                        
                        // create the chart and set the data
                        echo 'map1 = anychart.heatMap(data1);';
                        echo 'map2 = anychart.heatMap(data2);';
                        echo 'map3 = anychart.heatMap(data3);';
                        
                        // set the color scale as the color scale of the chart
                        echo 'map1.colorScale(customColorScale);';
                        echo 'map2.colorScale(customColorScale);';
                        echo 'map3.colorScale(customColorScale);';

                        // labels settings
                        echo 'var labels1 = map1.labels();';
                        echo 'var labels2 = map2.labels();';
                        echo 'var labels3 = map3.labels();';
                        // enable labels
                        echo 'labels1.enabled(false);';
                        echo 'labels2.enabled(false);';
                        echo 'labels3.enabled(false);';
                        
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