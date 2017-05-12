<?php

$steps = str_replace("<br>" , "\n", $_POST['steps']);

header('Content-type: application/download');
header('Content-Disposition: attachment; filename="Steps - '.date('Ymd_His').'.txt"');

print $steps;

?>