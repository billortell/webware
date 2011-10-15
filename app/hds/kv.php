<?php

class hds_kv
{
  private static $o;
  
  public static function open($k)
  {
    return (isset(self::$o[$k]) ? self::$o[$k] : self::$o[$k] = self::_opennew($k));
  }
  
  public static function _opennew($k)
  {
    $c = hwl_cfg::get("hds");
    
    if (!isset($c['i'][$k])) 
      return null;

    return (hds_kv_redis::open($c['i'][$k]['s']));
  }
}
