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

        $delivery_id = DB::select(DB::raw(
            "SELECT do_number
            FROM OrderData
            WHERE
                do_number = $do_number
        "));

        $is_duplicate_data = $delivery_id == NULL ? false : $delivery_id[0]->do_number == $do_number;
        
        if(!$is_duplicate_data){
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
        } 

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
            if (!$is_duplicate_data){
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
            foreach($pallet_options as $pallet_option) {
                if (!$is_duplicate_data){
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
        }
        echo '<html>';
        echo '<head>';
            echo '<meta http-equiv="Refresh" content="1; url=http://e-warehouse/picking/form">';
        echo '</head>';
        echo '<script>';
            if ($is_duplicate_data){
                echo 'alert("Order already recorded.");';    
            } else {
                echo 'alert("Order successfully recorded.");';
            }
        echo '</script>';
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
            header("Location: http://e-warehouse/picking/map");
            die();
        } else {           
            header("Location: http://e-warehouse/picking/list");
            die();
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
                    echo ComponentCheck::CurrentTime();
                    // echo '<script>';
                    //     echo 'function myFunction() {';
                    //         echo 'alert("Row available for pickup. ' . $count . ' matches found.");';
                    //     echo '}';
                    // echo '</script>';
                    header("Location: http://e-warehouse/picking/map?check=true");
                    die();
                } else {
                    echo ComponentCheck::CurrentTime();
                    // echo '<script>';
                    //     echo 'function myFunction() {';
                    //         echo 'alert("Row unavailable for pickup.");';
                    //     echo '}';
                    // echo '</script>';
                }
                    header("Location: http://e-warehouse/picking/map?check=false");
                    die();
            }
        } else {
            echo "Unauthorized device.";
            return ComponentCheck::CurrentTime();
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
                    return ComponentCheck::CurrentTime();
                } else {
                    echo "Pallet not in option.";
                    return ComponentCheck::CurrentTime();
                }
            } else {
                echo "Invalid pallet ID.";
                return ComponentCheck::CurrentTime();
            }
        } else {
            echo "Unauthorized device.";
            return ComponentCheck::CurrentTime();
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
        return ComponentCheck::CurrentTime();
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
        return ComponentCheck::CurrentTime();
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
                    return ComponentCheck::CurrentTime();
                }
            }
        } else {
            echo "Unauthorized device.";
            return ComponentCheck::CurrentTime();
        }
    }

    // counter loading karung
    public function PickingBagUpdate (Request $request) {
        $device_id = $request->get('device_id');
        $member_id = ComponentCheck::DeviceID($device_id, 5);

        if ($member_id != -1) {
            $temp = DB::select(DB::raw(
                "SELECT
                    Pallets.id AS pallet_id,
                    OrderDetails.id AS order_detail_id,
                    OrderDetails.quantity AS required_bag_count
                FROM Pallets
                    JOIN OrderDetails
                        ON Pallets.type_id = OrderDetails.type_id
                    JOIN OrderData
                        ON OrderDetails.order_id = OrderData.id
                WHERE
                    OrderData.member_id = $member_id
                    AND OrderData.status_id = 2
                    AND Pallets.status_id = 7
            "));

            if ($temp == null) {
                echo "Pallet unavailable for this operation";
                return ComponentCheck::CurrentTime();
            } else {
                $pallet_id = $temp[0]->pallet_id;
                $order_detail_id = $temp[0]->order_detail_id;
                $required_bag_count = $temp[0]->required_bag_count;

                // update status loading
                DB::update(DB::raw(
                    "UPDATE LoadingStatus
                    SET
                        loaded_bag_count = loaded_bag_count + 1
                "));

                // periksa jumlah loaded_bag_count
                $temp = DB::select(DB::raw(
                    "SELECT loaded_bag_count
                    FROM LoadingStatus
                    WHERE
                        id = $order_detail_id
                "));
                
                $loaded_bag_count = $temp[0]->loaded_bag_count;

                // periksa jumlah karung tersisa pada palet
                $temp = DB::select(DB::raw(
                    "SELECT bag_count
                    FROM Pallets
                    WHERE
                        id = $pallet_id
                "));

                $pallet_bag_count = $temp[0]->bag_count;

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
                        WHERE
                            id = $pallet_id
                    "));
                } else {    // sisa karung > 1
                    // loaded_bag_count terpenuhi
                    if ($loaded_bag_count == $required_bag_count) {
                        DB::update(DB::raw(
                            "UPDATE Pallets
                            SET
                                status_id = 8,
                                bag_count = bag_count - 1
                            WHERE
                                id = $pallet_id
                        "));
                    } else {
                        DB::update(DB::raw(
                            "UPDATE Pallets
                            SET
                                bag_count = bag_count - 1
                            WHERE
                                id = $pallet_id
                        "));
                    }
                }
                echo "Bag successfully loaded.";
                return ComponentCheck::CurrentTime();
            }
        } else {
            echo "Unauthorized device.";
            return ComponentCheck::CurrentTime();
        }
    }

    public function PickingList () {
        echo '<html>';
            echo '<head>';
                echo '<meta charset="utf-8">';
                echo '<meta name="author" content="Ronoto">';
                echo '<meta name="description" content="e-warehouse loading page">';
                echo '<meta http-equiv="refresh" content="5">';
                echo '<link rel="shortcut icon" href="http://e-warehouse/client/components/favicon.ico" type="image/x-icon">';
                echo '<link rel="stylesheet" href="http://e-warehouse/client/css/style.css">';
                
                echo '<title>Picking List | e-warehouse</title>';
            echo '</head>';
            echo '<body>';
                echo '<div id="nav">';
                    echo '<ul class="content">';
                        echo '<li><a href="http://e-warehouse">HOME</a></li>';
                        echo '<li><a href="http://e-warehouse/warehouse">STORAGE</a></li>';
                    echo '</ul>';
                echo '</div>';
                
                echo '<h1 class="content">PICKING LIST</h1>';
                echo '<br>';

                $queries = DB::select(DB::raw(
                    "SELECT
                        id,
                        do_number,
                        order_date,
                        status_id
                    FROM OrderData
                    WHERE
                        status_id = 1
                "));

                foreach ($queries as $query) {
                    echo '<div class="items">';
                        echo 'DO Number: ' . $query->do_number . '<br><br>';
                        echo 'Date issued: ' . $query->order_date . '<br>';
                        if ($query->status_id == 1) {
                            echo '<button onclick="location.href=\'http://e-warehouse/picking/select?order_id=' . $query->id . '&device_id=3-1\';">PICKUP</button>';   
                        } else {
                            echo '<button onclick="myFunction()">PICKUP</button>';
                            echo '<script>';
                                echo 'function myFunction() {';
                                    echo 'alert("Order busy.");';
                                echo '}';
                            echo '</script>';
                        }                        
                    echo '</div>';
                }
            echo '</body>';
        echo '</html>';
    }

    public function PickingMap (Request $request) {
        echo '<html>';
            echo '<head>';
                echo '<meta charset="utf-8">';
                echo '<meta name="author" content="Ronoto">';
                echo '<meta name="description" content="e-warehouse warehouse map">';
                echo '<title>Picking Map | e-Warehouse</title>';
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
                    
                    echo '<h1 class="content">PICKING MAP</h1>';
                    
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

                $queries = DB::select(DB::raw(
                    "SELECT
                        Pallets.row_number AS row_number,
                        Pallets.column_number AS column_number,
                        Rows.pallet_count AS pallet_count
                    FROM
                        PickupOptions JOIN Pallets
                            ON Pallets.id = PickupOptions.pallet_id
                        JOIN `Rows`
                            ON Rows.id = Pallets.row_number
                "));

                foreach ($queries as $query) {
                    $i = $query->row_number;
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

                $check = $request->get('check');
        
                if ($check == "true") {
                    echo '<script>';
                        echo 'alert("Row available for pickup.");';
                    echo '</script>';
                } elseif ($check == "false") {
                    echo '<script>';
                        echo 'alert("Row unavailable for pickup.");';
                    echo '</script>';
                }

            echo '</body>';
        echo '</html>';
    }

    public function LoadingCounter () {
        
            
            $query = DB::select(
                "SELECT
                    -- OrderDetails.order_id AS do_number,
                    OrderDetails.type_id AS type_id,
                    OrderDetails.quantity AS required_bag_count,
                    LoadingStatus.loaded_bag_count AS loaded_bag_count,
                    OrderData.do_number AS do_number
                FROM OrderDetails 
                    JOIN LoadingStatus ON OrderDetails.id = LoadingStatus.id 
                    JOIN OrderData ON OrderDetails.id = OrderData.id
            ");
            if ($query==null){
              $do_number = "No Order Found";
              $type_id = "0";
              $required_bag_count = "0";
              $loaded_bag_count = "0";
            } else {
              $do_number = $query[0]->do_number;
              $type_id = $query[0]->type_id;
              $required_bag_count = $query[0]->required_bag_count;
              $loaded_bag_count = $query[0]->loaded_bag_count;
            }
            echo '<html>';
            echo '<head>';
            echo '<meta charset="utf-8">';
            echo '<meta name="author" content="Ronoto">';
            echo '<meta name="description" content="e-warehouse warehouse map">';
            echo '<title>Loading | e-Warehouse</title>';
            echo '<meta http-equiv="refresh" content="3">';
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
            
            echo '<h1 class="content">LOADING</h1>';
echo '<div class="counter">';
echo '<p>&ensp;DO Number :&ensp;' . $do_number . '</p>';
echo '<p><br>&ensp;Type ID &emsp;&ensp;&nbsp;:&ensp;' . $type_id .'</p>';
echo '<p><br>&ensp;Loaded  &emsp;&ensp;&nbsp;:&ensp;' . $loaded_bag_count . '&nbsp;/&nbsp;' . $required_bag_count . '</p>';
echo '</div>';
            echo '</body>';
        echo '</html>';

    }
}