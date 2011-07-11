<?php

$config = array();
$config['id']   = 'hssui';
$config['name'] = 'Storage Service';
$config['summary'] = 'Hooto Storage Service User Interface';

$config['depends'][] = 'hss';
$config['depends'][] = 'user';

$config['version'] = '1.0.0';

return $config;
