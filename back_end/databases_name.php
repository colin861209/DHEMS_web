<?php
session_start();

if(!isset($_POST['phpReceive_database_name'])) {
    
    // phpReceive 無設置
    if (!isset($_SESSION['database'])) {
        // session database 無設置
        $database_name = 'DHEMS';
    }
    else {

        // session database 被設置
        $database_name = $_SESSION['database'];
    }

} else {
    // phpReceive 被設置
    $database_name = $_POST['phpReceive_database_name'];
}
$_SESSION['database'] = $database_name;

// if (!isset($_SESSION['database'])) {
//     $database_name = 'fdsafdsafdsa';
// }
// echo json_encode(array("status" => $_SESSION['database']));
?>