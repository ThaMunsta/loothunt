<?php
$time_start = microtime(true);
echo "<li>Starting Cron";
require __DIR__ . '/trophy.php';
$time_end = microtime(true);
$execution_time = ($time_end - $time_start)/60;
echo "<li>Completed in ".$execution_time;