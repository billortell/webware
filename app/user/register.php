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
 * Class user_register
 *
 * @category   User
 * @package    User_Model
 * @copyright  Copyright 2010 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class user_register
{   
    /**
     * Parameters check for user-sign-up action 
     *
     * @param array $params
     * @param string $msg Error message when false
     * @return bool
     */
    public static function isValid(&$params, &$msg = null) 
    {   
        if (!isset($params->uname)) {
            $msg = 'Username can not be null';
            return false;
        }
        
        $params->uname = strtolower(trim($params->uname));
        
		if (strlen($params->uname) < 5 || strlen($params->uname) > 16) {
		    $msg = 'Your Username must be between 5 and 16 characters long';
            return false;
		}
		
		if (!preg_match('/^[a-z]{1,1}$/', substr($params->uname, 0, 1))) {
		    $msg = 'Your Username must begin with a letter';
            return false;
		}
		
		if (!preg_match('/^[a-z0-9]{1,16}$/', $params->uname)) {
		    $msg = 'Only letters (a-z), numbers (0-9) are allowed';
            return false;
		}
		
		if (!Zend_Validate::is($params->pass, 'StringLength', array(6, 32))) {
            $msg = 'Password must be between 6 and 32 characters long';
            return false;
        }
        
        if ($params->pass != $params->repass) {
		    $msg = 'Passwords do not match';
            return false;
		}

		if (!Zend_Validate::is($params->email, 'EmailAddress')) {
            $msg = 'This is not a valid email address';
            return false;
        }
        
        return true;
    }
    
    
    public static function register($params)
    {
        try {

            $_user = Hooto_Data_Sql::getTable('user');
            
            $uid   = uname2uid($params->uname);
            $user = $_user->fetch($uid);

            if (isset($user->uname)) {
                throw new Exception('This ID is not available, please use another one');
            }
            
            $pass = md5($params->pass);

            $user = array(
                'id'   => $uid,
                'uname' => $params->uname,
                'name'  => $params->uname,
                'email' => $params->email,
                'pass'  => $pass,
                'created' => $_SERVER['REQUEST_TIME'],
                'updated' => $_SERVER['REQUEST_TIME'],
            );
        
            $_user->insert($user);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
 
}
