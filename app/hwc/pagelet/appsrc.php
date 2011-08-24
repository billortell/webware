<?php

header('Content-type: text/plain');


if ($this->reqs->path == null) {
    die('ERROR');
}

$f = SYS_ROOT."/app/{$this->reqs->path}";
//$f = str_replace("-", "/", $f);
$f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);

if (!file_exists($f)) {
    die('HWC404');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'
  || $_SERVER['REQUEST_METHOD'] == 'PUT') {
  
  $s = file_get_contents("php://input");

  $fp = fopen($f, 'w');
  fwrite($fp, $s);
  fclose($fp);

  die('HWC200');
}

print file_get_contents($f); 
?>
