<?php
require 'databases_name.php';

class SQLQuery {

    private $conn;
    private $sql = "";
    private $key = "";
    public $oneValue = "oneValue";
    public $aRow = "aRow";
    public $controlStatusResult = "controlStatusResult";
    public $emChargeDischarge = "emChargeDischarge";
    public $database_name = "";

    function __construct() {
        
        $this->database_name = $_SESSION['database'];
        $this->conn = new mysqli('140.124.42.65','root','fuzzy314', $this->database_name, '3306');
        mysqli_set_charset($this->conn, 'utf8');

        if ($this->conn -> connect_errno) {

            echo "Failed to connect MySQL: ". mysqli_connect_error();
            exit();
        }
        error_reporting(E_ALL & ~E_NOTICE);
    }

    function __destruct() { mysqli_close($this->conn); }

    // note by Colin Wang in 2021/2/27
    //////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////
    //////                                                                                  //////
    //////  function sqlFetchRow : Get value by row                                         //////
    //////  case : oneValue             return a value with 'string' type                   //////
    //////  case : aRow                 return an array with 'string' type                  //////
    //////  case : controlStatusResult  return multi-dimensional array with 'float' type    //////
    //////     controlStatusResult is a fixed case for getting each rows A0 ~ A95 data      //////
    //////     can also be used in the same structure tables like 'cost', 'real_status'     //////
    //////                                                                                  //////
    //////////////////////////////////////////////////////////////////////////////////////////////
    //////                                                                                  //////
    //////  function sqlFetchAssoc : Get value by column                                    //////
    //////  'key' is an array, should put data which is/are same as the column name(s)      //////
    //////  if 'key' only have one data ex: array("{column_name1}"),                        //////
    //////   return one dimensional array                                                   //////
    //////  if 'key' have more than one data ex: array("{column_name1}", "{column_name2}")  //////
    //////   return multi-dimensional array                                                 //////
    //////                                                                                  //////
    //////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////


    public function sqlFetchRow($sql, $key) {
        
        $this->sql = $sql;
        $this->key = $key;
        switch ($this->key) {
            case "oneValue":
                $result = mysqli_query($this->conn, $this->sql);
                $row = mysqli_fetch_row($result);
                $value = $row[0];
                mysqli_free_result($result);
                return $value;
                break;

            case "aRow":
                $result = mysqli_query($this->conn, $this->sql);
                while ($row = mysqli_fetch_row($result)) {
                    $array = $row;
                }
                mysqli_free_result($result);
                return $array;
                break;
            
            case "controlStatusResult":
                $k = 0;
                $result = mysqli_query($this->conn, $this->sql);
                while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

                    for($i = 1; $i < 97; $i++) {
                        
                        $array[$k][] = floatval($row[$i]);
                    }
                    $k++;
                }
                mysqli_free_result($result);
                return $array;
                break;
            case "emChargeDischarge":
                $k = 0;
                $result = mysqli_query($this->conn, $this->sql);
                while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

                    for($i = 0; $i < 97; $i++) {
                        
                        $array[$k][] = ($row[$i]);
                    }
                    $k++;
                }
                mysqli_free_result($result);
                return $array;
                break;
            default:
                echo "tap key";
        }
    }

    public function sqlFetchAssoc($sql, $key) {

        $this->sql = $sql;
        $this->key = $key;
        $result = mysqli_query($this->conn, $this->sql);
        
        if (count($key) == 1) {

            for($i = 0; $i < mysqli_num_rows($result); $i++) {

                $row = mysqli_fetch_assoc($result);
                $array[$i] = $row[$key[0]];
            }
        } 
        elseif (count($key) > 1) {

            for($i = 0; $i < mysqli_num_rows($result); $i++) {
                
                $row = mysqli_fetch_assoc($result);
                for($k = 0; $k < count($key); $k++) {
                    
                    $array[$k][$i] = $row[$key[$k]];
                }
            }
        }
        mysqli_free_result($result);
        return $array;
    }

    // return array multiple a value 
    public function multiply(array $array, float $factor) {
    
        foreach ($array as $key => $value) {
            $array[$key] = round($value * $factor);
        }
        return $array;
    }
        
    private function updateSQL($table, $target_col, $target_value, $condition_col, $condition_value) {

        $sql = "UPDATE `$table` SET `$target_col` = '$target_value' WHERE `$condition_col` = '$condition_value'";    
        mysqli_query($this->conn, $sql);
    }
    
    // BP Page
    public function UpdateBaseParameter($newParm) {
        
        for ($i=0; $i < count($newParm['name']); $i++) { 
            $this->updateSQL($newParm['table'], "value", $newParm['baseParameter'][$i], "parameter_name", $newParm['name'][$i]);
            $value = $this->sqlFetchRow("SELECT `value` FROM `" .$newParm['table']. "` WHERE `parameter_name` = '" .$newParm['name'][$i]. "'", $this->oneValue);

            if ($value == $newParm['baseParameter'][$i]) {
                $status = "success";
            }
            else {
                $status = "something went wrong";
                break;
            }
        }
        return $status;
    }

    // Flags Table
    public function UpdateFlags($newFlag) {

        for ($i=0; $i < count($newFlag['name']); $i++) { 
            
            $this->updateSQL($newFlag['table'], "flag", $newFlag['flag'][$i], "variable_name", $newFlag['name'][$i]);
        }

        $flag = $this->sqlFetchAssoc("SELECT `flag` FROM `" .$newFlag['table'] ."` WHERE `flag` IS NOT NULL", array("flag"));
        if ($flag == $newFlag['flag']) {
            $status = "success";
        }
        else {
            $status = "something went wrong";
        }
        return $status;
    }

    // EMEV Page
    private function create_wholeDay_userChargingNumber($table, $emev_type) {
    
        try {
            $nfsf_user_number=[]; $type_all=[]; $sql=""; $sql_array=array();
            mysqli_query($this->conn, "TRUNCATE TABLE `$table`");
            switch ($emev_type) {
                case 'EM_motor_type':
                    $sql = "SELECT `user_number`, `fast_user_number`, `super_fast_user_number` FROM EM_user_number";
                    $sql_array = array("user_number", "fast_user_number", "super_fast_user_number");
                    $user_number_tmp = $this->sqlFetchAssoc($sql, $sql_array);
                    for ($i=0; $i < count($user_number_tmp); $i++) {
                        array_push($nfsf_user_number, array_map('intval', $user_number_tmp[$i]));
                    }
                    break;
                case 'EV_motor_type':
                    $sql = "SELECT `user_number` FROM EV_user_number";
                    $sql_array = array("user_number");
                    $user_number_tmp = $this->sqlFetchAssoc($sql, $sql_array);
                    array_push($nfsf_user_number, array_map('intval', $user_number_tmp));
                    break;
                default:
                    $th = "Wrong EM or EV motor type to create whole day user number";
                    break;
            }
            $percent = $this->sqlFetchAssoc("SELECT `percent` FROM `". $emev_type ."`", array("percent"));
            $charging_power = $this->sqlFetchAssoc("SELECT `power` FROM `". $emev_type ."`", array("power"));
    
            if ($emev_type == "EM_motor_type") {
                
                for ($i=0; $i < count($percent); $i++) { 
                    switch ($charging_power[$i]) {
                        case '12':
                            $type_all[$i] = $this->multiply($nfsf_user_number[2], $percent[$i]/100);
                            break;
                        case '4.8':
                            $type_all[$i] = $this->multiply($nfsf_user_number[1], $percent[$i]/100);
                            break;
                        default:
                            $type_all[$i] = $this->multiply($nfsf_user_number[0], $percent[$i]/100);
                            break;
                    }
                }
                for ($i=0; $i < count($type_all[0]); $i++) { 
                    
                    $sql = "INSERT INTO `$table` (`timeblock`, `type_0`, `type_1`, `type_2`, `type_3`, `type_4`, `type_5`, `type_6`, `type_7`, `type_8`, `type_9`) VALUES ('".$i."','".$type_all[0][$i]."','".$type_all[1][$i]."','".$type_all[2][$i]."','".$type_all[3][$i]."','".$type_all[4][$i]."','".$type_all[5][$i]."','".$type_all[6][$i]."','".$type_all[7][$i]."','".$type_all[8][$i]."','".$type_all[9][$i]."');";
                    mysqli_query($this->conn, $sql);
                }
                return "success";
            }
            elseif ($emev_type == "EV_motor_type") {
    
                for ($i=0; $i < count($percent); $i++) { 
                    $type_all[$i] = $this->multiply($nfsf_user_number[0], $percent[$i]/100);
                }
                for ($i=0; $i < count($type_all[0]); $i++) { 
                    $sql = "INSERT INTO `$table` (`timeblock`, `type_0`, `type_1`, `type_2`, `type_3`) VALUES ('".$i."','".$type_all[0][$i]."','".$type_all[1][$i]."','".$type_all[2][$i]."','".$type_all[3][$i]."');";
                    mysqli_query($this->conn, $sql);
                }
                return "success";
            }
            else {
                $th = "Insert ". $table ." false";
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }
    private function update_EMParameter_totalChargingPole($old_totalPoles, $old_superFastPoles, $old_fastPoles, $old_normalPoles, $newParameter) {
        
        $new_sfPole = $newParameter['value'][array_search("Super_Fast_Charging_Pole", $newParameter['name'], true)];
        $new_fPole = $newParameter['value'][array_search("Fast_Charging_Pole", $newParameter['name'], true)];
        $new_nPole = $newParameter['value'][array_search("Normal_Charging_Pole", $newParameter['name'], true)];
        if ($new_sfPole != $old_superFastPoles || $new_fPole != $old_fastPoles || $new_nPole != $old_normalPoles) {
            $status = "Pole amount change";
        }
        if ($new_sfPole+$new_fPole+$new_nPole != $old_totalPoles) {
            
            $this->updateSQL($newParameter['table'], "value", ($new_sfPole+$new_fPole+$new_nPole), 'parameter_name', "Total_Charging_Pole");
            $new_totalPoles = $this->sqlFetchRow("SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Total_Charging_Pole'", $this->oneValue);
            if ($new_sfPole+$new_fPole+$new_nPole == $new_totalPoles)
                $status = "Total pole amount change";
            else
                $status = "something went wrong";
        }
        return [$status, $new_totalPoles, $new_sfPole, $new_fPole, $new_nPole];
    }
    private function create_chargingPole($table, $totalPoles, $sfPole=null, $fPole=null, $nPole=null) {
    
        switch ($table) {
            case 'EM_Pole':
                try {
                    mysqli_query($this->conn, "TRUNCATE TABLE `$table`");
                    for ($i=0; $i < $totalPoles; $i++) { 
                        
                        $sql = "INSERT INTO `$table` (`Pole_ID`, `number`, `sure`, `charging_status`, `discharge_status`, `full`, `wait`, `SOC`, `BAT_CAP`, `P_charging_pole`, `Start_timeblock`, `Departure_timeblock`) VALUES ('" .($i+1). "', NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL);";
                        mysqli_query($this->conn, $sql);
                    }
                    if ($nPole != 0) {
                        $sql = "UPDATE `$table` SET `P_charging_pole` = '0.6' WHERE `Pole_ID` <= '$nPole'";
                        mysqli_query($this->conn, $sql);
                    }
                    if ($fPole != 0) {
                        $sql = "UPDATE `$table` SET `P_charging_pole` = '4.8' WHERE `Pole_ID` > '$nPole' AND `Pole_ID` <= '".($nPole+$fPole)."'";
                        mysqli_query($this->conn, $sql);
                    }
                    if ($sfPole != 0) {
                        $sql = "UPDATE `$table` SET `P_charging_pole` = '12' WHERE Pole_ID > '".($nPole+$fPole)."' AND '".($nPole+$fPole+$sfPole)."'";
                        mysqli_query($this->conn, $sql);
                    }
                    return "success";
            
                } catch (\Throwable $th) {
                    return $th;
                }
                break;
            case 'EV_Pole':
                try {
                    mysqli_query($this->conn, "TRUNCATE TABLE `$table`");
                    for ($i=0; $i < $totalPoles; $i++) { 
                        
                        $sql = "INSERT INTO `$table` (`Pole_ID`, `number`, `sure`, `charging_status`, `discharge_status`, `full`, `wait`, `SOC`, `BAT_CAP`, `P_charging_pole`, `Start_timeblock`, `Departure_timeblock`) VALUES ('" .($i+1). "', NULL, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL);";
                        mysqli_query($this->conn, $sql);
    
                        mysqli_query($this->conn, "UPDATE `EV_Pole` SET `P_charging_pole` = (SELECT `power` FROM `EV_motor_type` WHERE `percent` <> 0 LIMIT 1 OFFSET 0)");
                        mysqli_query($this->conn, "UPDATE `EV_Parameter` SET value = (SELECT `power` FROM `EV_motor_type` WHERE `percent` <> 0 LIMIT 1 OFFSET 0) WHERE `parameter_name` = 'Charging_Power' ");
    
                    }
                    return "success";
    
                } catch (\Throwable $th) {
                    return $th;
                }
                break;
            default:
                return "Wrong Pole table";
                break;
        }
    }
    public function UpdateEMEV_ParmOrType($newParameter) {
        
        switch ($newParameter['table']) {
            case 'EM_motor_type':
                for ($i=0; $i < count($newParameter['id']); $i++) {

                    $this->updateSQL($newParameter['table'], "percent", $newParameter['percent_value'][$i], 'id', $newParameter['id'][$i]);
                    
                    $value = $this->sqlFetchRow("SELECT `percent` FROM `" .$newParameter['table']. "` WHERE `id` = '" .$newParameter['id'][$i]. "'", $this->oneValue);
                    
                    if ($value == $newParameter['percent_value'][$i])
                        $status = "success";
                    else
                        $status = "something went wrong";
                }
                $status = $this->create_wholeDay_userChargingNumber("EM_wholeDay_userChargingNumber", $newParameter['table']);
                return $status;
                break;
            case 'EV_Parameter_of_randomResult':
            case 'EM_Parameter_of_randomResult':
                for ($i=0; $i < count($newParameter['value']); $i++) {
                    
                    $this->updateSQL($newParameter['table'], "value", $newParameter['value'][$i], 'parameter_name', $newParameter['name'][$i]);
                    
                    $value = $this->sqlFetchRow("SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = '" .$newParameter['name'][$i]. "'", $this->oneValue);
                    
                    if ($value == $newParameter['value'][$i]) {
                        $status = "success";
                    }
                    else {
                        $status = "something went wrong";
                        break;
                    }
                }
                return $status;
                break;
            case 'EM_Parameter':
                $old_totalPoles = $this->sqlFetchRow("SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Total_Charging_Pole'", $this->oneValue);
                $old_superFastPoles = $this->sqlFetchRow("SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Super_Fast_Charging_Pole'", $this->oneValue);
                $old_FastPoles = $this->sqlFetchRow("SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Fast_Charging_Pole'", $this->oneValue);
                $old_normalPoles = $this->sqlFetchRow("SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Normal_Charging_Pole'", $this->oneValue);
                for ($i=0; $i < count($newParameter['value']); $i++) {
                    
                    $this->updateSQL($newParameter['table'], "value", $newParameter['value'][$i], 'parameter_name', $newParameter['name'][$i]);
                    
                    $value = $this->sqlFetchRow("SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = '" .$newParameter['name'][$i]. "'", $this->oneValue);
                    
                    if ($value == $newParameter['value'][$i]) {
                        $status = "success";
                    }
                    else {
                        $status = "something went wrong";
                        break;
                    }
                }
                [$change_status, $totalPoles, $sfPole, $fPole, $nPole] = $this->update_EMParameter_totalChargingPole($old_totalPoles, $old_superFastPoles, $old_FastPoles, $old_normalPoles, $newParameter);
                if ($change_status == "Pole amount change" ||$change_status == "Total pole amount change") {

                    $status = $this->create_chargingPole("EM_Pole", $totalPoles, $sfPole, $fPole, $nPole);
                }
                return $status;
                break;
            case 'EV_motor_type':
                for ($i=0; $i < count($newParameter['id']); $i++) {

                    $this->updateSQL($newParameter['table'], "percent", $newParameter['percent_value'][$i], 'id', $newParameter['id'][$i]);
                    
                    $value = $this->sqlFetchRow("SELECT `percent` FROM `" .$newParameter['table']. "` WHERE `id` = '" .$newParameter['id'][$i]. "'", $this->oneValue);
                    
                    if ($value == $newParameter['percent_value'][$i]) {
                        $status = "success";
                    }
                    else {
                        $status = "something went wrong";
                        break;
                    }
                }
                // P_charging_pole is not same as the EV type power
                if ($this->sqlFetchRow("SELECT DISTINCT(P_charging_pole) FROM `EV_Pole`", $this->oneValue) != $this->sqlFetchRow("SELECT `power` FROM `EV_motor_type` WHERE `percent` <> 0 LIMIT 1 OFFSET 0", $this->oneValue)) {
                    
                    mysqli_query($this->conn, "UPDATE `EV_Pole` SET `P_charging_pole` = (SELECT `power` FROM `EV_motor_type` WHERE `percent` <> 0 LIMIT 1 OFFSET 0)");
                    mysqli_query($this->conn, "UPDATE `EV_Parameter` SET value = (SELECT `power` FROM `EV_motor_type` WHERE `percent` <> 0 LIMIT 1 OFFSET 0) WHERE `parameter_name` = 'Charging_Power' ");
                }
                $status = $this->create_wholeDay_userChargingNumber("EV_wholeDay_userChargingNumber", $newParameter['table']);
                return $status;
                break;
            case 'EV_Parameter':
                $old_totalPoles = $this->sqlFetchRow("SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = 'Total_Charging_Pole'", $this->oneValue);
                for ($i=0; $i < count($newParameter['value']); $i++) {
                    
                    $this->updateSQL($newParameter['table'], "value", $newParameter['value'][$i], 'parameter_name', $newParameter['name'][$i]);
                    
                    $value = $this->sqlFetchRow("SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = '" .$newParameter['name'][$i]. "'", $this->oneValue);
                    
                    if ($value == $newParameter['value'][$i]) {
                        $status = "success";
                    }
                    else {
                        $status = "something went wrong";
                        break;
                    }
                }
                $totalPoles = $newParameter['value'][array_search("Total_Charging_Pole", $newParameter['name'], true)];
                if ($old_totalPoles != $totalPoles) {

                    $status = $this->create_chargingPole("EV_Pole", $totalPoles);
                }
                return $status;
                break;
            default:
                break;
        }
    }
}

