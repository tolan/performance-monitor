<?php

namespace PM\Settings\Repository;

use PM\Main\Abstracts\Repository;
use PM\Settings\Entity;

/**
 * This script defines class for manage gearman workers.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 */
class Worker extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('gearman_worker');
    }

    /**
     * It returns all instances of gearman workers.
     *
     * @return Entity\Worker[]
     */
    public function findWorkers() {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName());

        $item = $select->fetchAll();

        $result = array();
        foreach ($item as $item) {
            $item['id'] = (int)$item['id'];

            $result[] = new Entity\Worker($item);
        }

        return $result;
    }

    /**
     * It returns gearman worker.
     *
     * @param int $id Id of gearman
     *
     * @return Entity\Worker
     */
    public function getWorker($id) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('id = ?', $id);

        $data       = $select->fetchOne();
        $data['id'] = (int)$data['id'];

        $result = new Entity\Worker($data);

        return $result;
    }

    /**
     * It creates new gearman worker.
     *
     * @param Entity\Worker $worker Gearman worker instance
     *
     * @return Entity\Worker
     */
    public function createWorker(Entity\Worker $worker) {
        $data = array(
            'name'   => $worker->get('name'),
            'script' => $worker->get('script')
        );
        $id = parent::create($data);

        return $worker->setId($id);
    }

    /**
     * It updates existed gearman worker,
     *
     * @param Entity\Worker $worker Gearman worker instance
     *
     * @return int
     */
    public function updateWorker(Entity\Worker $worker) {
        $data = array(
            'name'   => $worker->get('name'),
            'script' => $worker->get('script')
        );

        return parent::update($worker->getId(), $data);
    }

    /**
     * It deletes gearman worker.
     *
     * @param int $id Id of gearman worker
     *
     * @return int
     */
    public function deleteWorker($id) {
        return parent::delete($id);
    }
}
