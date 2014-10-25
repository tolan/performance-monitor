<?php
/**
 * This file contains script for starting gearman worker.
 */

include __DIR__.'/../boot.php';

\PM\Main\Provider::getInstance()->loadEnum('\PM\Main\Gearman\ServerFunction');

$worker= new GearmanWorker();
$worker->addServer();
$worker->addFunction(\PM\Main\Gearman\Enum\ServerFunction::GEARMAN_FUNCTION, 'start_gearman_server');

while ($worker->work());

function start_gearman_server(GearmanJob $job) {
    $message  = unserialize($job->workload());
    $provider = \PM\Main\Provider::getInstance();
    $provider->get('database')
        ->disconnect()
        ->connect();

    $server = $provider->get('PM\Main\Gearman\Server'); /* @var $server \PM\Main\Gearman\Server */
    $server->setMessage($message);
    $server->run();

    $eveMan = $provider->get('PM\Main\Event\Manager'); /* @var $eveMan \PM\Main\Event\Manager */
    $eveMan->flush();
    $eveMan->clean();

    $provider->get('PM\Main\Commander')->cleanExecutor();

    $result = $server->getResult();

    return $result;
}
