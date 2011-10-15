<?php

if ($this->reqs->path == null) {
    die('ERROR');
}

$f = SYS_ROOT."/app/{$this->reqs->path}";
$f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);

if (!file_exists($f)) {
    header("HTTP/1.0 404 Not Found"); die();
}
if (is_dir($f)) {
  rmdir($f);
} else {
  unlink($f);
}
?>
