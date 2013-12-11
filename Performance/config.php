<?php

$config = array(
    'provider' => array(
        'useAutoloader' => TRUE,
        'initServices'  => array(
            'Performance_Main_Database',
            'log'
        ),
        'serviceMap'    =>  array(
            'database' => 'Performance_Main_Database',
            'config'   => 'Performance_Main_Config',
            'web'      => 'Performance_Main_Web_App',
            'request'  => 'Performance_Main_Web_Component_Request',
            'log'      => 'Performance_Main_Log',
            'access'   => 'Performance_Main_Access',
            'router'   => 'Performance_Main_Web_Component_Router'
        )
    ),
    'database' => array(
        'address'  => 'localhost',
        'user'     => 'root',
        'password' => 'net',
        'database' => 'performance',
        'install'  => true
    ),
    'access' => array(
        'allowFrom' => array(
            '127.0.0.1'
        ),
        'deniedFrom' => array(
        )
    ),
    'log' => array(
        'level' => 5,
        'file'  => __DIR__.'/performance.log',
        'cache' => true
    )
);
