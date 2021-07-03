<?php

$pwd = '/home/hems/how/fifty_DHEMS/log/';
$file = $_POST['sendtoPHP'] . '.log';
$path_file = $pwd.$file;

$file_amount = shell_exec('ls ' . escapeshellarg($pwd) . ' | wc -l');
foreach (glob($pwd.'*.log') as $file_tmp) {
    
    $file_name[] = basename($file_tmp, '.log');
}

//=-=-=-=-=- Show last 100 lines, ignore '\n' to split into lines_array -=-=-=-=-=//
// $total_lines = shell_exec('cat ' . escapeshellarg($path_file) . ' | wc -l');
// if (isset($_SESSION['current_line']) && $_SESSION['current_line'] < $total_lines)
//     $lines = shell_exec('tail -n' . ($total_lines - $_SESSION['current_line']) . ' ' . escapeshellarg($path_file));
// else if (!isset($_SESSION['current_line']))
//     $lines = shell_exec('tail -n100 ' . escapeshellarg($path_file));
// $_SESSION['current_line'] = $total_lines;
// $lines_array = array_filter(preg_split('#[\r\n]+#', trim($lines)));

$lines = shell_exec('cat' . ' ' . escapeshellarg($path_file));
$lines_array = explode(PHP_EOL, $lines);

$data_array = [

    "log_content" => $lines_array,
    "log_name" => $file,
    "log_path" => $path_file,
    "file_name_array" => $file_name,
];

echo json_encode($data_array);

?>