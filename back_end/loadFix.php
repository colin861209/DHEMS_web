<?php
require 'commonSQL_data.php';

$load_power_sum = sqlFetchAssoc($conn, "SELECT `totalLoad` FROM `totalLoad_model` ", array("totalLoad"));
$uncontrollable_load_sum = sqlFetchAssoc($conn, "SELECT `totalLoad` FROM `LHEMS_uncontrollable_load` ", array("totalLoad"));

// table info
$taipower_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'LoadSpend(taipowerPrice)' ", $oneValue);
$three_level_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'LoadSpend(threeLevelPrice)' ", $oneValue);
$real_buy_grid_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'realGridPurchase' ", $oneValue);
$max_sell_price = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'maximumSell' ", $oneValue);
$min_FC_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` ='fuelCellSpend' ", $oneValue);
$consumption = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'hydrogenConsumption(g)' ", $oneValue);
$simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'Global_next_simulate_timeblock' ", $oneValue);

$variable_name = sqlFetchAssoc($conn, "SELECT `equip_name` FROM `GHEMS_control_status` ", array("equip_name"));
$load_status_array = sqlFetchRow($conn, "SELECT * FROM `GHEMS_control_status` ", $controlStatusResult);
$cost_name = sqlFetchAssoc($conn, "SELECT `cost_name` FROM `cost` ", array("cost_name"));
$cost_array = sqlFetchRow($conn, "SELECT * FROM `cost` ", $controlStatusResult);
mysqli_close($conn);

$total_load_power_sum = 0;
for ($y = 0; $y < $time_block; $y++) {

    $load_model[$y] = floatval($load_power_sum[$y]) + floatval($uncontrollable_load_sum[$y]);
    $load_model_seperate[0][$y] = floatval($load_power_sum[$y]);
    $load_model_seperate[1][$y] = floatval($uncontrollable_load_sum[$y]);
    $total_load_power_sum += $cost_array[array_search("total_load_power", $cost_name, true)][$y];
}

for ($i = 0; $i < count($load_status_array[array_search("Psell", $variable_name, true)]); $i++) {

    $oppsite_sell_array[$i] = $load_status_array[array_search("Psell", $variable_name, true)][$i] * (-1);
}

$data_array = [

    "total_load_power_sum" => $total_load_power_sum,
    "taipower_loads_cost" => floatval($taipower_loads_cost),
    "three_level_loads_cost" => floatval($three_level_loads_cost),
    "real_buy_grid_cost" => floatval($real_buy_grid_cost),
    "max_sell_price" => floatval($max_sell_price),
    "min_FC_cost" => floatval($min_FC_cost),
    "consumption" => floatval($consumption),
    "electric_price" => $electric_price,
    "limit_capability" => $limit_capability,
    "simulate_solar" => $simulate_solar,
    // "FC_power" => $load_status_array[array_search("Pfc", $variable_name, true)],
    "sell_power" => $oppsite_sell_array,
    "battery_power" => $load_status_array[array_search("Pess", $variable_name, true)],
    "SOC_value" => $load_status_array[array_search("SOC", $variable_name, true)],
    "grid_power" => $load_status_array[array_search("Pgrid", $variable_name, true)],
    "simulate_timeblock" => intval($simulate_timeblock),
    "load_model" => $load_model,
    "load_status_array" => $load_status_array,
    "load_model_seperate" => $load_model_seperate,
    "GHEMS_flag" => $GHEMS_flag

];

echo json_encode($data_array);
