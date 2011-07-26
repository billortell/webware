<?php

header('Content-type: text/plain');

$xml = file_get_contents("php://input");

$url = SYS_ROOT.'app/hwc/pagelet/creator_demo.php';

$fp = fopen($url, 'w');
fwrite($fp, $xml);
fclose($fp);

echo 'OK:'.$xml;
?>
