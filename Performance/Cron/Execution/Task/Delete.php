<?php

namespace PM\Cron\Execution\Task;

use PM\Cron\Execution\Context\Entity;
use PM\Statistic;

/**
 * This script defines class for execute task delete.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Delete extends AbstractTask {

    /**
     * It creates execution for delete statistic run entity.
     *
     * @param Entity\StatisticRun $entity Statistic run context entity
     *
     * @return Delete
     */
    public function runStatisticRun(Entity\StatisticRun $entity) {
        $executor = $this->getExecutor()
            ->add(function(Statistic\Gearman\Delete\Client $client) use ($entity) {
                $client->setData(array('id' => $entity->getId()))
                    ->doAsynchronize();
            });

        $this->execute($executor);

        return $this;
    }
}
