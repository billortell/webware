<?php

defined('SYS_ROOT') or die('Access Denied!');


class user_profile
{
    public static function isValid(&$params, &$message = null) 
    {
		if (!Zend_Validate::is($params->name, 'NotEmpty')) {
            $message = 'name can not be empty';
            return false;
        }
        
        if (!Zend_Validate::is($params->birthday, 'Date')) {
            $message = 'This is not a valid date';
            return false;
        }

        return true;
    }
    
    public static function fetch($uname)
    {
        try {
        
            $uid    = uname2uid($uname);
            
            $_profile = Hooto_Data_Sql::getTable('user_profile');
            
            $pf = $_profile->fetch($uid);
            
            if (isset($pf['id'])) {
                $pf['content'] = Hooto_Util_Format::textHtmlFilter($pf['content']);
            } else {
                throw new Exception('No Profile Found');
            }
            
        } catch (Exception $e) {
            throw $e;
        }
        
        return $pf;
    }
}
