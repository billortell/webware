#!/usr/bin/php
<?php

define ('SYS_ENTRY', '.');
define ('SYS_PATH', realpath('.'));

/**
 * No timeout
 */
ignore_user_abort(true);
set_time_limit(0);

/** 
 * Set Error Reporting Level 
 */
error_reporting(E_ALL|E_STRICT);

/**
 * Set timezone
 */
date_default_timezone_set('Asia/Shanghai');

/** 
 * Setting global paths 
 */
set_include_path(
    dirname(__FILE__) . PATH_SEPARATOR .
    dirname(__FILE__) . '/app' . PATH_SEPARATOR .
    dirname(__FILE__) . '/etc' . PATH_SEPARATOR .  
    get_include_path()
);

function __autoload($class) {
    $class = str_replace('_', '/', $class);
    if (preg_match("#^(.*)/Model/(.*)#i", $class, $regs)) {
        $class = strtolower($regs[1]."/models/").$regs[2];
    }
    require_once ($class .".php");
}

$queues = array();

while (true)
{
    $conf = require SYS_ENTRY .'/etc/global.php';

    // Setting Logger
    $logger = new Tvm_Log($conf['log_path']);
    $logger->setPrefix('tsysagent.');


    if (isset($conf['cronjob'])) {
        $jobs = $conf['cronjob'];
    } else {
        $jobs = array();
    }


    foreach ($jobs as $procname => $job) {

        if (strlen($job['exec']) > 1) {

            if (!isset($queues[$procname])) {
                $queues[$procname] = NULL;
            }

            if (is_null($queues[$procname])) {
                $queues[$procname]['run'] = false;
            } else if (isset($queues[$procname]['proc']) && is_resource($queues[$procname]['proc'])) {
                $status = proc_get_status($queues[$procname]['proc']);
                if (! $status['running'] || feof($queues[$procname]['pipe'][1])) {
                    $queues[$procname]['run'] = false;
                    fclose($queues[$procname]['pipe'][0]);
                    fclose($queues[$procname]['pipe'][1]);
                    proc_terminate($queues[$procname]['proc']);

                    Tvm_Log_Monitor::log('error', 'tsysagent', "Crash::job::{$procname}");

                } else {
                    $queues[$procname]['run'] = true;
                }

            } else {
                $queues[$procname]['run'] = true;
            }

            if (! $queues[$procname]['run']) {

                $des = array(
                    0 => array("pipe", "r"), 
                    1 => array("pipe", "w"),
                    2 => array("file", "/tmp/error-output.txt", "a")
                );

                $exec = $job['exec']; // TODO su user
                $logger->log("Open $procname, $exec");

                $process = proc_open($exec, $des, $pipes, null, null);	

                if (is_resource($process)) {
                    stream_set_blocking($pipes[1], 0);
                    $queues[$procname]['proc'] = $process;
                    $queues[$procname]['pipe'] = $pipes;
                } else {
                    $queues[$procname] = NULL;
                }
            }
        }
    }    

    sleep(10);
}
