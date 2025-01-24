<?php 
$table = $_POST['table'];
$output = shell_exec("sh sync.sh {$table}");
echo $output;
