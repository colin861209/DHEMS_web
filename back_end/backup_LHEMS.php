<?php
require 'commonSQL_data.php';

$BP = 'backup_BaseParameter';
$CS = 'backup_LHEMS';
$Cost = 'backup_LHEMS_cost';
$UCLoad = 'backup_LHEMS_uncontrollable_load';

$backup = new HEMS($BP, $CS, $UCLoad, $Cost);

$data_array = [

    // fetch_mysql
    "database_name" => $backup->database_name,
    // construct
    "LHEMS_flag" => $backup->LHEMS_flag,
    "time_block" => $backup->time_block,
    "local_simulate_timeblock" => $backup->local_simulate_timeblock,
    "global_simulate_timeblock" => $backup->global_simulate_timeblock,
    "simulate_timeblock" => $backup->simulate_timeblock,
    "electric_price" => $backup->electric_price,
    "limit_capability" => $backup->limit_capability,
    "app_counts" => $backup->app_counts,
    "household_num" => $backup->household_num,
    "uncontrollable_load_flag" => $backup->uncontrollable_load_flag,
    "dr_mode" => $backup->dr_mode,
    "dr_info" => $backup->dr_info,
    "comfortLevel_flag" => $backup->comfortLevel_flag,
    "electric_price_upper_limit" => $backup->electric_price_upper_limit,
    "householdsLoadSum_upper_limit" => $backup->householdsLoadSum_upper_limit,
    "each_household_status_upper_limit" => $backup->each_household_status_upper_limit,
    // cost
    "origin_grid_price" => $backup->origin_grid_price,
    "real_grid_price" => $backup->real_grid_price,
    "public_price" => $backup->public_price,
    "origin_pay_price" => $backup->origin_pay_price,
    "final_pay_price" => $backup->final_pay_price,
    "saving_efficiency" => $backup->saving_efficiency,
    "total_origin_grid_price" => $backup->total_origin_grid_price,
    // getHEMS_LoadListSelect
    "load_list_select" => $backup->arr_LoadSelect,
    "load_list_select_count" => $backup->arr_LoadSelectCount,
    // getHEMS_LoadListArray
    "load_list_array" => $backup->arr_loadList,
    "start" => $backup->start,
    "end" => $backup->end,
    "operation" => $backup->operation,
    "power1" => $backup->power1,
    "power2" => $backup->power2,
    "power3" => $backup->power3,
    "number" => $backup->number,
    "equip_name" => $backup->equip_name,
    // getHEMS_UCLoad
    "uncontrollable_load" => $backup->arr_UCLoad,
    // getHEMS_PowerOfLoadGridEss
    "load_power_sum" => $backup->arr_LoadPowerSum,
    "load_power" => $backup->arr_LoadPower,
    "grid_power" => $backup->arr_GridPower,
    "battery_power" => $backup->arr_EssPower,
    "SOC" => $backup->arr_EssSOC,
    // getHEMS_DRParticipateAndCBL
    "dr_participation" => $backup->arr_HouseholdParticipation,
    "arr_household_CBL" => $backup->arr_HouseholdCBL,
    "household_CBL" => $backup->arr_HouseholdCBLMAX,
    // getHEMS_ComLvTime
    "each_household_startComfortLevel" => $backup->arr_HouseholdComLvStart,
    "each_household_endComfortLevel" => $backup->arr_HouseholdComLvEnd,
];

echo json_encode($data_array);
