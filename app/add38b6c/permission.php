<?php
defined('SYS_ROOT') or die('Access Denied!');

$config['perms'] = array(
    'entry.edit' => array('title' => ''),
    'entry.delete' => array('title' => ''),
    'comment.add' => array('title' => ''),
    'comment.edit' => array('title' => ''),
    'comment.delete' => array('title' => ''),
    'term.edit' => array('title' => ''),
    'term.delete' => array('title' => ''),
);

$config['defaults'] = array(
    '1' => 'all',
    '2' => 'all',
    '3' => array('comment.add')
);

return $config;
