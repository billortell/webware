<?php


defined('SYS_ROOT') or die('Access Denied!');


class hds_kv_redis
{
  public static function open($o)
  {
    $r = new Redis();
    
    if (isset($o['sock']))
      $r->connect($o['sock']);
    else
      $r->connect($o['host'], $o['port']);
    
    return $r;
  }
}
