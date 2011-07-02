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
 * @category   user
 * @package    user_session
 * @copyright  Copyright 2010 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    $Id: Instance.php 834 2010-03-22 16:26:33Z onerui $
 */
 
/** ensure this file is being included by a parent file */
defined('SYS_ROOT') or die('Access Denied!');

/**
 * Class user_session
 *
 * @category   user
 * @package    user_session
 * @copyright  Copyright 2010 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
final class user_session
{
    protected static $_instance;
    
    protected static $_perms;
    

    // TODO
    public static function getInstance() 
    {
        if (self::$_instance === null) {
            self::$_instance = new user_session_instance();
        }
        return self::$_instance;
    }
    
    //
    public function isLogin($uid = '0')
    {
        if ($uid == '0') {
            return (self::getInstance()->uid != '0' ? true : false);
        }

        return (($uid === self::getInstance()->uid) ? true : false);
    }
    
    //
    public static function isAllow($instance, $perm)
    {
        if (self::$_perms === null) {

            if (isset(self::getInstance()->content['roles'])) {
                
                $roles = explode(',', self::getInstance()->content['roles']);
                
                $_perm = Hooto_Data_Sql::getTable('role_permission');
                
                $arg = trim(str_repeat(',?', count($roles)), ',');
                $q = $_perm->select()->where("rid IN ($arg)", $roles);
                
                $ret = $_perm->query($q);
                foreach ($ret as $val) {
                    self::$_perms[$val['instance']][$val['permission']] = true;
                }
            } else {
            
                self::$_perms = array();
            }
        }
        
        if (isset(self::$_perms[$instance]) && isset(self::$_perms[$instance][$perm])) {
            return true;
        }
        
        return false;
    }
}

class user_session_instance
{   
    public $sid = '';
    
    public $uid = 0;
    
    public $uname = 'guest';
    
    public $content = array();
    
 
    // TODO
    public function __construct()
    {
        if (isset($_SESSION['sid'])) {
            $sid = trim($_SESSION['sid']);
        } else if (isset($_COOKIE['sid'])) {
            $sid = trim($_COOKIE['sid']);
        } else {
            $sid = null;
        }
     
        if (!is_null($sid)) {

            try {

                $_session = Hooto_Data_Sql::getTable('session');
                $rs = $_session->fetch($sid);
            } catch (Exception $e) {
                $rs = null;
            }

            if (isset($rs['id'])) {
            
                $this->sid   = $rs['id'];
                $this->uid   = $rs['uid'];
                $this->uname = $rs['uname'];

                if (strlen($rs['content']) == 0) {
                
                    $_user = Hooto_Data_Sql::getTable('user');
                    $user = $_user->fetch($rs['uid']);

                    $this->content = array('roles' => $user['roles']);
                    
                    $_session->update(
                        array('content' => serialize($this->content)),
                        array('id' => $rs['id'])
                    );
                    
                } else {
                    $this->content = unserialize($rs['content']);
                }
            }
        }
    }
}
