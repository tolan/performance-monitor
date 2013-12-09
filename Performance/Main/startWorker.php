<?php

$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
include $_SERVER['DOCUMENT_ROOT'].'/Performance/boot.php';

Performance_Main_Provider::getInstance()->loadEnum('Performance_Main_Gearman_ServerFunction');

$worker= new GearmanWorker();
$worker->addServer();
$worker->addFunction(Performance_Main_Gearman_Enum_ServerFunction::GEARMAN_FUNCTION, 'start_gearman_server');

while ($worker->work());

function start_gearman_server(GearmanJob $job) {
    $message = unserialize($job->workload());
    $provider = Performance_Main_Provider::getInstance();

    $server = $provider->get('Performance_Main_Gearman_Server');
    $server->setMessage($message);
    $server->run();

    $result = $server->getResult();

    unset($provider);

    return $result;
}