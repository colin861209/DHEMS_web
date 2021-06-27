<?php
require 'commonSQL_data.php';

$load_power_sum = sqlFetchAssoc($conn, "SELECT `totalLoad` FROM `backup_totalLoad` ", array("totalLoad"));
// $uncontrollable_load_sum = sqlFetchAssoc($conn, "SELECT `totalLoad` FROM `LHEMS_uncontrollable_load` ", array("totalLoad"));
$target_price = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` WHERE `parameter_name` = 'simulate_price' ", $oneValue);
$target_solar = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` WHERE `parameter_name` = 'simulate_weather' ", $oneValue);
$electric_price = sqlFetchAssoc($conn, "SELECT `" .$target_price. "` FROM `price` ", array($target_price));
$simulate_solar = sqlFetchAssoc($conn, "SELECT `" .$target_solar. "` FROM `solar_data` ", array($target_solar));
// table info
$total_load_power_sum = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'totalLoad' ", $oneValue);
$taipower_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'LoadSpend(taipowerPrice)' ", $oneValue);
$three_level_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'LoadSpend(threeLevelPrice)' ", $oneValue);
$real_buy_grid_cost = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'realGridPurchase' ", $oneValue);
$max_sell_price = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'maximumSell' ", $oneValue);
$min_FC_cost = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` ='fuelCellSpend' ", $oneValue);
$consumption = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'hydrogenConsumption(g)' ", $oneValue);
$simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'Global_next_simulate_timeblock' ", $oneValue);

$variable_name = sqlFetchAssoc($conn, "SELECT `equip_name` FROM `backup_GHEMS` ", array("equip_name"));
$load_status_array = sqlFetchRow($conn, "SELECT * FROM `backup_GHEMS` ", $controlStatusResult);
mysqli_close($conn);

for ($y = 0; $y < $time_block; $y++) {

    $load_model[$y] = floatval($load_power_sum[$y]) + floatval($uncontrollable_load_sum[$y]);
    $load_model_seperate[0][$y] = floatval($load_power_sum[$y]);
    $load_model_seperate[1][$y] = floatval($uncontrollable_load_sum[$y]);
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
    "electric_price" => array_map('floatval', $electric_price),
    "limit_capability" => $limit_capability,
    "simulate_solar" => array_map('floatval', $simulate_solar),
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
