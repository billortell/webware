<?php

defined('SYS_ROOT') or die('Access Denied!');


class user_pass
{
    public static function isValid(&$params, &$message = null) 
    {
        if (!isset($params['pass_current'])) {
		    $message = 'Current password can not be null';
            return false;
		}
        if (!isset($params['pass']) || !isset($params['pass_confirm'])) {
            $message = 'Password or Password-confirm can not be empty';
            return false;
        }
		if (!Zend_Validate::is($params['pass'], 'StringLength', array(6, 32))) {
		    $message = 'Password must between 6 and 32 characters long';
            return false;
        }
		if ($params['pass'] != $params['pass_confirm']) {
		    $message = 'Passwords do not match';
            return false;
		}

        return true;
    } 
}
