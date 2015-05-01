<?php
/**
 * This file contains script for start cron job.
 * It should be called each minute.
 */
include __DIR__.'/../boot.php';

$provider = \PM\Main\Provider::getInstance();

$cronManager = $provider->singleton('\PM\Cron\Execution\Manager'); /* @var $cronManager \PM\Cron\Execution\Manager */
$taskService = $provider->singleton('\PM\Cron\Service\Task'); /* @var $taskService \PM\Cron\Service\Task */
$executor    = $provider
    ->get('commander')
    ->getExecutor(__FILE__); /* @var $executor \PM\Main\Commander\Executor */

$date  = new \PM\Cron\Parser\Date('2014-12-31 11:20:20');
$tasks = $executor->clean()->add('findTasks', $taskService)->execute()->getData();

$executor->clean()
    ->add('getTask', $taskService)
    ->add(function($data) {
        return array('task' => $data);
    })
    ->add('processTask', $cronManager, array('datetime' => $date));

foreach ($tasks as $task) {
    $executor->getResult()
        ->setId($task->getId());

    $executor->execute();
}
