<?php
require 'commonSQL_data.php';

function updateSQL($conn, $table, $target_col, $target_value, $condition_col, $condition_value) {

    $sql = "UPDATE `$table` SET `$target_col` = '$target_value' WHERE `$condition_col` = '$condition_value'";    
    mysqli_query($conn, $sql);
}

function multiply(array $array, float $factor) {

    foreach ($array as $key => $value) {
        $array[$key] = round($value * $factor);
    }
    return $array;
}

function create_wholeDay_userChargingNumber($conn, $table) {

    try {
        $nfsf_user_number=[]; $type_all=[];
        mysqli_query($conn, "TRUNCATE TABLE `$table`");
        $user_number_tmp = sqlFetchAssoc($conn, "SELECT `user_number`, `fast_user_number`, `super_fast_user_number` FROM EV_user_number", array("user_number", "fast_user_number", "super_fast_user_number"));
        $percent = sqlFetchAssoc($conn, "SELECT `percent` FROM `EV_motor_type`", array("percent"));
        $charging_power = sqlFetchAssoc($conn, "SELECT `power` FROM `EV_motor_type`", array("power"));
        
        for ($i=0; $i < count($user_number_tmp); $i++) 
            array_push($nfsf_user_number, array_map('intval', $user_number_tmp[$i]));
        
        for ($i=0; $i < count($percent); $i++) { 
            switch ($charging_power[$i]) {
                case '12':
                    $type_all[$i] = multiply($nfsf_user_number[2], $percent[$i]/100);
                    break;
                case '4.8':
                    $type_all[$i] = multiply($nfsf_user_number[1], $percent[$i]/100);
                    break;
                default:
                    $type_all[$i] = multiply($nfsf_user_number[0], $percent[$i]/100);
                    break;
            }    
        }
        for ($i=0; $i < count($type_all[0]); $i++) { 
            
            $sql = "INSERT INTO `$table` (`timeblock`, `type_0`, `type_1`, `type_2`, `type_3`, `type_4`, `type_5`, `type_6`, `type_7`, `type_8`, `type_9`) VALUES ('".$i."','".$type_all[0][$i]."','".$type_all[1][$i]."','".$type_all[2][$i]."','".$type_all[3][$i]."','".$type_all[4][$i]."','".$type_all[5][$i]."','".$type_all[6][$i]."','".$type_all[7][$i]."','".$type_all[8][$i]."','".$type_all[9][$i]."');";
            mysqli_query($conn, $sql);
        }
        return "success";

    } catch (\Throwable $th) {
        return $th;
    }
}

function update_EVParameter_totalChargingPole($conn, $old_totalPoles, $old_superFastPoles, $old_fastPoles, $old_normalPoles, $newParameter) {
    
    $new_sfPole = $newParameter['value'][array_search("Super_Fast_Charging_Pole", $newParameter['name'], true)];
    $new_fPole = $newParameter['value'][array_search("Fast_Charging_Pole", $newParameter['name'], true)];
    $new_nPole = $newParameter['value'][array_search("Normal_Charging_Pole", $newParameter['name'], true)];
    if ($new_sfPole != $old_superFastPoles || $new_fPole != $old_fastPoles || $new_nPole != $old_normalPoles) {
        $status = "Pole amount change";
    }
    if ($new_sfPole+$new_fPole+$new_nPole != $old_totalPoles) {
        
        updateSQL($conn, $newParameter['table'], "value", ($new_sfPole+$new_fPole+$new_nPole), 'parameter_name', "Total_Charging_Pole");
        $new_totalPoles = sqlFetchRow($conn, "SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Total_Charging_Pole'", "oneValue");
        if ($new_sfPole+$new_fPole+$new_nPole == $new_totalPoles)
            $status = "Total pole amount change";
        else
            $status = "something went wrong";
    }
    return [$status, $new_totalPoles, $new_sfPole, $new_fPole, $new_nPole];
}

function create_chargingPole($conn, $table, $totalPoles, $sfPole, $fPole, $nPole) {

    try {
        mysqli_query($conn, "TRUNCATE TABLE `$table`");
        for ($i=0; $i < $totalPoles; $i++) { 
            
            $sql = "INSERT INTO `$table` (`Pole_ID`, `number`, `sure`, `charging_status`, `discharge_status`, `full`, `wait`, `SOC`, `BAT_CAP`, `V_battery`, `I_battery`, `Arrived_Timeblock`, `Start_timeblock`, `Departure_timeblock`) VALUES ('" .($i+1). "', NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
            mysqli_query($conn, $sql);
        }
        if ($nPole != 0) {
            $sql = "UPDATE `$table` SET `V_battery` = '48', `I_battery` = '12.5' WHERE `Pole_ID` <= '$nPole'";
            mysqli_query($conn, $sql);
        }
        if ($fPole != 0) {
            $sql = "UPDATE `$table` SET `V_battery` = '87.6', `I_battery` = '54.79' WHERE `Pole_ID` > '$nPole' AND `Pole_ID` <= '".($nPole+$fPole)."'";
            mysqli_query($conn, $sql);
        }
        if ($sfPole != 0) {
            $sql = "UPDATE `$table` SET `V_battery` = '87.6', `I_battery` = '136.99' WHERE Pole_ID > '".($nPole+$fPole)."' AND '".($nPole+$fPole+$sfPole)."'";
            mysqli_query($conn, $sql);
        }
        return "success";

    } catch (\Throwable $th) {
        return $th;
    }
}

$newParameter = $_POST['phpReceive'];

switch ($newParameter['table']) {
    case 'EV_motor_type':
        for ($i=0; $i < count($newParameter['id']); $i++) {

            updateSQL($conn, $newParameter['table'], "percent", $newParameter['percent_value'][$i], 'id', $newParameter['id'][$i]);
            
            $value = sqlFetchRow($conn, "SELECT `percent` FROM `" .$newParameter['table']. "` WHERE `id` = '" .$newParameter['id'][$i]. "'", $oneValue);
            
            if ($value == $newParameter['percent_value'][$i])
                $status = "success";
            else
                $status = "something went wrong";
        }
        $status = create_wholeDay_userChargingNumber($conn, "EV_wholeDay_userChargingNumber");
        break;
    
    default:
        // EV_Parameter
        $old_totalPoles = sqlFetchRow($conn, "SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Total_Charging_Pole'", $oneValue);
        $old_superFastPoles = sqlFetchRow($conn, "SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Super_Fast_Charging_Pole'", $oneValue);
        $old_FastPoles = sqlFetchRow($conn, "SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Fast_Charging_Pole'", $oneValue);
        $old_normalPoles = sqlFetchRow($conn, "SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Normal_Charging_Pole'", $oneValue);
        for ($i=0; $i < count($newParameter['value']); $i++) {
            
            updateSQL($conn, $newParameter['table'], "value", $newParameter['value'][$i], 'parameter_name', $newParameter['name'][$i]);
            
            $value = sqlFetchRow($conn, "SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = '" .$newParameter['name'][$i]. "'", $oneValue);
            
            if ($value == $newParameter['value'][$i])
                $status = "success";
            else
                $status = "something went wrong";
        }
        [$change_status, $totalPoles, $sfPole, $fPole, $nPole] = update_EVParameter_totalChargingPole($conn, $old_totalPoles, $old_superFastPoles, $old_FastPoles, $old_normalPoles, $newParameter);
        if ($change_status == "Pole amount change" ||$change_status == "Total pole amount change") {

            $status = create_chargingPole($conn, "EV_Pole", $totalPoles, $sfPole, $fPole, $nPole);
        }
        break;
}


echo json_encode(array(
    "status" => $status,
    "a" => [$change_status, $totalPoles, $sfPole, $fPole, $nPole],
    "database_name" => $database_name
));
mysqli_close($conn);

?>