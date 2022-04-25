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
        
    public function updateSQL($table, $target_col, $target_value, $condition_col, $condition_value) {

        $sql = "UPDATE `$table` SET `$target_col` = '$target_value' WHERE `$condition_col` = '$condition_value'";    
        mysqli_query($this->conn, $sql);
    }
}

