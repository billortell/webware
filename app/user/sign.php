<?php
/**
 * SmartKit
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   User
 * @package    User_Model
 * @copyright  Copyright 2010 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    $Id: Sign.php 834 2010-03-22 16:26:33Z onerui $
 */
 
/** ensure this file is being included by a parent file */
defined('SYS_ROOT') or die('Access Denied!');


/**
 * Class user_sign
 *
 * @category   User
 * @package    User_Model
 * @copyright  Copyright 2010 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class user_sign
{
    /**
     * Parameters check for user-sign-in action 
     *
     * @param array $params
     * @param string $msg Error message when false
     * @return bool
     */
    public static function isValid(&$params, &$msg = null) 
    {
        $params['uname'] = strtolower(trim($params['uname']));
        
        if (! isset($params['persistent']) || $params['persistent'] != 1) {
            $params['persistent'] = 0;
        }
        
		if (!preg_match('/^[a-z]{1,1}[a-z0-9]{2,15}$/', $params['uname'])) {
		    $msg = 'Invalid Username';
            return false;
		}
        
        return true;
    }
    
    public static function in($params) 
    {
        try {
        
            $_user = Hooto_Data_Sql::getTable('user');
            
            $uid   = uname2uid($params['uname']);
            $user = $_user->fetch($uid);
            
            if (!isset($user['uname'])) {
                throw new Exception('Username and pass do not match');
            }
            
        } catch (Exception $e) {
            throw $e;
        }
        
        $pass = md5($params['pass']);
        if ($pass != $user['pass']) {
            throw new Exception('Username and pass do not match');
        }

        $sid = hwl_string::rand(32);
        $timeout = $_SERVER['REQUEST_TIME'] + 864000;  
        $data = array('id' => $sid,
            'uid'    => $user['id'],
            'uname'  => $user['uname'],
            'persistent'=> $params['persistent'],
            'source'    => Core_Util_Ip::getRemoteAddr(),
            'created' => $_SERVER['REQUEST_TIME'],
            'updated' => $_SERVER['REQUEST_TIME'],
            'timeout' => $timeout,
        );

        try {
            $_session = Hooto_Data_Sql::getTable('session');
            $_session->insert($data);
        } catch (Exception $e) {
            throw $e;
        }
        
        //setcookie("sid", $sid, $timeout, '/');
        setcookie("access_token", $sid, $timeout, '/');
        registry("access_token", $sid);
    }
    
    public function out()
    {
        if (isset($_COOKIE['access_token'])) {
            $sid = $_COOKIE['access_token'];
        } else {
            $sid = null;
        }

        if (strlen($sid)) {
            try {
                $_session = Hooto_Data_Sql::getTable('session');
                $_session->delete($sid);
            } catch (Exception $e) {
                throw $e;
            }
        }
        
        setcookie("sid", '', 1, '/');
        //setcookie("uid", '', 1, '/');
        @session_destroy();
    } 
}
