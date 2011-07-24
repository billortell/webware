<?php
class hwl_string
{
    static function rand($len = 12, $t = 1)
    {
        if ($t == 0) {
            $s = mt_rand(1,9);
            $c = str_split('0123456789');
        } else if ($t == 1) {
            $s = chr(mt_rand(97,102));
            $c = str_split('0123456789abcdef');
        } else {
            $s = chr(mt_rand(97,122));
            $c = str_split('0123456789abcdefghijklmnopqrstuvwxyz');
        }
        $len--;
        for ($i=0; $i<$len; $i++) {
            $s .= $c[array_rand($c)];
        }
        return$s;
    }
}
