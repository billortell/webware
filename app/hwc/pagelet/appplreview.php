<?php

if ($this->reqs->path == null) {
    die('ERROR');
}

$f = SYS_ROOT."/app/{$this->reqs->path}";
$f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);

if (!file_exists($f)) {
    die('HWC404');
}

?>
