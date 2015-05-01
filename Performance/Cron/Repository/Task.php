<?php

namespace PM\Cron\Repository;

use PM\Main\Abstracts\Repository;
use PM\Cron;

/**
 * This script defines class for manage cron tasks.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Task extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('cron_task');
    }

    /**
     * It returns cron task.
     *
     * @param int $id Id of cron task.
     *
     * @return Cron\Entity\Task
     */
    public function getTask($id) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('id = ?', $id);

        $data       = $select->fetchOne();
        $data['id'] = (int)$data['id'];

        $result = new Cron\Entity\Task($data);

        return $result;
    }

    /**
     * It returns all instances of cron task.
     *
     * @return Cron\Entity\Task[]
     */
    public function findTasks() {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName());

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id'] = (int)$item['id'];

            $result[] = new Cron\Entity\Task($item);
        }

        return $result;
    }

    /**
     * It creates new cron task.
     *
     * @param Cron\Entity\Task $task Cron task instance
     *
     * @return Cron\Entity\Task
     */
    public function createTask(Cron\Entity\Task $task) {
        $data = array(
            'name'        => $task->get('name'),
            'description' => $task->get('description', '')
        );
        $id = parent::create($data);

        return $task->setId($id);
    }

    /**
     * It updates existed cron task,
     *
     * @param Cron\Entity\Task $task Cron task instance
     *
     * @return int
     */
    public function updateTask(Cron\Entity\Task $task) {
        $data = array(
            'name'        => $task->get('name'),
            'description' => $task->get('description', ''),
        );

        return parent::update($task->getId(), $data);
    }

    /**
     * It deletes cron task.
     *
     * @param int $id Id of cron task
     *
     * @return int
     */
    public function deleteTask($id) {
        return parent::delete($id);
    }
}
