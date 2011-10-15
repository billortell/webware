<?php
/**
 * HOOTO LIB
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
 * @category   hwl
 * @package    hwl_cfg
 * @copyright  Copyright 2011 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
 
/** ensure this file is being included by a parent file */
defined('SYS_ROOT') or die('Access Denied!');

/**
 * Class hwl_cfg
 *
 * @category   hwl
 * @package    hwl_cfg
 * @copyright  Copyright 2011 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
final class hwl_cfg
{
  private static $o = array();

  public static function get($k)
  {
    if (isset(self::$o[$k]))
      return self::$o[$k];
    
    if (is_array($data = require SYS_ROOT."/conf/{$k}.php")) {
      return (self::$o[$k] = $data);
    }

    return NULL;
  }
}
