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
 * @category   Hooto_Web_Request
 * @package    Hooto_Web_Request
 * @copyright  Copyright 2004-2010 HOOTO.COM
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */

/** ensure this file is being included by a parent file */
defined('SYS_ROOT') or die('Access Denied!');



class hww_request
{
    /** URL */
    public $url     = '';
    
    /** URI */
    public $uri     = '';
    
    /** BASE */
    public $base    = '';
    
    /** URI */
    public $urlins     = '';
    
    /** Instance */
    public $ins     = null;
    
    /** Application */
    public $app     = 'w';
    
    /** Action */
    public $act     = 'index';
    
    /** METHOD */
    //public $method  = 'GET';
    
    //public $params  = array();
    
    public function __construct()
    {
        $this->url = (!empty($_SERVER['HTTPS'])) ? 
            "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : 
            "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

        if (isset($_SERVER['REQUEST_URI'])) {
            $this->uri = strstr(trim($_SERVER['REQUEST_URI'], '/').'?', '?', true);
            if ($pos = strpos($this->uri, '?')) {
                $this->uri = substr($this->uri, 0, $pos);
            }
        }
        //print_r($_SERVER);
        //$this->vars = new Hooto_Object();
        
        foreach ($_REQUEST as $key => $val) {
            $this->$key = $val;
            //$this->params[$key] = $val; // TODO DELETE
        }
    }
    
    public function __set($key, $val)
    {
        if (!empty($key) && '_' != substr($key, 0, 1)) {
            $this->$key = $val;
        }
    }
    
    public function __get($key)
    {
        return NULL;
    }
    
    public function router($routes = array())
    {
        $uri = ($this->uri == '') ? array() : explode('/', $this->uri);
        $urc = count($uri);

        /* $routes[] = array('route' => ':app/:act',
            'app' => 'cm', 'act' => 'index'); */
        
        foreach ($routes as $v) {
            
            $rot = explode('/', trim($v['_route'], '/'));
            $max = max($urc, count($rot));

            $pre = NULL;

            for ($i = 0; $i < $max; $i++) {                
                
                if (isset($rot[$i]) && isset($uri[$i])) {
                
                    if (substr($rot[$i], 0, 1) == ":") {
                        $v[substr($rot[$i], 1)] = $uri[$i];
                    } else if ($rot[$i] != $uri[$i]) {
                        continue 2;
                    }
                
                } else if (isset($uri[$i])) {

                    if ($pre === NULL) {
                        $pre = $uri[$i];
                    } else {
                        $v[$pre] = $uri[$i];
                        $pre = NULL;       
                    }
                
                }
            }
            
            //if (isset($v['ins']) && isset($v['act'])) {
            if (isset($v['ins']) || isset($v['ctr'])) {
                foreach ($v as $key => $val) {
                    $this->$key = $val; // TODO XSS
                }
                break;
            }
        }
        
        $key = array_search($this->ins, $uri);
        if ($key > 0) {
            for ($i = 0; $i < $key; $i++) {
                $this->base .= "/".$uri[$i];
            }
        } else {
            $this->base = "/".$this->uri;
        }
        //print_r($this);
        $this->urlins = $this->base.'/'.$this->ins;
    }
}
