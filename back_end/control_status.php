<?php
require 'fetch_mysql.php';

$electric_price_array = sqlFetchAssoc($conn, "SELECT `price_value` FROM `price` ", array("price_value"));
$variable_name = sqlFetchAssoc($conn, "SELECT `equip_name` FROM `control_status` ", array("equip_name"));
$cost_name = sqlFetchAssoc($conn, "SELECT `cost_name` FROM `cost` ", array("cost_name"));
$solor_fake = sqlFetchAssoc($conn, "SELECT `value` FROM `solar_day` ", array("value"));

$load_list_array = sqlFetchAssoc($conn, "SELECT start_time, end_time, operation_time, power1, power2, power3, block1, block2, block3, number, equip_name  FROM load_list ", array("start_time","end_time", "operation_time", "power1", "power2", "power3", "block1", "block2", "block3", "number", "equip_name"));

$interrupt_num = sqlFetchRow($conn, "SELECT count(*) AS numcols FROM load_list WHERE group_id=1 ", $oneValue);
$uninterrupt_num = sqlFetchRow($conn, "SELECT count(*) AS numcols FROM load_list WHERE group_id=2 ", $oneValue);
$varying_num = sqlFetchRow($conn, "SELECT count(*) AS numcols FROM load_list WHERE group_id=3 ", $oneValue);

$limit_power = sqlFetchRow($conn, "SELECT `value` FROM `LP_BASE_PARM` where `parameter_id` = 13 ", $oneValue);
$taipower_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `LP_BASE_PARM` where `parameter_id` = 18 ", $oneValue);
$three_level_loads_cost = sqlFetchRow($conn, "SELECT `value` FROM `LP_BASE_PARM` where `parameter_id` = 19 ", $oneValue);
$real_buy_grid_cost = sqlFetchRow($conn, "SELECT `value` FROM `LP_BASE_PARM` where `parameter_id` = 20 ", $oneValue);
$min_FC_cost = sqlFetchRow($conn, "SELECT `value` FROM `LP_BASE_PARM` where `parameter_id` = 22 ", $oneValue);
$consumption = sqlFetchRow($conn, "SELECT `value` FROM `LP_BASE_PARM` where `parameter_id` = 23 ", $oneValue);

$simulate_timeblock = sqlFetchRow($conn, "SELECT `value` FROM `LP_BASE_PARM` where `parameter_id` = 28 ", $oneValue);

$load_power_sum = sqlFetchAssoc($conn, "SELECT `load_model` FROM `totalLoad_model` ", array("load_model"));
$load_status_array = sqlFetchRow($conn, "SELECT * FROM `control_status` ", $controlStatusResult);
$cost_array = sqlFetchRow($conn, "SELECT * FROM `cost` ", $controlStatusResult);
mysqli_close($conn);

for($y=0;$y<96;$y++)
    $limit_capability[$y] = floatval($limit_power);

// electric_price_array
for($y=0; $y<24; $y++) {

    for ($i=0;$i<4;$i++)
        $electric_price[4*$y+$i] = floatval($electric_price_array[$y]);
}

for($y=0; $y<96; $y++)
    $simulate_solar[$y] = floatval($solor_fake[$y]); 

for($y=0; $y<96; $y++)   
    $load_power_sum[$y] = floatval($load_power_sum[$y]); 

for($y=0; $y<96; $y++)
    $total_load_power_sum += $cost_array[array_search("total_load_power", $cost_name, true)][$y]; 
    
// load_list_array
for($i=0; $i<$interrupt_num + $uninterrupt_num + $varying_num; $i++) {

    $start_time[$i] = intval($load_list_array[0][$i]);
    $end_time[$i] = intval($load_list_array[1][$i]);
    $operation_time[$i] = intval($load_list_array[2][$i]);
    $power1[$i] = floatval($load_list_array[3][$i]);
    $power2[$i] = floatval($load_list_array[4][$i]);
    $power3[$i] = floatval($load_list_array[5][$i]);
    $block1[$i] = intval($load_list_array[6][$i] * 4);
    $block2[$i] = intval($load_list_array[7][$i] * 4);
    $block3[$i] = intval($load_list_array[8][$i] * 4);
    $number[$i] = intval($load_list_array[9][$i]);
    $equip_name[$i] = $load_list_array[10][$i];

}

for ($i=0; $i<$varying_num; $i++) {

    for($y=0; $y<$block1[$interrupt_num+$uninterrupt_num]; $y++)
        $varying_power[$i][$y] = $power1[$interrupt_num + $uninterrupt_num];
    
    for($y=0; $y<$block2[$interrupt_num+$uninterrupt_num]; $y++)
        $varying_power[$i][$y+$block1[$interrupt_num+$uninterrupt_num]] = $power2[$interrupt_num + $uninterrupt_num];
    
    for($y=0; $y<$block3[$interrupt_num+$uninterrupt_num]; $y++)
        $varying_power[$i][$y+$block1[$interrupt_num+$uninterrupt_num]+$block2[$interrupt_num+$uninterrupt_num]] = $power3[$interrupt_num + $uninterrupt_num];
    
}
// load_status_array
for($u=0; $u<$interrupt_num+$uninterrupt_num+$varying_num; $u++){

    if($u<$interrupt_num+$uninterrupt_num){

        for($y=0;$y<96;$y++)
            $load_power[$u][] = $power1[$u] * $load_status_array[$u][$y];

    } 
    else {

        // show the varying power array status
        for($y=0;$y<96;$y++) 
            $load_power[$u][] = $load_status_array[array_search("varyingPsi1", $variable_name, true)][$y];
            
    }
    
}
// load power sum
// for ($i=0; $i < 96; $i++) { 
    
//     for ($u=0; $u < count($load_power); $u++)
//         $load_power_sum[$i] += $load_power[$u][$i];
// }

# Pbattery    
if (array_search("Pess", $variable_name, true) != false) 
        $battery_power = $load_status_array[array_search("Pess", $variable_name, true)];

else
    $battery_power = 0.0;

# Pgrid
// if (array_search("Pgrid", $variable_name, true) != false)
//     $grid_power = floatval($load_status_array[array_search("Pgrid", $variable_name, true)]);
// else
//     $grid_power[$i] = 0.0;

# SOC 
// if (array_search("SOC", $variable_name, true) != false) 
//     $SOC_value = $load_status_array[array_search("SOC", $variable_name, true)];
// else
//     $SOC_value = 0.0;

# fuel cell 

// if (array_search("Pfc", $variable_name, true) != false) 
//     $FC_power = $load_status_array[array_search("Pfc", $variable_name, true)]; 
// else
//     $FC_power[$i] = 0.0;

$data_array = [
    "interrupt_num"=>$interrupt_num,
    "uninterrupt_num"=>$uninterrupt_num,
    "varying_num"=>$varying_num,
    "total_load_power_sum"=>$total_load_power_sum,
    "taipower_loads_cost"=>floatval($taipower_loads_cost),
    "three_level_loads_cost"=>floatval($three_level_loads_cost),
    "real_buy_grid_cost"=>floatval($real_buy_grid_cost),
    "min_FC_cost"=>floatval($min_FC_cost),
    "consumption"=>floatval($consumption),
    "electric_price"=>$electric_price,
    "start_time"=>$start_time,
    "end_time"=>$end_time,
    "operation_time"=>$operation_time,
    "power1"=>$power1,
    "power2"=>$power2,
    "power3"=>$power3,
    "block1"=>$block1,
    "block2"=>$block2,
    "block3"=>$block3,
    "limit_capability"=>$limit_capability,
    "load_power"=>$load_power,
    "equip_name"=>$equip_name,
    "load_num"=>$number,
    "simulate_solar"=>$simulate_solar,
    "battery_power"=>$load_status_array[array_search("Pess", $variable_name, true)],
    "SOC_value"=>$$load_status_array[array_search("SOC", $variable_name, true)],
    "grid_power"=>$load_status_array[array_search("Pgrid", $variable_name, true)],
    "simulate_timeblock"=>intval($simulate_timeblock),
    "FC_power"=>$FC_power,
    "load_power_sum"=>$load_power_sum,
    "load_status_array"=> $load_status_array,
    "test"=>$load_status_array[array_search("Pgrid", $variable_name, true)]
];

    
echo json_encode($data_array);


?>