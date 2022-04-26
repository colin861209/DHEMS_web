<?php
require 'fetch_mysql.php';

$obj = new SQLQuery();
$newFlag = $_POST['phpReceive'];

$status = $obj->UpdateFlags($newFlag);

echo json_encode(array(
    "status" => $status,
    "database_name" => $database_name
));

?>