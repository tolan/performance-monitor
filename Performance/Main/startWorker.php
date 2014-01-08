<?php

include __DIR__.'/../boot.php';

\PF\Main\Provider::getInstance()->loadEnum('\PF\Main\Gearman\ServerFunction');

$worker= new GearmanWorker();
$worker->addServer();
$worker->addFunction(\PF\Main\Gearman\Enum\ServerFunction::GEARMAN_FUNCTION, 'start_gearman_server');

while ($worker->work());

function start_gearman_server(GearmanJob $job) {
    $message  = unserialize($job->workload());
    $provider = \PF\Main\Provider::getInstance();

    $server = $provider->get('PF\Main\Gearman\Server');
    $server->setMessage($message);
    $server->run();

    $eveMan = $provider->get('PF\Main\Event\Manager'); /* @var $eveMan \PF\Main\Event\Manager */
    $eveMan->flush();
    $eveMan->clean();

    $result = $server->getResult();

    unset($provider);

    return $result;
}