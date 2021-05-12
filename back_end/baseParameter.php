<?php
require 'commonSQL_data.php';

$baseParameter = sqlFetchAssoc($conn, "SELECT `parameter_name`, `value` FROM `BaseParameter`", array("parameter_name", "value"));
mysqli_close($conn);

$data_array = [

    "baseParameter" => $baseParameter

];

echo json_encode($data_array);
