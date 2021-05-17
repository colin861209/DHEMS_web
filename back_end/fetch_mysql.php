<?php
require 'databases_name.php';

$conn = new mysqli('140.124.42.65','root','fuzzy314', $_SESSION['database'], '3306');
mysqli_set_charset($conn, 'utf8');

if ($conn -> connect_errno) {

    echo "Failed to connect MySQL: ". mysqli_connect_error();
    exit();
}
error_reporting(E_ALL & ~E_NOTICE);

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

$oneValue = "oneValue";
$aRow = "aRow";
$controlStatusResult = "controlStatusResult";

function sqlFetchRow ($conn, $sql, $key) {

    switch ($key) {
        case "oneValue":
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_row($result);
            $value = $row[0];
            mysqli_free_result($result);
            return $value;
            break;

        case "aRow":
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_row($result)) {
                $array = $row;
            }
            mysqli_free_result($result);
            return $array;
            break;
        
        case "controlStatusResult":
            $k = 0;
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {

                for($i = 1; $i < 97; $i++) {
                    
                    $array[$k][] = floatval($row[$i]);
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

function sqlFetchAssoc($conn, $sql, $key) {

    $result = mysqli_query($conn, $sql);
    $count = mysqli_num_rows($result);
    if (count($key) == 1) {

        for($i = 0; $i < $count; $i++) {

            $row = mysqli_fetch_assoc($result);
            $array[$i] = $row[$key[0]];
        }
    } 
    elseif (count($key) > 1) {

        for($i = 0; $i < $count; $i++) {
            
            $row = mysqli_fetch_assoc($result);
            for($k = 0; $k < count($key); $k++) {
                
                $array[$k][$i] = $row[$key[$k]];
            }
        }
    }
    mysqli_free_result($result);
    return $array;
}
