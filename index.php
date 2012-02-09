<?php
/**
 * Hooto Web Engine
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
 * @package    hwe
 * @copyright  Copyright 2012 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */

//header("HTTP/1.1 503 Service Unavailable");
//die("<h1>This site is down for maintenance. Please check back again soon.</h1>");

define('SYS_ROOT', realpath('.'). DIRECTORY_SEPARATOR);

$uri = array('default');
foreach (array('REQUEST_URI','PATH_INFO','ORIG_PATH_INFO') as $v) {
    if (preg_match('/^\/[\w\-~\/\.+%]{1,600}/', 
        (isset($_SERVER[$v]) ? $_SERVER[$v] : NULL), $p)) {
        $uri = explode('/', trim($p[0], '/')); break;
    }
}

$ro = require SYS_ROOT.'conf/sites.php';

if (isset($ro['app'][$uri[0]])) {
    require SYS_ROOT.$ro['app'][$uri[0]]['boot'];
} else {
    require SYS_ROOT.$ro['app']['default']['boot'];
}
