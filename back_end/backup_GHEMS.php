<?php
require 'commonSQL_data.php';

$load_power_sum = sqlFetchAssoc($conn, "SELECT `totalLoad` FROM `backup_totalLoad` ", array("totalLoad"));
// $uncontrollable_load_sum = sqlFetchAssoc($conn, "SELECT `totalLoad` FROM `LHEMS_uncontrollable_load` ", array("totalLoad"));
$target_price = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` WHERE `parameter_name` = 'simulate_price' ", $oneValue);
$target_solar = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` WHERE `parameter_name` = 'simulate_weather' ", $oneValue);
$electric_price = sqlFetchAssoc($conn, "SELECT `" .$target_price. "` FROM `price` ", array($target_price));
$simulate_solar = sqlFetchAssoc($conn, "SELECT `" .$target_solar. "` FROM `solar_data` ", array($target_solar));
$dr_mode = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'dr_mode' ", $oneValue);
$publicLoad_power = sqlFetchAssoc($conn, "SELECT `power1` FROM `load_list` WHERE group_id = 5", array("power1"));
$EM_flag = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'ElectricMotor' ", $oneValue);
$EM_discharge_flag = sqlFetchRow($conn, "SELECT `value` FROM `backup_EM_Parameter` where `parameter_name` = 'Motor_can_discharge' ", $oneValue);
$EM_discharge_power = sqlFetchAssoc($conn, "SELECT `discharge_normal_power` FROM `EM_user_number`", array("discharge_normal_power"));
$EM_total_power = sqlFetchAssoc($conn, "SELECT `total_power` FROM `backup_EM_user_number`", array("total_power"));
if ($dr_mode != 0)
    $dr_info = sqlFetchRow($conn, "SELECT * FROM `demand_response` WHERE mode =" .$dr_mode , $aRow);
else
    $dr_info = null;
    
// table info
$total_load_power_sum = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'totalLoad' ", $oneValue);
$total_publicLoad_power = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'publicLoad' ", $oneValue);
$total_publicLoad_cost = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'publicLoadSpend(threeLevelPrice)' ", $oneValue);
$taipower_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'LoadSpend(taipowerPrice)' ", $oneValue);
$three_level_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'LoadSpend(threeLevelPrice)' ", $oneValue);
$real_buy_grid_cost = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'realGridPurchase' ", $oneValue);
$max_sell_price = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'maximumSell' ", $oneValue);
$min_FC_cost = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` ='fuelCellSpend' ", $oneValue);
$consumption = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'hydrogenConsumption(g)' ", $oneValue);
$dr_feedbackPrice = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'demandResponse_feedbackPrice' ", $oneValue);
$EM_total_power_sum = sqlFetchRow($conn, "SELECT SUM(total_power) FROM `backup_EM_user_number`", $oneValue);
$EM_start_departure_SOC_tmp = sqlFetchAssoc($conn, "SELECT `Start_SOC`,`Departure_SOC` FROM `backup_EM_user_result` WHERE Real_departure_timeblock IS NOT NULL", array("Start_SOC", "Departure_SOC"));
$simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'Global_next_simulate_timeblock' ", $oneValue);

$variable_name = sqlFetchAssoc($conn, "SELECT `equip_name` FROM `backup_GHEMS` ", array("equip_name"));
$load_status_array = sqlFetchRow($conn, "SELECT * FROM `backup_GHEMS` ", $controlStatusResult);
mysqli_close($conn);

$load_model_seperate = [];
$load_model = array_map('floatval', $load_power_sum);
array_push($load_model_seperate, $load_model);
if ($uncontrollable_load_flag) {
    
    $uncontrollable_load_sum = array_map('floatval', $uncontrollable_load_sum);
    array_push($load_model_seperate, $uncontrollable_load_sum);
    $load_model = array_map(function() {
        return array_sum(func_get_args());
    }, $load_model, $uncontrollable_load_sum);
}

if ($database_name == 'DHEMS_fiftyHousehold') {
        
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
    if ($EM_flag) {
    
        $EM_total_power = array_map('floatval', $EM_total_power);
        array_push($load_model_seperate, $EM_total_power);
        $load_model = array_map(function() {
            return array_sum(func_get_args());
        }, $load_model, $EM_total_power);

        if ($EM_discharge_flag) {
            $EM_discharge_power = array_map('floatval', $EM_discharge_power);
            array_push($load_model_seperate, $EM_discharge_power);
            $load_model = array_map(function() {
                return array_sum(func_get_args());
            }, $load_model, $EM_discharge_power);
        }
    }
}

for ($i = 0; $i < count($load_status_array[array_search("Psell", $variable_name, true)]); $i++) {

    $oppsite_sell_array[$i] = $load_status_array[array_search("Psell", $variable_name, true)][$i] * (-1);
}

$EM_total_power_cost = array_sum(array_map(function($x, $y) { return $x * $y * 0.25; }, $electric_price, $EM_total_power));
$EM_start_departure_SOC=[];    
array_push($EM_start_departure_SOC, array_map('floatval', $EM_start_departure_SOC_tmp[0]));
array_unshift($EM_start_departure_SOC, array_map(function($x, $y) { return $x - $y; }, array_map('floatval', $EM_start_departure_SOC_tmp[1]), $EM_start_departure_SOC[0]));

for ($i=0; $i < count($EM_start_departure_SOC); $i++) { 
    
    foreach ($EM_start_departure_SOC[$i] as $key => $value) {
        $EM_start_departure_SOC[$i][$key] = $value * 100;
    }
}

$data_array = [

    "local_simulate_timeblock" => intval($local_simulate_timeblock),
    "global_simulate_timeblock" => intval($global_simulate_timeblock),
    "total_load_power_sum" => round($total_load_power_sum, 2),
    "total_publicLoad_power" => round($total_publicLoad_power, 2),
    "total_publicLoad_cost" => round($total_publicLoad_cost, 2),
    "taipower_loads_cost" => round($taipower_loads_cost, 2),
    "three_level_loads_cost" => round($three_level_loads_cost, 2),
    "real_buy_grid_cost" => round($real_buy_grid_cost, 2),
    "max_sell_price" => round($max_sell_price, 2),
    "min_FC_cost" => round($min_FC_cost, 2),
    "consumption" => round($consumption, 2),
    "EM_flag" => intval($EM_flag),
    "EM_total_power_sum" => round($EM_total_power_sum, 2),
    "EM_total_power_cost" => round($EM_total_power_cost, 2),
    "EM_start_departure_SOC" => $EM_start_departure_SOC,
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
    "dr_mode" => $dr_mode,
    "dr_info" => $dr_info,
    "GHEMS_flag" => $GHEMS_flag,
    "dr_feedbackPrice" => round($dr_feedbackPrice, 2),
    "database_name" => $database_name
];

echo json_encode($data_array);
