<?php
require 'commonSQL_data.php';

if ($database_name == "DHEMS_fiftyHousehold") {

    $load_list_array = sqlFetchAssoc($conn, "SELECT 
    `household1_startEndOperationTime`, `household2_startEndOperationTime`, `household3_startEndOperationTime`, `household4_startEndOperationTime`, `household5_startEndOperationTime`, `household6_startEndOperationTime`, `household7_startEndOperationTime`, `household8_startEndOperationTime`, `household9_startEndOperationTime`, `household10_startEndOperationTime`,
    `household11_startEndOperationTime`, `household12_startEndOperationTime`, `household13_startEndOperationTime`, `household14_startEndOperationTime`, `household15_startEndOperationTime`, `household16_startEndOperationTime`, `household17_startEndOperationTime`, `household18_startEndOperationTime`, `household19_startEndOperationTime`, `household20_startEndOperationTime`, 
    `household21_startEndOperationTime`, `household22_startEndOperationTime`, `household23_startEndOperationTime`, `household24_startEndOperationTime`, `household25_startEndOperationTime`, `household26_startEndOperationTime`, `household27_startEndOperationTime`, `household28_startEndOperationTime`, `household29_startEndOperationTime`, `household30_startEndOperationTime`, 
    `household31_startEndOperationTime`, `household32_startEndOperationTime`, `household33_startEndOperationTime`, `household34_startEndOperationTime`, `household35_startEndOperationTime`, `household36_startEndOperationTime`, `household37_startEndOperationTime`, `household38_startEndOperationTime`, `household39_startEndOperationTime`, `household40_startEndOperationTime`, 
    `household41_startEndOperationTime`, `household42_startEndOperationTime`, `household43_startEndOperationTime`, `household44_startEndOperationTime`, `household45_startEndOperationTime`, `household46_startEndOperationTime`, `household47_startEndOperationTime`, `household48_startEndOperationTime`, `household49_startEndOperationTime`, `household50_startEndOperationTime`, 
    `power1`, `power2`, `power3`, `number`, `equip_name` 
    FROM load_list", array(
        "household1_startEndOperationTime", "household2_startEndOperationTime", "household3_startEndOperationTime", "household4_startEndOperationTime", "household5_startEndOperationTime", "household6_startEndOperationTime", "household7_startEndOperationTime", "household8_startEndOperationTime", "household9_startEndOperationTime", "household10_startEndOperationTime",
        "household11_startEndOperationTime", "household12_startEndOperationTime", "household13_startEndOperationTime", "household14_startEndOperationTime", "household15_startEndOperationTime", "household16_startEndOperationTime", "household17_startEndOperationTime",  "household18_startEndOperationTime", "household19_startEndOperationTime", "household20_startEndOperationTime",
        "household21_startEndOperationTime", "household22_startEndOperationTime", "household23_startEndOperationTime", "household24_startEndOperationTime", "household25_startEndOperationTime", "household26_startEndOperationTime", "household27_startEndOperationTime", "household28_startEndOperationTime", "household29_startEndOperationTime", "household30_startEndOperationTime",
        "household31_startEndOperationTime", "household32_startEndOperationTime", "household33_startEndOperationTime", "household34_startEndOperationTime", "household35_startEndOperationTime", "household36_startEndOperationTime", "household37_startEndOperationTime", "household38_startEndOperationTime", "household39_startEndOperationTime", "household40_startEndOperationTime",
        "household41_startEndOperationTime", "household42_startEndOperationTime", "household43_startEndOperationTime", "household44_startEndOperationTime", "household45_startEndOperationTime", "household46_startEndOperationTime", "household47_startEndOperationTime", "household48_startEndOperationTime", "household49_startEndOperationTime", "household50_startEndOperationTime", 
        "power1", "power2", "power3", "number", "equip_name"
    ));
        
    $uncontrollable_load = sqlFetchAssoc($conn, "SELECT 
    `household1`, `household2`, `household3`, `household4`, `household5`, `household6`, `household7`, `household8`, `household9`, `household10`,
    `household11`, `household12`, `household13`, `household14`, `household15`, `household16`, `household17`, `household18`, `household19`, `household20`,
    `household21`, `household22`, `household23`, `household24`, `household25`, `household26`, `household27`, `household28`, `household29`, `household30`,
    `household31`, `household32`, `household33`, `household34`, `household35`, `household36`, `household37`, `household38`, `household39`, `household40`,
    `household41`, `household42`, `household43`, `household44`, `household45`, `household46`, `household47`, `household48`, `household49`, `household50` 
    FROM LHEMS_uncontrollable_load", array(
        "household1", "household2", "household3", "household4", "household5", "household6", "household7", "household8", "household9", "household10", 
        "household11", "household12", "household13", "household14", "household15", "household16", "household17", "household18", "household19", "household20", 
        "household21", "household22", "household23", "household24", "household25", "household26", "household27", "household28", "household29", "household30", 
        "household31", "household32", "household33", "household34", "household35", "household36", "household37", "household38", "household39", "household40", 
        "household41", "household42", "household43", "household44", "household45", "household46", "household47", "household48", "household49", "household50" 
    ));
}
else {

    $load_list_array = sqlFetchAssoc($conn, "SELECT household1_startEndOperationTime, household2_startEndOperationTime, household3_startEndOperationTime, household4_startEndOperationTime, household5_startEndOperationTime, power1, power2, power3, number, equip_name FROM load_list", array("household1_startEndOperationTime", "household2_startEndOperationTime", "household3_startEndOperationTime", "household4_startEndOperationTime", "household5_startEndOperationTime", "power1", "power2", "power3", "number", "equip_name"));
    $uncontrollable_load = sqlFetchAssoc($conn, "SELECT `household1`, `household2`, `household3`, `household4`, `household5` FROM LHEMS_uncontrollable_load", array("household1", "household2", "household3", "household4", "household5"));
}

# base parameter
$app_counts = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'app_counts' ", $oneValue);
$interrupt_num = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'interrupt_num' ", $oneValue);
$uninterrupt_num = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'uninterrupt_num' ", $oneValue);
$household_num = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'householdAmount' ", $oneValue);
$variable_num = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'local_variable_num' ", $oneValue);
$simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'next_simulate_timeblock' ", $oneValue);
$dr_mode = sqlFetchRow($conn, "SELECT `value` FROM `backup_BaseParameter` where `parameter_name` = 'dr_mode' ", $oneValue);
if ($dr_mode != 0)
    $dr_info = sqlFetchRow($conn, "SELECT * FROM `demand_response` WHERE mode =" .$dr_mode , $aRow);
    
$household_id = sqlFetchAssoc($conn, "SELECT `household_id` FROM `backup_LHEMS` ORDER BY `household_id`, `control_id` ASC ", array("household_id"));

$grid_power = [];
$load_power = [];
for ($i=0; $i < $household_num; $i++) { 
    
    $interrupt_status = sqlFetchRow($conn, "SELECT * FROM `backup_LHEMS` WHERE (equip_name LIKE '%interrupt%' OR equip_name LIKE 'varyingPsi%') AND household_id =" .($i+1). " ORDER BY `household_id`, `control_id` ASC ", $controlStatusResult);
    array_push($load_power, $interrupt_status);
    $grid_power_tmp = sqlFetchRow($conn, "SELECT * FROM `backup_LHEMS` WHERE equip_name = 'Pgrid' AND household_id =" .($i+1). " ORDER BY `household_id`, `control_id` ASC ", $aRow);
    array_splice($grid_power_tmp, 0, 1);
    array_splice($grid_power_tmp, 96, count($grid_power_tmp)-1);
    array_push($grid_power, array_map('floatval', $grid_power_tmp));
}

mysqli_close($conn);

for ($j = 0; $j < count($uncontrollable_load); $j++) {

    for ($i = 0; $i < $time_block; $i++) {

        $uncontrollable_load[$j][$i] = floatval($uncontrollable_load[$j][$i]);
    }
}

// load_list_array
for ($i = 0; $i < $app_counts; $i++) {

    for ($j = 0; $j < $household_num; $j++) {

        list($start_tmp, $end_tmp, $operation_tmp) = explode("~", $load_list_array[$j][$i]);
        $start[$j][$i] = intval($start_tmp);
        $end[$j][$i] = intval($end_tmp);
        $operation[$j][$i] = intval($operation_tmp);
    }
    $power1[$i] = floatval($load_list_array[$household_num][$i]);
    $power2[$i] = floatval($load_list_array[$household_num + 1][$i]);
    $power3[$i] = floatval($load_list_array[$household_num + 2][$i]);
    $number[$i] = $load_list_array[$household_num + 3][$i];
    $equip_name[$i] = $load_list_array[$household_num + 4][$i];
}

for ($i = 0; $i < $household_num; $i++) {

    for ($y = 0; $y < $time_block; $y++) {
        
        for ($u = 0; $u < $interrupt_num + $uninterrupt_num; $u++) {

            $load_power[$i][$u][$y] = $power1[$u] * $load_power[$i][$u][$y];
        }
        for ($u=0; $u < $app_counts; $u++) { 
            
            $load_power_sum[$i][$y] += $load_power[$i][$u][$y];
        }
    }
}

for ($i = 0; $i < $household_num; $i++) {

    for ($y = 0; $y < $time_block; $y++) {

        $battery_power[$i][] = $optimize_result[$i * $variable_num + $app_counts + 1][$y];
        $SOC[$i][] = $optimize_result[$i * $variable_num + $app_counts + 4][$y];
    }
}


$data_array = [

    "simulate_timeblock" => floatval($simulate_timeblock),
    "electric_price" => $electric_price,
    "limit_capability" => $limit_capability,
    "app_counts" => intval($app_counts),
    "household_num" => intval($household_num),
    "start" => $start,
    "end" => $end,
    "operation" => $operation,
    "power1" => $power1,
    "power2" => $power2,
    "power3" => $power3,
    "load_list_array" => $load_list_array,
    "number" => $number,
    "equip_name" => $equip_name,
    "optimize_result" => $optimize_result,
    "load_power_sum" => $load_power_sum,
    "uncontrollable_load" => $uncontrollable_load,
    "load_power" => $load_power,
    "grid_power" => $grid_power,
    "battery_power" => $battery_power,
    "SOC" => $SOC,
    "LHEMS_flag" => $LHEMS_flag,
    "uncontrollable_load_flag" => $uncontrollable_load_flag,
    "dr_mode" => $dr_mode,
    "dr_info" => $dr_info,
    "database_name" => $database_name
];

echo json_encode($data_array);