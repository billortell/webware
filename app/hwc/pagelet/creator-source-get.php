<?php

header('Content-type: text/plain');

$url = SYS_ROOT.'app/hwc/pagelet/creator_demo.php';
print file_get_contents($url);
 
?>
