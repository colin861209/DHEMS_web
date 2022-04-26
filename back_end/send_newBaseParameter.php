<?php
require 'fetch_mysql.php';


$obj = new SQLQuery();
$newParameter = $_POST['phpReceive'];

$status = $obj->UpdateBaseParameter($newParameter);

echo json_encode(array(
    "status" => $status,
    "database_name" => $obj->database_name
));

?>