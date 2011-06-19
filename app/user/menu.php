<?php

defined('SYS_ROOT') or die('Access Denied!');


class user_menu
{
    public static function getList($type, $uid = 0)
    {
        $_menu = Hooto_Data_Sql::getTable('menu_link');
        
        $query = $_menu->select()
            ->where('type = ?', $type)
            ->where('uid = ?', $uid)
            ->where('status = ?', 1)
            ->order('weight', 'asc')
            ->limit(10);

        return $_menu->query($query);
    }
}
