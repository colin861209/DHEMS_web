<?php
require 'commonSQL_data.php';

$em_motor_type = sqlFetchAssoc($conn, "SELECT `type`, `capacity`, `voltage`, `power`, `percent` FROM `EM_motor_type`", array("type", "capacity", "voltage", "power", "percent"));

$emParameter = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `EM_Parameter`", array("parameter_name", "value"));
$emParameter_of_ESS = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `EM_Parameter_of_ESS`", array("parameter_name", "value"));
$emParameter_of_randomResult = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `EM_Parameter_of_randomResult`", array("parameter_name", "value"));

$wholeDay_chargingUser_nums = sqlFetchAssoc($conn, "SELECT `type_0`, `type_1`, `type_2`, `type_3`, `type_4`, `type_5`, `type_6`, `type_7`, `type_8`, `type_9` FROM `EM_wholeDay_userChargingNumber`", array("type_0", "type_1", "type_2", "type_3", "type_4", "type_5", "type_6", "type_7", "type_8", "type_9"));

$em_chargingOrDischargingStatus_array = sqlFetchRow($conn, "SELECT * FROM EM_chargingOrDischarging_status", $emChargeDischarge);

mysqli_close($conn);

$sf_chargingUser_nums = []; $f_chargingUser_nums = []; $n_chargingUser_nums = [];

array_push($n_chargingUser_nums, array_map('intval', $wholeDay_chargingUser_nums[2]));
array_push($n_chargingUser_nums, array_map('intval', $wholeDay_chargingUser_nums[5]));
array_push($n_chargingUser_nums, array_map('intval', $wholeDay_chargingUser_nums[6]));
array_push($n_chargingUser_nums, array_map('intval', $wholeDay_chargingUser_nums[7]));
array_push($n_chargingUser_nums, array_map('intval', $wholeDay_chargingUser_nums[8]));
array_push($n_chargingUser_nums, array_map('intval', $wholeDay_chargingUser_nums[9]));

array_push($f_chargingUser_nums, array_map('intval', $wholeDay_chargingUser_nums[1]));
array_push($f_chargingUser_nums, array_map('intval', $wholeDay_chargingUser_nums[4]));

array_push($sf_chargingUser_nums, array_map('intval', $wholeDay_chargingUser_nums[0]));
array_push($sf_chargingUser_nums, array_map('intval', $wholeDay_chargingUser_nums[3]));


$data_array = [
    "local_simulate_timeblock" => intval($local_simulate_timeblock),
    "global_simulate_timeblock" => intval($global_simulate_timeblock),
    "em_motor_type" => $em_motor_type,
    "emParameter" => $emParameter,
    "emParameter_of_ESS" => $emParameter_of_ESS,
    "emParameter_of_randomResult" => $emParameter_of_randomResult,
    "n_chargingUser_nums" => $n_chargingUser_nums,
    "f_chargingUser_nums" => $f_chargingUser_nums,
    "sf_chargingUser_nums" => $sf_chargingUser_nums,
    "em_chargingOrDischargingStatus_array" => $em_chargingOrDischargingStatus_array,
    "database_name" => $database_name
];

echo json_encode($data_array);
?>