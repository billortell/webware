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
 * @category   Index
 * @package    Index
 * @copyright  Copyright 2011 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */

define('START_TIME', microtime(true));
define('START_MEMORY_USAGE', memory_get_usage());

define('DS', DIRECTORY_SEPARATOR);
define('SYS_ROOT', realpath('..'). DS);


set_include_path(implode(PATH_SEPARATOR, 
    array(SYS_ROOT.'lib', SYS_ROOT.'app', get_include_path())));

require 'Hooto/Web/Boot.php';


echo "
<script type=\"text/javascript\"> \n
document.getElementById('htdebug').textContent = 'Page rendered in "
    . round((microtime(true) - START_TIME), 5) * 1000 ." ms, taking "
    . round((memory_get_usage() - START_MEMORY_USAGE) / (1024 * 8), 2) ." KB';\n
time = '".date("Y-m-d H:i:s")."';\n
</script>";

