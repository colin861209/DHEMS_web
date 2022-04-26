<?php
require 'commonSQL_data.php';

$BP = 'BaseParameter';
$emev = new EMEVSetting($BP);

$data_array = [
    // fetch mysql
    "database_name" => $emev->database_name,
    "local_simulate_timeblock" => $emev->local_simulate_timeblock,
    "global_simulate_timeblock" => $emev->global_simulate_timeblock,
    "electric_price" => $emev->electric_price,
    "electric_price_upper_limit" =>$emev->electric_price_upper_limit,

    "em_motor_type" => $emev->em_motor_type,
    "ev_motor_type" => $emev->ev_motor_type,
    "emParameter" => $emev->emParameter,
    "evParameter" => $emev->evParameter,
    "emParameter_of_randomResult" => $emev->emParameter_of_randomResult,
    "evParameter_of_randomResult" => $emev->evParameter_of_randomResult,
    "n_chargingUser_nums" => $emev->n_chargingUser_nums,
    "f_chargingUser_nums" => $emev->f_chargingUser_nums,
    "sf_chargingUser_nums" => $emev->sf_chargingUser_nums,
    "ev_chargingUser_nums" => $emev->ev_chargingUser_nums,
    "em_n_chargingUser_nums_upper_limit" => $emev->em_n_chargingUser_nums_upper_limit,
    "ev_chargingUser_nums_upper_limit" => $emev->ev_chargingUser_nums_upper_limit,
    "em_chargingOrDischargingStatus_array" => $emev->em_chargingOrDischargingStatus_array,
    "ev_chargingOrDischargingStatus_array" => $emev->ev_chargingOrDischargingStatus_array,
];

echo json_encode($data_array);
?>