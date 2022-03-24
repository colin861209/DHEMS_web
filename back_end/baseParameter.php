<?php
require 'commonSQL_data.php';

$baseParameter = sqlFetchAssoc($conn, "SELECT `parameter_name`, `parameter_define`, `value` FROM `BaseParameter`", array("parameter_name", "parameter_define", "value"));
$target_solar = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'simulate_history_weather' ", $oneValue);
$simulate_history_weather = sqlFetchAssoc($conn, "SELECT `" .$target_solar. "` FROM `solar_data` ", array($target_solar));
$EM_charging_amount = sqlFetchRow($conn, "SELECT COUNT(*) FROM `EM_Pole` WHERE `charging_status`=1 ", $oneValue);
$EM_sure_charging_amount = sqlFetchRow($conn, "SELECT COUNT(*) FROM `EM_Pole` WHERE `sure`=1 ", $oneValue);

mysqli_close($conn);

$data_array = [

    "baseParameter" => $baseParameter,
    "simulate_solar" => $simulate_solar,
    "electric_price" => $electric_price,
    "dr_count" => $dr_count,
    "EM_flag" => $EM_flag,
    "EM_charging_amount" => $EM_charging_amount,
    "EM_sure_charging_amount" => $EM_sure_charging_amount,
    "electric_price_upper_limit" => $electric_price_upper_limit,
    "database_name" => $database_name
];

echo json_encode($data_array);
