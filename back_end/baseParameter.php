<?php
require 'commonSQL_data.php';

$BP = 'BaseParameter';
$obj = new BPSetting($BP);


$data_array = [

    // fetch mysql
    "database_name" => $obj->database_name,
    // BPSetting
    "baseParameter" => $obj->baseParameter,
    "EM_charging_amount" => $obj->EM_charging_amount,
    "EM_sure_charging_amount" => $obj->EM_sure_charging_amount,
    "EM_flag" => $obj->EM_flag,
    // CommonData & BP construct
    "electric_price_upper_limit" => $obj->electric_price_upper_limit,
    "weather_upper_limit" => $obj->weather_upper_limit,
    "simulate_solar" => $obj->simulate_solar,
    "electric_price" => $obj->electric_price,
    "dr_count" => $obj->dr_count,
];

echo json_encode($data_array);
