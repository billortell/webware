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
 * @category   Module
 * @package    User
 * @copyright  Copyright 2010 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    $Id: UpValidate.php 800 2010-01-25 14:46:15Z evorui $
 */
 
/** ensure this file is being included by a parent file */
defined('SYS_ROOT') or die('Access Denied!');

/**
 * Class user_email
 *
 * @category   Module
 * @package    User
 * @copyright  Copyright 2010 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class user_email
{
    /**
     * Parameters check for user-sign-in action 
     *
     * @param array $params
     * @param string $msg Error message when false
     * @return bool
     */
    public function isValid(&$params, &$msg = NULL) 
    {
		if (!Zend_Validate::is($params['email'], 'EmailAddress')) {
            $msg = 'This is not a valid email address';
            return false;
        }
        
        if (!isset($params['pass'])) {
		    $message = 'pass can not be null';
            return false;
		}
        
        return true;
    }
}
