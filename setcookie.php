<?php

header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

if (count($_GET) > 0) {

    if (isset($_GET['expire'])) {
        $expire = (int)$_GET['expire'];
        unset($_GET['expire']);
    } else {
        $expire = time() - 3600;
    }
    
    foreach ($_GET as $k => $v) {
    
        $k = strip_tags(trim($k));
        $v = strip_tags(trim($v));

        setcookie($k, $v, $expire, '/');
    }
    
    die('200');
}
