<?php

$config = array();
$config['appid']   = 'hssui';
$config['name'] = 'Hooto Storage Service UI';
$config['summary'] = 'Hooto Storage Service User Interface';

$config['type'] = '2';

$config['depends'][] = 'hss';
$config['depends'][] = 'user';

$config['version'] = '1.0.0';

return $config;
