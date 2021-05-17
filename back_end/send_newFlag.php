<?php
require 'commonSQL_data.php';

function updateSQL($conn, $table, $target_col, $target_value, $condition_col, $condition_value) {

    $sql = "UPDATE `$table` SET `$target_col` = '$target_value' WHERE `$condition_col` = '$condition_value'";    
    mysqli_query($conn, $sql);
    
}

$newFlag = $_POST['phpReceive'];

for ($i=0; $i < count($newFlag['name']); $i++) { 
    
    updateSQL($conn, $newFlag['table'], "flag", $newFlag['flag'][$i], "variable_name", $newFlag['name'][$i]);
}

$flag = sqlFetchAssoc($conn, "SELECT `flag` FROM `" .$newFlag['table'] ."` WHERE `flag` IS NOT NULL", array("flag"));
if ($flag == $newFlag['flag'])
    $status = "success";
else
    $status = "something went wrong";

echo json_encode(array(
    "status" => $status,
    "database_name" => $database_name
));

mysqli_close($conn);
?>