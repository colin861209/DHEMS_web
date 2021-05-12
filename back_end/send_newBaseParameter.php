<?php
require 'commonSQL_data.php';

function updateSQL($conn, $table, $target_col, $target_value, $condition_col, $condition_value) {

    $sql = "UPDATE `$table` SET `$target_col` = '$target_value' WHERE `$condition_col` = '$condition_value'";    
    mysqli_query($conn, $sql);
}

$newParameter = $_POST['phpReceive'];

for ($i=0; $i < count($newParameter['name']); $i++) { 
    
    $sql = updateSQL($conn, $newParameter['table'], "value", $newParameter['baseParameter'][$i], "parameter_name", $newParameter['name'][$i]);
    
    $value = sqlFetchRow($conn, "SELECT `value` FROM `" .$newParameter['table']. "` WHERE `parameter_name` = '" .$newParameter['name'][$i]. "'", $oneValue);

    if ($value == $newParameter['baseParameter'][$i])
        $status = "success";
    else
        $status = "something went wrong";
}

echo json_encode(array("status" => $status));

mysqli_close($conn);
?>