<?php
require 'commonSQL_data.php';

$BP = 'backup_BaseParameter';
$CS = 'backup_GHEMS';
$L_UCLoad = 'backup_LHEMS_uncontrollable_load';
$G_UCLoad = 'backup_GHEMS_uncontrollable_load';
$TotalLoad = 'backup_totalLoad';
$EM_BP = 'backup_EM_Parameter';
$EM_number = 'backup_EM_user_number';
$EM_result = 'backup_EM_user_result';
$EV_BP = 'backup_EV_Parameter';
$EV_number = 'backup_EV_user_number';
$EV_result = 'backup_EV_user_result';

$cems = new CEMS($BP, $CS, $L_UCLoad, $G_UCLoad, $TotalLoad, $EM_BP, $EM_number, $EM_result, $EV_BP, $EV_number, $EV_result);

$data_array = [

    // fetch mysql
    "database_name" => $cems->database_name,
    // construct
    "GHEMS_flag" => $cems->GHEMS_flag,
    "local_simulate_timeblock" => $cems->local_simulate_timeblock,
    "global_simulate_timeblock" => $cems->global_simulate_timeblock,
    "electric_price" => $cems->electric_price,
    "simulate_solar" => $cems->simulate_solar,
    "dr_mode" => $cems->dr_mode,
    "dr_info" => $cems->dr_info,
    "ucLoad_flag" => $cems->uncontrollable_load_flag,
    "electric_price_upper_limit" => $cems->electric_price_upper_limit,
    "load_model_upper_limit" => $cems->load_model_upper_limit,
    "load_model_lower_limit" => $cems->load_model_lower_limit,
    "load_model_seperate_upper_limit" => $cems->load_model_seperate_upper_limit,
    "load_model_seperate_lower_limit" => $cems->load_model_seperate_lower_limit,
    // getCEMS_BP
    "Global_ucLoad_flag" => $cems->Global_uncontrollable_load_flag,
    "EM_flag" => $cems->EM_flag,
    "EV_flag" => $cems->EV_flag,
    "total_load_power_sum" => $cems->total_load_power_sum,
    "total_publicLoad_power" => $cems->total_publicLoad_power,
    "total_publicLoad_cost" => $cems->total_publicLoad_cost,
    "taipower_loads_cost" => $cems->taipower_loads_cost,
    "three_level_loads_cost" => $cems->three_level_loads_cost,
    "real_buy_grid_cost" => $cems->real_buy_grid_cost,
    "max_sell_price" => $cems->max_sell_price,
    "min_FC_cost" => $cems->min_FC_cost,
    "consumption" => $cems->consumption,
    "dr_feedbackPrice" => $cems->dr_feedbackPrice,
    "simulate_timeblock" => $cems->simulate_timeblock, 
    // getEMInfo
    "EM_discharge_flag" => $cems->EM_discharge_flag,
    "EM_total_power_sum" => $cems->EM_total_power_sum,
    "EM_total_power_cost" => $cems->EM_total_power_cost,
    "EM_start_departure_SOC" => $cems->EM_start_departure_SOC,
    "EM_MIN_departureSOC" => $cems->EM_MIN_departureSOC,
    "EM_AVG_departureSOC" => $cems->EM_AVG_departureSOC,
    // getEVInfo
    "EV_discharge_flag" => $cems->EV_discharge_flag,
    "EV_total_power_sum" => $cems->EV_total_power_sum,
    "EV_MIN_departureSOC" => $cems->EV_MIN_departureSOC,
    "EV_AVG_departureSOC" => $cems->EV_AVG_departureSOC,
    "EV_total_power_cost" => $cems->EV_total_power_cost,
    "EV_start_departure_SOC" => $cems->EV_start_departure_SOC,
    // getCEMSLoadModel
    "load_model" => $cems->load_model,
    "load_model_seperate" => $cems->load_model_seperate,
    "FC_power" => $cems->arr_FCPower,
    "battery_power" => $cems->arr_EssPower,
    "SOC_value" => $cems->arr_EssSOC,
    "grid_power" => $cems->arr_GridPower,
    "sell_power" => $cems->arr_SellPower,
    // getCEMS_DRCBL
    "arr_community_CBL" => $cems->arr_CommunityCBL,
];

echo json_encode($data_array);
