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
 * @category   Hooto_Web_Boot
 * @package    Hooto_Web_Boot
 * @copyright  Copyright 2004-2010 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */

/** ensure this file is being included by a parent file */
defined('SYS_ROOT') or die('Access Denied!');

define('HWW_VERSION', '1.0.0lab');

//ini_set('zlib.output_compression', 'Off');

// Don't escape quotes when reading files from the database, disk, etc.
ini_set('magic_quotes_runtime', '0');

// Use session cookies, not transparent sessions that puts the session id in
ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
// Don't send HTTP headers using PHP's session handler.
ini_set('session.cache_limiter', 'none');
// Use httponly session cookies.
ini_set('session.cookie_httponly', '1');

// Set sane locale settings
// to ensure consistent string, dates, times and numbers handling.
setlocale(LC_ALL, 'en_US.utf-8');

//define('REQUEST_TIME', $_SERVER['REQUEST_TIME']);

if (ini_get('magic_quotes_gpc')) {

	function array_stripslashes(&$v) {
		$v = stripslashes($v);
	}
	
	function array_stripslashes_files(&$v, $k) {
	    if ($k != 'tmp_name') {
		    $v = stripslashes($v);
		}
	}

	array_walk_recursive($_GET, 'array_stripslashes');
	array_walk_recursive($_POST, 'array_stripslashes');
	array_walk_recursive($_COOKIE, 'array_stripslashes');
	array_walk_recursive($_REQUEST, 'array_stripslashes');
	array_walk_recursive($_FILES, 'array_stripslashes_files');
}

function server($k,$d=NULL) {
	return isset($_SERVER[$k])?$_SERVER[$k]:$d;
}

function h($data) {
	return htmlspecialchars($data,ENT_QUOTES,'utf-8');
}

function registry($k,$v=NULL) {
	static$o;return(func_num_args()>1?$o[$k]=$v:(isset($o[$k])?$o[$k]:NULL));	
}

class Hooto_Object {
    public function __construct($array = null) {
        if ($array !== null) 
            foreach ((array)$array as $key => $val)
                $this->$key = $val;
    }
    
    public function __set($key, $val) {
        if ('_' != substr($key, 0, 1)) $this->$key = $val;
    }
    
    public function __get($key) {
        return null;
    }
}

function uname2uid($uname) {
    return substr(md5($uname), 0, 6);
}
    
    
function __autoload($class) {
    $class = str_replace('_', '/', $class);
    if (preg_match("#^(.*)/Model/(.*)#i", $class, $regs)) {
        $class = strtolower($regs[1]).'/models/'.$regs[2];
    }
    require_once ($class .".php");
}
//spl_autoload_register('__autoload');


try {
    
    /** Sites Routing */
    $h = h(server('SERVER_NAME')?server('HTTP_HOST'):server('SERVER_NAME'));
    //$cfgs = Hooto_Config_Array::get('sites');
    $cfgs = require SYS_ROOT."conf/sites.php";
    define('SITE_NAME', (isset($cfgs[$h]) ? $cfgs[$h] : 'default'));
    if (isset($cfgs['hook_pre'])) {
        foreach ($cfgs['hook_pre'] as $v) {
            $v = str_replace('_', '/', $v);
            require_once ($v .".php");
        }
    }
    
    /**
     * Routing
     */
    $cg = Hooto_Config_Array::get('global');

    $reqs = new Hooto_Web_Request();//print_r($reqs);//print_r($_SERVER);
    if (isset($cg['routes'])) {
        $reqs->router($cg['routes']);
    } //print_r($reqs);
        
    //print_r($reqs);
    require_once "Core/Common.php";

    $ctr = str_replace(array('-', '.'), ' ', $reqs->ctr);
    $ctr = str_replace(' ', '', ucwords($ctr)).'Controller';
    $pat = SYS_ROOT ."app/{$reqs->app}/controllers/";
    
    if (file_exists($pat."{$ctr}.php")) {
        require_once $pat."{$ctr}.php";
    } else if (file_exists($pat."IndexController.php")) {
        require_once $pat."IndexController.php";
        $ctr = "IndexController";
    } else {
        throw new Exception('Invalid Request');
    }
    
    $controller = new $ctr($reqs);       
    $controller->dispatch();
    
} catch (Exception $e) {
    echo $e->getMessage();
}
