<?php
defined('SYS_ROOT') or die('Access Denied!');

$config['perms'] = array(
    'menu.custom' => array('title' => ''),
);

$config['defaults'] = array(
    '1' => 'all',
    '2' => array(),
    '3' => array()
);

return $config;
