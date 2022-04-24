<?php
require 'commonSQL_data.php';

$BP = 'BaseParameter';
$CS = 'LHEMS_control_status';
$Cost = 'LHEMS_cost';
$UCLoad = 'LHEMS_uncontrollable_load';

$hems = new HEMS($BP, $CS, $UCLoad, $Cost);

$data_array = [

    // fetch_mysql
    "database_name" => $hems->database_name,
    // construct
    "LHEMS_flag" => $hems->LHEMS_flag,
    "time_block" => $hems->time_block,
    "local_simulate_timeblock" => $hems->local_simulate_timeblock,
    "global_simulate_timeblock" => $hems->global_simulate_timeblock,
    "simulate_timeblock" => $hems->simulate_timeblock,
    "electric_price" => $hems->electric_price,
    "limit_capability" => $hems->limit_capability,
    "app_counts" => $hems->app_counts,
    "household_num" => $hems->household_num,
    "uncontrollable_load_flag" => $hems->uncontrollable_load_flag,
    "dr_mode" => $hems->dr_mode,
    "dr_info" => $hems->dr_info,
    "comfortLevel_flag" => $hems->comfortLevel_flag,
    "electric_price_upper_limit" => $hems->electric_price_upper_limit,
    "householdsLoadSum_upper_limit" => $hems->householdsLoadSum_upper_limit,
    "each_household_status_upper_limit" => $hems->each_household_status_upper_limit,
    // cost
    "origin_grid_price" => $hems->origin_grid_price,
    "real_grid_price" => $hems->real_grid_price,
    "public_price" => $hems->public_price,
    "origin_pay_price" => $hems->origin_pay_price,
    "final_pay_price" => $hems->final_pay_price,
    "saving_efficiency" => $hems->saving_efficiency,
    "total_origin_grid_price" => $hems->total_origin_grid_price,
    // getHEMS_LoadListSelect
    "load_list_select" => $hems->arr_LoadSelect,
    "load_list_select_count" => $hems->arr_LoadSelectCount,
    // getHEMS_LoadListArray
    "load_list_array" => $hems->arr_loadList,
    "start" => $hems->start,
    "end" => $hems->end,
    "operation" => $hems->operation,
    "power1" => $hems->power1,
    "power2" => $hems->power2,
    "power3" => $hems->power3,
    "number" => $hems->number,
    "equip_name" => $hems->equip_name,
    // getHEMS_UCLoad
    "uncontrollable_load" => $hems->arr_UCLoad,
    // getHEMS_PowerOfLoadGridEss
    "load_power_sum" => $hems->arr_LoadPowerSum,
    "load_power" => $hems->arr_LoadPower,
    "grid_power" => $hems->arr_GridPower,
    "battery_power" => $hems->arr_EssPower,
    "SOC" => $hems->arr_EssSOC,
    // getHEMS_DRParticipateAndCBL
    "dr_participation" => $hems->arr_HouseholdParticipation,
    "arr_household_CBL" => $hems->arr_HouseholdCBL,
    "household_CBL" => $hems->arr_HouseholdCBLMAX,
    // getHEMS_ComLvTime
    "each_household_startComfortLevel" => $hems->arr_HouseholdComLvStart,
    "each_household_endComfortLevel" => $hems->arr_HouseholdComLvEnd,
];

echo json_encode($data_array);
