<?php
require 'fetch_mysql.php';

$electric_price_array = sqlFetchAssoc($conn, "SELECT `price_value` FROM `price` ", array("price_value"));
$solor_fake = sqlFetchAssoc($conn, "SELECT `value` FROM `solar_day` ", array("value"));
$load_power_sum = sqlFetchAssoc($conn, "SELECT `totalLoad` FROM `totalLoad_model` ", array("totalLoad"));

// table info
// $taipower_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'LoadSpend(taipowerPrice)' ", $oneValue);
// $three_level_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'LoadSpend(threeLevelPrice)' ", $oneValue);
// $real_buy_grid_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'realGridPurchase' ", $oneValue);
// $max_sell_price = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'maximumSell' ", $oneValue);
// $min_FC_cost = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` ='fuelCellSpend' ", $oneValue);
// $consumption = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'hydrogenConsumption(g)' ", $oneValue);

$limit_power = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'Pgridmax' ", $oneValue);
// $simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'Global_next_simulate_timeblock' ", $oneValue);

$variable_name = sqlFetchAssoc($conn, "SELECT `equip_name` FROM `GHEMS_control_status` ", array("equip_name"));
$FCPrice_pointZeroEight_array = sqlFetchRow($conn, "SELECT * FROM `FCPrice0.08` ", $controlStatusResult);
$FCPrice_pointOne_array = sqlFetchRow($conn, "SELECT * FROM `FCPrice0.1` ", $controlStatusResult);
$FCPrice_pointOneTwo_array = sqlFetchRow($conn, "SELECT * FROM `FCPrice0.12` ", $controlStatusResult);
// $cost_name = sqlFetchAssoc($conn, "SELECT `cost_name` FROM `cost` ", array("cost_name"));
// $cost_array = sqlFetchRow($conn, "SELECT * FROM `cost` ", $controlStatusResult);
mysqli_close($conn);

for($y=0;$y<96;$y++)
    $limit_capability[$y] = floatval($limit_power);

// electric_price_array
for($y=0; $y<24; $y++) {

    for ($i=0;$i<4;$i++)
        $electric_price[4*$y+$i] = floatval($electric_price_array[$y]);
}

for($y=0; $y<96; $y++)
    $simulate_solar[$y] = floatval($solor_fake[$y]);
    
for($y=0; $y<96; $y++)
    $load_model[$y] = floatval($load_power_sum[$y]); 

// for($y=0; $y<96; $y++)
//     $total_load_power_sum += $cost_array[array_search("total_load_power", $cost_name, true)][$y]; 

$pointZeroEight_tmp_sell = $FCPrice_pointZeroEight_array[array_search("Psell", $variable_name, true)];
$pointOne_tmp_sell = $FCPrice_pointOne_array[array_search("Psell", $variable_name, true)];
$pointOneTwo_tmp_sell = $FCPrice_pointOneTwo_array[array_search("Psell", $variable_name, true)];
for ($i=0; $i < count($pointZeroEight_tmp_sell); $i++) { 

    $FCPrice_pointZeroEight_sell[$i] = $pointZeroEight_tmp_sell[$i] * (-1);
    $FCPrice_pointOne_sell[$i] = $pointOne_tmp_sell[$i] * (-1);
    $FCPrice_pointOneTwo_sell[$i] = $pointOneTwo_tmp_sell[$i] * (-1);
}
$data_array = [

    // "total_load_power_sum"=>$total_load_power_sum,
    // "taipower_loads_cost"=>floatval($taipower_loads_cost),
    // "three_level_loads_cost"=>floatval($three_level_loads_cost),
    // "real_buy_grid_cost"=>floatval($real_buy_grid_cost),
    // "max_sell_price"=>floatval($max_sell_price),
    // "min_FC_cost"=>floatval($min_FC_cost),
    // "consumption"=>floatval($consumption),

    "electric_price"=>$electric_price,
    "limit_capability"=>$limit_capability,
    "simulate_solar"=>$simulate_solar,
    // 0.08
    "eight_FC_power"=>$FCPrice_pointZeroEight_array[array_search("Pfc", $variable_name, true)],
    "eight_sell_power"=>$FCPrice_pointZeroEight_sell,
    "eight_battery_power"=>$FCPrice_pointZeroEight_array[array_search("Pess", $variable_name, true)],
    "eight_SOC_value"=>$FCPrice_pointZeroEight_array[array_search("SOC", $variable_name, true)],
    "eight_grid_power"=>$FCPrice_pointZeroEight_array[array_search("Pgrid", $variable_name, true)],
    // 0.1
    "one_FC_power"=>$FCPrice_pointZeroEight_array[array_search("Pfc", $variable_name, true)],
    "one_sell_power"=>$FCPrice_pointOne_sell,
    "one_battery_power"=>$FCPrice_pointZeroEight_array[array_search("Pess", $variable_name, true)],
    "one_SOC_value"=>$FCPrice_pointZeroEight_array[array_search("SOC", $variable_name, true)],
    "one_grid_power"=>$FCPrice_pointZeroEight_array[array_search("Pgrid", $variable_name, true)],
    // 0.12
    "oneTwo_FC_power"=>$FCPrice_pointOneTwo_array[array_search("Pfc", $variable_name, true)],
    "oneTwo_sell_power"=>$FCPrice_pointOneTwo_sell,
    "oneTwo_battery_power"=>$FCPrice_pointOneTwo_array[array_search("Pess", $variable_name, true)],
    "oneTwo_SOC_value"=>$FCPrice_pointOneTwo_array[array_search("SOC", $variable_name, true)],
    "oneTwo_grid_power"=>$FCPrice_pointOneTwo_array[array_search("Pgrid", $variable_name, true)],
    "load_model"=>$load_model,

    // "simulate_timeblock"=>intval($simulate_timeblock),

];

    
echo json_encode($data_array);
