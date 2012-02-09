<?php

if (!strlen($this->reqs->appid)) {
    die('ERROR: appid can not be null');
}

$f = SYS_ROOT."app/{$this->reqs->appid}/info.php";
$f = preg_replace(array("/\.+/", "/\/+/"), array(".", "/"), $f);

if (!file_exists($f)) {
    die("ERROR: $f is not exists");
}

$info = require $f;



if (isset($info['boot']) && strlen($info['boot'])) {

    $f2 = SYS_ROOT."conf/sites.php";
    $cf = require $f2;

    $cf['app'][$this->reqs->appid] = array('boot' => $info['boot']);
    
    $cf = "<?php\nreturn ". var_export($cf, true) .";\n";
  
    Hooto_Util_Directory::mkfiledir($f2, 0644);

    if (!is_writable($f2)) {
        die("ERROR: Can not write to '$f2'");
    }

    $fp = fopen($f2, 'w');

    //fwrite($fp, pack("CCC",0xef,0xbb,0xbf)); // utf8
    fputs($fp,"{$cf}");
    //fwrite($fp, $as);
    fclose($fp);
}

die('OK');

