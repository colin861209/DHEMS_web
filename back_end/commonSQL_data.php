<?php
require 'fetch_mysql.php';

$target_price = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'simulate_price' ", $oneValue);
$target_solar = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'simulate_weather' ", $oneValue);
$electric_price = array_map('floatval', sqlFetchAssoc($conn, "SELECT `" .$target_price. "` FROM `price` ", array($target_price)));
$simulate_solar = array_map('floatval', sqlFetchAssoc($conn, "SELECT `" .$target_solar. "` FROM `solar_data` ", array($target_solar)));
$GHEMS_flag = sqlFetchAssoc($conn, "SELECT `variable_name`, `variable_define`, `flag` FROM `GHEMS_flag` WHERE `flag` IS NOT NULL", array("variable_name", "variable_define", "flag"));
$LHEMS_flag = sqlFetchAssoc($conn, "SELECT `variable_name`, `variable_define`, `flag` FROM `LHEMS_flag` WHERE `flag` IS NOT NULL", array("variable_name", "variable_define", "flag"));

$local_simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'next_simulate_timeblock' ", $oneValue);
$global_simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'Global_next_simulate_timeblock' ", $oneValue);
$time_block = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'time_block' ", $oneValue);
$limit_capability = array_fill(0, $time_block, floatval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'Pgridmax' ", $oneValue)));
$community_limit_capability = array_fill(0, $time_block, floatval(sqlFetchRow($conn, "SELECT `value`*(SELECT `value` FROM `BaseParameter` where `parameter_name` = 'householdAmount') FROM `BaseParameter` where `parameter_name` = 'Pgridmax' ", $oneValue)));
$dr_mode = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'dr_mode' ", $oneValue);
$dr_count = sqlFetchRow($conn, "SELECT COUNT(*) FROM `demand_response` ", $oneValue);
if ($dr_mode != 0) {
    $dr_participate_flag = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'dr_participate_flag' ", $oneValue);
    $dr_info = sqlFetchRow($conn, "SELECT * FROM `demand_response` WHERE mode =" .$dr_mode , $aRow);
}
// Base parameter flag
$uncontrollable_load_flag = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'uncontrollable_load_flag' ", $oneValue);
$Global_uncontrollable_load_flag = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'Global_uncontrollable_load_flag' ", $oneValue);
$comfortLevel_flag = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'comfortLevel_flag' ", $oneValue);
$EM_flag = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'ElectricMotor' ", $oneValue);
$EM_discharge_flag = sqlFetchRow($conn, "SELECT `value` FROM `EM_Parameter` where `parameter_name` = 'Motor_can_discharge' ", $oneValue);
$EV_flag = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'ElectricVehicle' ", $oneValue);
$EV_discharge_flag = sqlFetchRow($conn, "SELECT `value` FROM `EV_Parameter` where `parameter_name` = 'Vehicle_can_discharge' ", $oneValue);

// Each chart y-axis max value
$chart_upperLowerLimit_flag = boolval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'chart_upperLowerLimit_flag' ", $oneValue));
if ($chart_upperLowerLimit_flag) {

    $electric_price_upper_limit = floatval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'electric_price_upper_limit' ", $oneValue));
    $ev_chargingUser_nums_upper_limit = floatval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'ev_chargingUser_nums_upper_limit' ", $oneValue));
    $em_n_chargingUser_nums_upper_limit = floatval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'em_n_chargingUser_nums_upper_limit' ", $oneValue));
    $load_model_upper_limit = floatval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'load_model_upper_limit' ", $oneValue));
    $load_model_lower_limit = floatval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'load_model_lower_limit' ", $oneValue));
    $load_model_seperate_upper_limit = floatval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'load_model_seperate_upper_limit' ", $oneValue));
    $load_model_seperate_lower_limit = floatval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'load_model_seperate_lower_limit' ", $oneValue));
    $householdsLoadSum_upper_limit = floatval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'householdsLoadSum_upper_limit' ", $oneValue));
    $each_household_status_upper_limit = floatval(sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` WHERE `parameter_name` = 'each_household_status_upper_limit' ", $oneValue));
}
else {
    
    $electric_price_upper_limit = null;
    $ev_chargingUser_nums_upper_limit = null;
    $em_n_chargingUser_nums_upper_limit = null;
    $load_model_upper_limit = null;
    $load_model_lower_limit = null;
    $load_model_seperate_upper_limit = null;
    $load_model_seperate_lower_limit = null;
    $householdsLoadSum_upper_limit = null;
    $each_household_status_upper_limit = null;
}

?>