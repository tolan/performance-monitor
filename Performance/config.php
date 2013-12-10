<?php

$config = array(
    'provider' => array(
        'useAutoloader' => TRUE,
        'initServices' => array('Performance_Main_Database'),
        'serviceMap' =>  array(
            'database' => 'Performance_Main_Database',
            'config'   => 'Performance_Main_Config',
            'web'      => 'Performance_Main_Web_App',
            'request'  => 'Performance_Main_Web_Component_Request'
        )
    ),
    'database' => array(
        'address'  => 'localhost',
        'user'     => 'root',
        'password' => 'net',
        'database' => 'performance',
        'install'  => true
    )
);