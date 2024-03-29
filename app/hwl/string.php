<?php
/**
 * Hooto Web library
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
 * @package    hwl_string
 * @copyright  Copyright 2011 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
 
/** ensure this file is being included by a parent file */
defined('SYS_ROOT') or die('Access Denied!');

/**
 * Class hwl_string
 *
 * @category   hwl
 * @package    hwl_string
 * @copyright  Copyright 2011 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class hwl_string
{
  static function rand($len = 12, $t = 1)
  {
    if ($t == 0) {
      $s = mt_rand(1,9);
      $c = str_split('0123456789');
    } else if ($t == 1) {
      $s = chr(mt_rand(97,102));
      $c = str_split('0123456789abcdef');
    } else {
      $s = chr(mt_rand(97,122));
      $c = str_split('0123456789abcdefghijklmnopqrstuvwxyz');
    }
    $len--;
    for ($i=0; $i<$len; $i++) {
      $s .= $c[array_rand($c)];
    }
    return$s;
  }
}
