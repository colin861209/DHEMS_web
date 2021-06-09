<?php
require 'commonSQL_data.php';

$baseParameter = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `BaseParameter`", array("parameter_name", "value"));
$target_solar = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'simulate_history_weather' ", $oneValue);
$simulate_history_weather = sqlFetchAssoc($conn, "SELECT `" .$target_solar. "` FROM `solar_data` ", array($target_solar));
mysqli_close($conn);

$data_array = [

    "baseParameter" => $baseParameter,
    "simulate_solar" => $simulate_solar,
    "simulate_history_weather" => array_map('floatval', $simulate_history_weather),
    "electric_price" => $electric_price,
    "database_name" => $database_name
];

echo json_encode($data_array);
