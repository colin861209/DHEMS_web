<?php
require 'commonSQL_data.php';

$compare_timeblock = $_POST['compare_timeblock'];

if (strpos($compare_timeblock['page_name'], 'backup')) { $target_DB = 'backup_BaseParameter'; }
else { $target_DB = 'BaseParameter';}

$new_timeblock = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `". $target_DB ."` WHERE `parameter_name` LIKE '%next_simulate_timeblock' ", array("parameter_name", "value"));
mysqli_close($conn);

if ($compare_timeblock['local'] == $new_timeblock[1][array_search("next_simulate_timeblock", $new_timeblock[0], true)] && 
    $compare_timeblock['global'] == $new_timeblock[1][array_search("Global_next_simulate_timeblock", $new_timeblock[0], true)]) {
    
    $status = "not_reload";
}
else {
    
    $status = "reload";
}

echo json_encode(array("status" => $status));
