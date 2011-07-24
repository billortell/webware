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
define('SYS_ROOT', realpath('.'). DS);

if (preg_match("/gzip/", $_SERVER['HTTP_ACCEPT_ENCODING'])) {
    ob_start();
}

set_include_path(implode(PATH_SEPARATOR, array(SYS_ROOT.'app')));

require SYS_ROOT.'app/hww/boot.php';


