<?php
require 'commonSQL_data.php';

$load_power_sum = sqlFetchAssoc($conn, "SELECT `totalLoad` FROM `totalLoad_model` ", array("totalLoad"));
$uncontrollable_load_sum = sqlFetchAssoc($conn, "SELECT `totalLoad` FROM `LHEMS_uncontrollable_load` ", array("totalLoad"));
$publicLoad_power = sqlFetchAssoc($conn, "SELECT `power1` FROM `load_list` WHERE group_id = 5", array("power1"));
// table info
$total_load_power_sum = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'totalLoad' ", $oneValue);
$taipower_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'LoadSpend(taipowerPrice)' ", $oneValue);
$three_level_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'LoadSpend(threeLevelPrice)' ", $oneValue);
$real_buy_grid_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'realGridPurchase' ", $oneValue);
$max_sell_price = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'maximumSell' ", $oneValue);
$min_FC_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` ='fuelCellSpend' ", $oneValue);
$consumption = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'hydrogenConsumption(g)' ", $oneValue);
$simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'Global_next_simulate_timeblock' ", $oneValue);

$variable_name = sqlFetchAssoc($conn, "SELECT `equip_name` FROM `GHEMS_control_status` ", array("equip_name"));
$load_status_array = sqlFetchRow($conn, "SELECT * FROM `GHEMS_control_status` ", $controlStatusResult);
mysqli_close($conn);

$load_model_seperate = [];
array_push($load_model_seperate, array_map('floatval', $load_power_sum));
$load_model = array_map('floatval', $load_power_sum);
if ($uncontrollable_load_flag) {
    
    array_push($load_model_seperate, array_map('floatval', $uncontrollable_load_sum));
    $uncontrollable_load_sum = array_map('floatval', $uncontrollable_load_sum);
    $load_model = array_map(function() {
        return array_sum(func_get_args());
    }, $load_model, $uncontrollable_load_sum);
}
if ($database_name == 'DHEMS_fiftyHousehold') {
    
    if ($GHEMS_flag[1][array_search("publicLoad", $GHEMS_flag[0], true)]) {
        
        for ($i=0; $i < count($publicLoad_power); $i++) { 
            
            $name = "publicLoad".($i+1);
            $publicLoad[$i] = $load_status_array[array_search($name, $variable_name, true)];
            for ($y = 0; $y < $time_block; $y++) {
                $publicLoad[$i][$y] *= $publicLoad_power[$i];
                $publicLoad_total[$y] += $publicLoad[$i][$y];
            }
            array_push($load_model_seperate, $publicLoad[$i]);
        }
        $load_model = array_map(function() {
            return array_sum(func_get_args());
        }, $load_model, $publicLoad_total);
    }
}

for ($i = 0; $i < count($load_status_array[array_search("Psell", $variable_name, true)]); $i++) {

    $oppsite_sell_array[$i] = $load_status_array[array_search("Psell", $variable_name, true)][$i] * (-1);
}

$data_array = [

    "total_load_power_sum" => round($total_load_power_sum, 2),
    "taipower_loads_cost" => round($taipower_loads_cost, 2),
    "three_level_loads_cost" => round($three_level_loads_cost, 2),
    "real_buy_grid_cost" => round($real_buy_grid_cost, 2),
    "max_sell_price" => round($max_sell_price, 2),
    "min_FC_cost" => round($min_FC_cost, 2),
    "consumption" => round($consumption, 2),
    "electric_price" => $electric_price,
    "limit_capability" => $limit_capability,
    "simulate_solar" => $simulate_solar,
    "FC_power" => $load_status_array[array_search("Pfc", $variable_name, true)],
    "sell_power" => $oppsite_sell_array,
    "battery_power" => $load_status_array[array_search("Pess", $variable_name, true)],
    "SOC_value" => $load_status_array[array_search("SOC", $variable_name, true)],
    "grid_power" => $load_status_array[array_search("Pgrid", $variable_name, true)],
    "simulate_timeblock" => intval($simulate_timeblock),
    "load_model" => $load_model,
    "load_status_array" => $load_status_array,
    "load_model_seperate" => $load_model_seperate,
    "GHEMS_flag" => $GHEMS_flag,
    "database_name" => $database_name
];

echo json_encode($data_array);
