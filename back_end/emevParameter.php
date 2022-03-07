<?php
require 'commonSQL_data.php';

$em_motor_type = sqlFetchAssoc($conn, "SELECT `type`, `capacity`, `voltage`, `power`, `percent` FROM `EM_motor_type`", array("type", "capacity", "voltage", "power", "percent"));
$ev_motor_type = sqlFetchAssoc($conn, "SELECT `type`, `capacity(kWh)`, `power`, `percent` FROM `EV_motor_type`", array("type", "capacity(kWh)", "power", "percent"));

$emParameter = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `EM_Parameter`", array("parameter_name", "value"));
$evParameter = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `EV_Parameter`", array("parameter_name", "value"));
$emParameter_of_randomResult = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `EM_Parameter_of_randomResult`", array("parameter_name", "value"));
$evParameter_of_randomResult = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `EV_Parameter_of_randomResult`", array("parameter_name", "value"));

$em_wholeDay_chargingUser_nums = sqlFetchAssoc($conn, "SELECT `type_0`, `type_1`, `type_2`, `type_3`, `type_4`, `type_5`, `type_6`, `type_7`, `type_8`, `type_9` FROM `EM_wholeDay_userChargingNumber`", array("type_0", "type_1", "type_2", "type_3", "type_4", "type_5", "type_6", "type_7", "type_8", "type_9"));
$ev_wholeDay_chargingUser_nums = sqlFetchAssoc($conn, "SELECT `type_0`, `type_1`, `type_2`, `type_3` FROM `EV_wholeDay_userChargingNumber`", array("type_0", "type_1", "type_2", "type_3"));

$em_chargingOrDischargingStatus_array = sqlFetchRow($conn, "SELECT * FROM EM_chargingOrDischarging_status", $emChargeDischarge);
$ev_chargingOrDischargingStatus_array = sqlFetchRow($conn, "SELECT * FROM EV_chargingOrDischarging_status", $emChargeDischarge);

mysqli_close($conn);

$sf_chargingUser_nums = []; $f_chargingUser_nums = []; $n_chargingUser_nums = [];

array_push($n_chargingUser_nums, array_map('intval', $em_wholeDay_chargingUser_nums[2]));
array_push($n_chargingUser_nums, array_map('intval', $em_wholeDay_chargingUser_nums[5]));
array_push($n_chargingUser_nums, array_map('intval', $em_wholeDay_chargingUser_nums[6]));
array_push($n_chargingUser_nums, array_map('intval', $em_wholeDay_chargingUser_nums[7]));
array_push($n_chargingUser_nums, array_map('intval', $em_wholeDay_chargingUser_nums[8]));
array_push($n_chargingUser_nums, array_map('intval', $em_wholeDay_chargingUser_nums[9]));

array_push($f_chargingUser_nums, array_map('intval', $em_wholeDay_chargingUser_nums[1]));
array_push($f_chargingUser_nums, array_map('intval', $em_wholeDay_chargingUser_nums[4]));

array_push($sf_chargingUser_nums, array_map('intval', $em_wholeDay_chargingUser_nums[0]));
array_push($sf_chargingUser_nums, array_map('intval', $em_wholeDay_chargingUser_nums[3]));

$ev_chargingUser_nums = [];
for ($i=0; $i < count($ev_wholeDay_chargingUser_nums); $i++) { 
    array_push($ev_chargingUser_nums, array_map('intval', $ev_wholeDay_chargingUser_nums[$i]));
}

$data_array = [
    "local_simulate_timeblock" => intval($local_simulate_timeblock),
    "global_simulate_timeblock" => intval($global_simulate_timeblock),
    "electric_price" => $electric_price,
    "em_motor_type" => $em_motor_type,
    "ev_motor_type" => $ev_motor_type,
    "emParameter" => $emParameter,
    "evParameter" => $evParameter,
    "emParameter_of_randomResult" => $emParameter_of_randomResult,
    "evParameter_of_randomResult" => $evParameter_of_randomResult,
    "n_chargingUser_nums" => $n_chargingUser_nums,
    "f_chargingUser_nums" => $f_chargingUser_nums,
    "sf_chargingUser_nums" => $sf_chargingUser_nums,
    "ev_chargingUser_nums" => $ev_chargingUser_nums,
    "em_n_chargingUser_nums_upper_limit" => $em_n_chargingUser_nums_upper_limit,
    "ev_chargingUser_nums_upper_limit" => $ev_chargingUser_nums_upper_limit,
    "electric_price_upper_limit" =>$electric_price_upper_limit,
    "em_chargingOrDischargingStatus_array" => $em_chargingOrDischargingStatus_array,
    "ev_chargingOrDischargingStatus_array" => $ev_chargingOrDischargingStatus_array,
    "database_name" => $database_name
];

echo json_encode($data_array);
?>