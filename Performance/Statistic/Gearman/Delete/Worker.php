<?php

namespace PM\Statistic\Gearman\Delete;

/**
 * This script defines statistic gearman worker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Worker extends \PM\Main\Abstracts\Gearman\Worker {

    /**
     * Process method which deletes statistic data.
     *
     * @return void
     */
    public function process() {
        $data = $this->getMessageData();
        $runId = $data['id'];

        $runService = $this->getProvider()->get('PM\Statistic\Service\Run'); /* @var $runService \PM\Statistic\Service\Run */
        $executor   = $this->getProvider()->get('PM\Main\Commander\Executor'); /* @var $executor \PM\Main\Commander\Executor */
        $executor->clean()
            ->add('deleteRun', $runService, array('id' => $runId))
            ->execute();
    }

    /**
     * Result for asynchronous job is true.
     *
     * @return boolean
     */
    public function getResult() {
        return true;
    }
}
