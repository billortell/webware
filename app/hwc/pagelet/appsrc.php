<?php




header('Content-type: text/plain');


if ($this->reqs->path == null) {
    header("HTTP/1.0 404 Not Found"); die();
}

$f = SYS_ROOT."/app/{$this->reqs->path}";
//$f = str_replace("-", "/", $f);
$f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);

if (!file_exists($f)) {
    header("HTTP/1.0 404 Not Found"); die();
}

$fm = mime_content_type($f);
if (substr($fm,0,4) != 'text' && substr($fm,-3) != 'xml') {
  header("HTTP/1.0 404 Not Found"); die();
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
