<?php
require 'commonSQL_data.php';

$load_list_array = sqlFetchAssoc($conn, "SELECT household1_startEndOperationTime, household2_startEndOperationTime, household3_startEndOperationTime, household4_startEndOperationTime, household5_startEndOperationTime, power1, power2, power3, block1, block2, block3, number, equip_name FROM load_list", array("household1_startEndOperationTime", "household2_startEndOperationTime", "household3_startEndOperationTime", "household4_startEndOperationTime", "household5_startEndOperationTime", "power1", "power2", "power3", "block1", "block2", "block3", "number", "equip_name"));

$uncontrollable_load = sqlFetchAssoc($conn, "SELECT `household1`, `household2`, `household3`, `household4`, `household5` FROM LHEMS_uncontrollable_load", array("household1", "household2", "household3", "household4", "household5"));

# base parameter
$app_counts = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'app_counts' ", $oneValue);
$interrupt_num = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'interrupt_num' ", $oneValue);
$uninterrupt_num = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'uninterrupt_num' ", $oneValue);
$household_num = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'householdAmount' ", $oneValue);
$variable_num = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'variable_num' ", $oneValue);
$simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `BaseParameter` where `parameter_name` = 'next_simulate_timeblock' ", $oneValue);

$household_id = sqlFetchAssoc($conn, "SELECT `household_id` FROM `LHEMS_control_status` ", array("household_id"));
$optimize_result = sqlFetchRow($conn, "SELECT * FROM `LHEMS_control_status` ", $controlStatusResult);
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
    $power1[$i] = floatval($load_list_array[5][$i]);
    $power2[$i] = floatval($load_list_array[6][$i]);
    $power3[$i] = floatval($load_list_array[7][$i]);
    $block1[$i] = intval($load_list_array[8][$i] * 4);
    $block2[$i] = intval($load_list_array[9][$i] * 4);
    $block3[$i] = intval($load_list_array[10][$i] * 4);
    $number[$i] = intval($load_list_array[11][$i]);
    $equip_name[$i] = $load_list_array[12][$i];
}

for ($i = 0; $i < $household_num; $i++) {

    for ($u = 0; $u < $app_counts; $u++) {

        for ($y = 0; $y < $time_block; $y++) {

            if ($u < $interrupt_num + $uninterrupt_num)
                $load_power[$i][$u][] = $power1[$u] * $optimize_result[$u + $i * $variable_num][$y];
            else
                $load_power[$i][$u][] =  $optimize_result[($i + 1) * $variable_num - 1][$y];
            // $load_power[$u][] = $optimize_result[array_search("varyingPsi1", $variable_name, true)][$y];

            $load_power_sum[$i][$y] += $load_power[$i][$u][$y];
        }
    }
}

$data_array = [

    "simulate_timeblock" => $simulate_timeblock,
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
    "block1" => $block1,
    "block2" => $block2,
    "block3" => $block3,
    "number" => $number,
    "equip_name" => $equip_name,
    "optimize_result" => $optimize_result,
    "load_power_sum" => $load_power_sum,
    "uncontrollable_load" => $uncontrollable_load,
    "load_power" => $load_power
];

echo json_encode($data_array);
