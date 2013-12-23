<?php

$config = array(
    'provider' => array(
        'useAutoloader' => true,
        'initServices'  => array(
            'database',
            'log'
        ),
        'serviceMap'   =>  array(
            'database' => 'Performance_Main_Database',
            'config'   => 'Performance_Main_Config',
            'web'      => 'Performance_Main_Web_App',
            'request'  => 'Performance_Main_Web_Component_Request',
            'response' => 'Performance_Main_Web_Component_Response',
            'log'      => 'Performance_Main_Log',
            'access'   => 'Performance_Main_Access',
            'router'   => 'Performance_Main_Web_Component_Router',
            'cache'    => 'Performance_Main_Cache'
        )
    ),
    'database' => array(
        'address'  => 'localhost',
        'user'     => 'root',
        'password' => 'net',
        'database' => 'performance',
        'install'  => false
    ),
    'access' => array(
        'allowFrom' => array(
        ),
        'deniedFrom' => array(
        )
    ),
    'log' => array(
        'level' => 5,
        'file'  => __DIR__.'/performance.log',
        'cache' => true
    ),
    'translate' => array(
        'lang' => 'CS'
    )
);
