<?php
defined('SYS_ROOT') or die('Access Denied!');

$config['perms'] = array(
    'list' => array('title' => ''),
    'delete' => array('title' => ''),
    'new' => array('title' => ''),
);

$config['defaults'] = array(
    '1' => 'all',
    '2' => 'all',
    '3' => array()
);

return $config;
