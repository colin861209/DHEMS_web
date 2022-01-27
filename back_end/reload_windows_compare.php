<?php
require 'commonSQL_data.php';

$new_timeblock = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `BaseParameter` WHERE `parameter_name` LIKE '%next_simulate_timeblock' ", array("parameter_name", "value"));
mysqli_close($conn);

$compare_timeblock = $_POST['compare_timeblock'];

if ($compare_timeblock['local'] == $new_timeblock[1][array_search("next_simulate_timeblock", $new_timeblock[0], true)] || 
    $compare_timeblock['global'] == $new_timeblock[1][array_search("Global_next_simulate_timeblock", $new_timeblock[0], true)]) {
    
    $status = "not_reload";
}
else {
    
    $status = "reload";
}

echo json_encode(array("status" => $status));
