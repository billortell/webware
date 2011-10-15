<?php

$config = array();
$config['appid']   = 'hss';
$config['name'] = 'Hooto Storage Service';
$config['summary'] = 'Hooto Storage Service';

$config['depends'][] = 'hss';
$config['depends'][] = 'user';

$config['version'] = '1.0.0';

return $config;
