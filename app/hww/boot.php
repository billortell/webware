<?php
/**
 * SmartKit
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @category   Hooto_Web_Boot
 * @package  Hooto_Web_Boot
 * @copyright  Copyright 2004-2010 HOOTO.COM
 * @license  http://www.apache.org/licenses/LICENSE-2.0
 */

/** ensure this file is being included by a parent file */
defined('SYS_ROOT') or die('Access Denied!');

define('HWW_VERSION', '1.0.2lab');

ini_set('zlib.output_compression', 'Off');

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

// autoload_register
function __autoload($class) {
  $class = str_replace('_', '/', $class);
  require_once ($class .".php");
}
//spl_autoload_register('__autoload');


if (ini_get('magic_quotes_gpc')) {

	function stripa(&$v) {
		$v = stripslashes($v);
	}
	
	function stripf(&$v, $k) {
	  if ($k != 'tmp_name') $v = stripslashes($v);
	}

	array_walk_recursive($_GET,     'stripa');
	array_walk_recursive($_POST,    'stripa');
	array_walk_recursive($_COOKIE,  'stripa');
	array_walk_recursive($_REQUEST, 'stripa');
	array_walk_recursive($_FILES,   'stripf');
}

set_error_handler(array('hww_error','handler'));
register_shutdown_function(array('hww_error','fatal'));
set_exception_handler(array('hww_error','exception'));


function server($k,$d=NULL) {
	return isset($_SERVER[$k])?$_SERVER[$k]:$d;
}
function h($data) {
	return htmlspecialchars($data,ENT_QUOTES,'utf-8');
}
function registry($k,$v=NULL) {
	static$o;return(func_num_args()>1?$o[$k]=$v:(isset($o[$k])?$o[$k]:NULL));	
}
function url($k = NULL, $d = NULL) {
	static$s;if(!$s){foreach(array('REQUEST_URI','PATH_INFO','ORIG_PATH_INFO')as$v){preg_match('/^\/[\w\-~\/\.+%]{1,600}/',server($v),$p);if(!empty($p)){$s=explode('/',trim($p[0],'/'));break;}}}if($s)return($k!==NULL?(isset($s[$k])?$s[$k]:$d):implode('/',$s));
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

function uname2uid($v) {
  return substr(md5($v), 0, 6);
}


//echo hwl_string::random_characters(8, 2);

//print_r(url());return;
/** Sites Routing */
$h = h(server('SERVER_NAME')?server('HTTP_HOST'):server('SERVER_NAME'));

//V2
/*
$cg   = require SYS_ROOT."/sites/global.php";
define('DOMAIN_NAME', (isset($cg['hosts'][$h]) ? $cg['hosts'][$h] : 'default'));
//
$csg  = require SYS_ROOT."/sites/".DOMAIN_NAME."/global.php";
$url = ((url() ? explode('/', url()) : array()) + explode('/', 'index/index'));
print_r($url);

$a = array();

foreach ($csg['routes'] as $v) {  
  
  $a = array();
  
  $v2 = explode('/', $v['r']);
  
  if (isset($v['ins']))
    $a['ins'] = $v['ins'];
  if (isset($v['act']))
    $a['act'] = $v['act'];
  
  if ($v['t'] == 'simple') {
  
    foreach ($v2 as $k3 => $v3) {

      if ($v3 == '*') {
        break 2;
      } else if (!isset($url[$k3])) {
        continue 2;
      }
        
      if (substr($v3, 0, 1) == ":") {        
        $a[substr($v3, 1)] = $url[$k3];
      } else if ($v3 == $url[$k3]) {   
        $a[$k3] = $url[$k3];
      } else {
        continue 2;
      }
      
    }
  
  } else {
  
  }
}


print_r($a);
//print_r($_SERVER);
return;


// Get the controller and page
list($instance, $action) = array_slice($url, 0, 2);
*/


//echo "$instance/$action";
// V1
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
// print_r($cg);
$reqs = new Hooto_Web_Request();//print_r($reqs);//print_r($_SERVER);
if (isset($cg['routes'])) {
  $reqs->router($cg['routes'], $cg['routes_default']);
} //print_r($reqs);die();

/**
 * database api
 */
Hooto_Data_Sql::set('def', $cg['data']['def']);

/** Init */
if (file_exists(SYS_ROOT."app/{$reqs->app}/func.php")) {
  require_once SYS_ROOT."app/{$reqs->app}/func.php";
}

if (isset($cg['sitename'])) {
  $view->headtitle = $cg['sitename'];
} //print_r($view);
$cig = Hooto_Config_Array::get("{$reqs->ins}/global");
if (isset($cig['appid'])) {
  $reqs->app = $cig['appid'];
}
if (file_exists(SYS_ROOT."/app/{$reqs->app}/action/{$reqs->act}.php")) {
  $cia = Hooto_Config_Array::get($reqs->act, SYS_ROOT."/app/{$reqs->ins}/action/");
} else {
  $cia = Hooto_Config_Array::get("{$reqs->ins}/action_{$reqs->act}");
}


/** views */
$view = new Hooto_Web_View();
$view->reqs = $reqs;
$view->setPath(SYS_ROOT."app/{$reqs->app}");

// TODO
if ((isset($cig['isite']) || isset($cia['isite'])) && isset($reqs->uname)) {
  try {
    $is = user_profile::fetch($reqs->uname);
    if (isset($is['id'])) {
      $view->headtitle = $cig['name'] .' | '. $is['sitename'];
    }
  } catch (Exception $e) {}
} else {
  $view->headtitle = $cig['name'] .' | '. $cg['sitename'];
}

foreach ($cia['pagelet'] as $let => $val) {
  
  $val['v'] = "pagelet/{$val['v']}";
  
  if (isset($val['params'])) {
    $params = $val['params'];
  } else {
    $params = NULL;
  }
  
  if (isset($val['let'])) {
    $view->{$val['let']} .= $view->render($val['v'], $params);
  } else if (isset($cia['page'])) {
    $view->{$let} .= $view->render($val['v'], $params);
  } else {
    print $view->render($val['v'], $params);
  }
}

if (is_array(Hooto_Web_View::$headStylesheet)) {
  foreach (Hooto_Web_View::$headStylesheet as $val) {
    $view->headStylesheet .= '<link rel="stylesheet" href="'.$val.'" type="text/css" media="all" />'."\n";
  }
}

if (is_array(Hooto_Web_View::$headJavascript)) {
  foreach (Hooto_Web_View::$headJavascript as $val) {
    $view->headJavascript .= '<script type="text/javascript" src="'.$val.'"></script>'."\n";
  }
}

if (isset($cia['page'])) {    
  print $view->render('page/'.$cia['page']['v']); 
}

