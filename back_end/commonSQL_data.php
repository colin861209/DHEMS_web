<?php
require 'fetch_mysql.php';

$electric_price_tmp = sqlFetchAssoc($conn, "SELECT `price_value` FROM `price` ", array("price_value"));
$simulate_solar = sqlFetchAssoc($conn, "SELECT `value` FROM `solar_day` ", array("value"));
$GHEMS_flag = sqlFetchAssoc($conn, "SELECT `variable_name`, `flag` FROM `GHEMS_flag` WHERE `flag` IS NOT NULL", array("variable_name", "flag"));
$LHEMS_flag = sqlFetchAssoc($conn, "SELECT `variable_name`, `flag` FROM `LHEMS_flag` WHERE `flag` IS NOT NULL", array("variable_name", "flag"));

$limit_power = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'Pgridmax' ", $oneValue);
$time_block = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'time_block' ", $oneValue);
$dr_mode = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'dr_mode' ", $oneValue);
$uncontrollable_load_flag = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'uncontrollable_load_flag' ", $oneValue);

for ($y = 0; $y < $time_block; $y++) {

    $limit_capability[$y] = floatval($limit_power);
    $simulate_solar[$y] = floatval($simulate_solar[$y]);
    $electric_price[$y] = floatval($electric_price_tmp[$y]);
}

?>