<?php
phpinfo();  // by zhaoqi

echo "<pre>"; 
//print_r($_SERVER);
echo "</pre>";

//01234567890123456789012345678901234567
//8901234567890123456789012345678901234567

//phpinfo();

function getList($var = null) {
    $var = $var . 'tst';
    return $var;
}

//echo getList();
echo user::get();

class user
{
    public static function get()
    {
        return 'admin';
    }
}


