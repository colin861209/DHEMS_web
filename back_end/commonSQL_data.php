<?php
require 'fetch_mysql.php';

$target_price = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'simulate_price' ", $oneValue);
$target_solar = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'simulate_weather' ", $oneValue);
$electric_price_tmp = sqlFetchAssoc($conn, "SELECT `" .$target_price. "` FROM `price` ", array($target_price));
$simulate_solar = sqlFetchAssoc($conn, "SELECT `" .$target_solar. "` FROM `solar_data` ", array($target_solar));
$GHEMS_flag = sqlFetchAssoc($conn, "SELECT `variable_name`, `flag` FROM `GHEMS_flag` WHERE `flag` IS NOT NULL", array("variable_name", "flag"));
$LHEMS_flag = sqlFetchAssoc($conn, "SELECT `variable_name`, `flag` FROM `LHEMS_flag` WHERE `flag` IS NOT NULL", array("variable_name", "flag"));

$local_simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'next_simulate_timeblock' ", $oneValue);
$global_simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'Global_next_simulate_timeblock' ", $oneValue);
$limit_power = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'Pgridmax' ", $oneValue);
$time_block = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'time_block' ", $oneValue);
$dr_mode = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'dr_mode' ", $oneValue);
$dr_count = sqlFetchRow($conn, "SELECT COUNT(*) FROM `demand_response` ", $oneValue);
if ($dr_mode != 0)
    $dr_info = sqlFetchRow($conn, "SELECT * FROM `demand_response` WHERE mode =" .$dr_mode , $aRow);

$uncontrollable_load_flag = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'uncontrollable_load_flag' ", $oneValue);
$comfortLevel_flag = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'comfortLevel_flag' ", $oneValue);
$EM_flag = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'ElectricMotor' ", $oneValue);

for ($y = 0; $y < $time_block; $y++) {

    $limit_capability[$y] = floatval($limit_power);
    $simulate_solar[$y] = floatval($simulate_solar[$y]);
    $electric_price[$y] = floatval($electric_price_tmp[$y]);
}

?>