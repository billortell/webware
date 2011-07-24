<?php
defined('SYS_ROOT') or die('Access Denied!');

$config['perms'] = array(
    'creator.list' => array('title' => ''),
    'creator.view' => array('title' => ''),
    'creator.edit' => array('title' => ''),
);

$config['defaults'] = array(
    '1' => 'all',
    '2' => array(),
    '3' => array()
);

return $config;
