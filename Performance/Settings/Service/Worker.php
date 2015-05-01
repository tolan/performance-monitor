<?php

namespace PM\Settings\Service;

use PM\Main\Abstracts;
use PM\Settings\Entity;
use PM\Settings\Repository;

/**
 * This script defines class for gearman worker service.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 */
class Worker extends Abstracts\Service {

    /**
     * It returns all instances of gearman workers.
     *
     * @param Repository\Worker $repository Worker repository instance
     *
     * @return Entity\Worker[]
     */
    public function findWorkers(Repository\Worker $repository) {
        return $repository->findWorkers();
    }

    /**
     * It returns gearman worker.
     *
     * @param int               $id         ID of worker
     * @param Repository\Worker $repository Worker repository instance
     *
     * @return Entity\Worker
     */
    public function getWorker($id, Repository\Worker $repository) {
        return $repository->getWorker($id);
    }

    /**
     * Creates new gearman worker by given data.
     *
     * @param array             $data       Data of new gearman worker
     * @param Repository\Worker $repository Worker repository instance
     *
     * @return Entity\Worker
     */
    public function createWorker($data, Repository\Worker $repository) {
        $worker = new Entity\Worker($data);

        $repository->createWorker($worker);

        return $worker;
    }

    /**
     * Updates existed gearman worker by given data.
     *
     * @param array             $data       Data of existed action
     * @param Repository\Worker $repository Worker repository instance
     *
     * @return Entity\Worker
     */
    public function updateWorker($data, Repository\Worker $repository) {
        $worker = new Entity\Worker($data);

        $repository->updateWorker($worker);

        return $worker;
    }

    /**
     * Deletes gearman worker entity.
     *
     * @param int               $id         ID of gearman worker entity
     * @param Repository\Worker $repository Worker repository instance
     *
     * @return int
     */
    public function deleteWorker($id, Repository\Worker $repository) {
        return $repository->deleteWorker($id);
    }
}
