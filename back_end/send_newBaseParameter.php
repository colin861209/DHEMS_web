<?php
require 'fetch_mysql.php';

$obj = new SQLQuery();
$status = '';
$newParameter = $_POST['phpReceive'];

for ($i=0; $i < count($newParameter['name']); $i++) { 
    
    $obj->updateSQL($newParameter['table'], "value", $newParameter['baseParameter'][$i], "parameter_name", $newParameter['name'][$i]);
    $value = $obj->sqlFetchRow("SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = '" .$newParameter['name'][$i]. "'", $obj->oneValue);

    if ($value == $newParameter['baseParameter'][$i]) {
        $status = "success";
    }
    else {
        $status = "something went wrong";
        break;
    }
}

echo json_encode(array(
    "status" => $status,
    "database_name" => $obj->database_name
));

?>