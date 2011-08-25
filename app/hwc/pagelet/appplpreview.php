<?php

$f = SYS_ROOT."/app/{$this->reqs->path}";
$f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);

if (!file_exists($f)) {
    die('');
}

$p = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $this->reqs->path);
$p = explode('/', $p);
if (!isset($p[1]) || $p[1] != 'pagelet') {
  die('');
}

print $this->rawRender($f);
//print_r($p);
?>
