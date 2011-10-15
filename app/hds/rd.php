<?php

defined('SYS_ROOT') or die('Access Denied!');

final class hds_rd
{
  private static $_confs = array();
  private static $_conns = array();
  private static $_table = array();

  public static function set($key, $val)
  {
    self::$_confs[$key] = $val;
  }
  
  public static function setAll($var)
  {
    foreach ($var as $key => $val) {
      self::$_confs[$key] = $val;
    }
  }
  
  public static function getTable($table, $db = 'def')
  {
    if (!isset(self::$_table[$table])) {
      self::$_table[$table] = new hds_rd_table($db, $table);
    }
    
    return self::$_table[$table];
  }
  
  public static function getConn($db)
  {
    if (!isset(self::$_conns[$db])) {
      
      if (!isset(self::$_confs[$db])) {
        return false;
      }
      
      $c = self::$_confs[$db];
      $o = array();
      
      if ($c['adapter'] == 'mysql') {
        $o['1002'] = "SET NAMES 'utf8'";
        $o[PDO::ATTR_PERSISTENT] = true;
      }

      self::$_conns[$db] = new PDO($c['dsn'], $c['user'], $c['pass'], $o);
    }
    
    return self::$_conns[$db];
  }
}
