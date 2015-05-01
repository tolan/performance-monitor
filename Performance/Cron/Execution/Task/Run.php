<?php

namespace PM\Cron\Execution\Task;

use PM\Cron\Execution\Context\Entity;
use PM\Profiler;
use PM\Profiler\Enum\HttpKeys;
use PM\Statistic;

/**
 * This script defines class for execute task run.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Run extends AbstractTask {

    /**
     * It creates execution for run scenario entity.
     *
     * @param Entity\Scenario $entity Scenario context entity
     *
     * @return Run
     */
    public function runScenario(Entity\Scenario $entity, Profiler\Service\Test $serviceTest) {
        $executor = $this->getExecutor()
            ->add('createTest', $serviceTest, array('scenarioId' => $entity->getId()))
            ->add(function(Profiler\Gearman\Client $client, $data) {
                $client->setData(array(HttpKeys::TEST_ID => $data->getId()))
                    ->doAsynchronize();
            });

        $this->execute($executor);

        return $this;
    }

    /**
     * It creates execution for run statistic entity.
     *
     * @param Entity\StatisticSet $entity Statistic set context entity
     *
     * @return Run
     */
    public function runStatisticSet(Entity\StatisticSet $entity, Statistic\Service\Run $runService) {
        $executor = $this->getExecutor()
            ->add('createRun', $runService, array('setId' => $entity->getId()))
            ->add(function(Statistic\Gearman\Run\Client $client, $data) {
                $client->setData(array('id' => $data->getId()))
                    ->doAsynchronize();
            });

        $this->execute($executor);

        return $this;
    }
}
