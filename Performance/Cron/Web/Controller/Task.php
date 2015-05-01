<?php

namespace PM\Cron\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;
use PM\Cron;
use PM\Main\Database;
use PM\Main\Web\Component\Request;

/**
 * This scripts defines class of cron task controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 *
 * @link /cron/task
 */
class Task extends Json  {

    /**
     * This find all cron tasks in database.
     *
     * @link /find
     *
     * @method GET
     *
     * @return void
     */
    public function actionFindTasks() {
        $taskService = $this->getProvider()->get('\PM\Cron\Service\Task'); /* @var $taskService Cron\Service\Task */

        $this->getExecutor()
            ->add('findTasks', $taskService);
    }

    /**
     * Get cron task by ID.
     *
     * @link /get/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetTask($id) {
        $taskService = $this->getProvider()->get('\PM\Cron\Service\Task'); /* @var $taskService Cron\Service\Task */

        $this->getExecutor()->add('getTask', $taskService, array('id' => $id));
    }

    /**
     * Creates new cron task by given data.
     *
     * @link /create
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreateTask() {
        $taskService = $this->getProvider()->get('\PM\Cron\Service\Task'); /* @var $taskService Cron\Service\Task */

        $this->getExecutor()
            ->add(function(Database $database, Request $request) {
                $database->getTransaction()->begin();
                return array('data' => $request->getInput());
            })
            ->add('createTask', $taskService)
            ->add(function(Database $databasse) {
                $databasse->getTransaction()->commitAll();
            });
    }

    /**
     * Updates existed cron task by given data.
     *
     * @link /update/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdateTask($id) {
        $taskService = $this->getProvider()->get('\PM\Cron\Service\Task'); /* @var $taskService Cron\Service\Task */

        $this->getExecutor()
            ->add(function(Database $database, $input) use ($id) {
                $database->getTransaction()->begin();
                $input['id'] = $id;

                return array('data' => $input);
            })
            ->add('updateTask', $taskService)
            ->add(function(Database $databasse) {
                $databasse->getTransaction()->commitAll();
            });
    }

    /**
     * Deletes cron task by given ID.
     *
     * @link /delete/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteTask($id) {
        $taskService = $this->getProvider()->get('\PM\Cron\Service\Task'); /* @var $taskService Cron\Service\Task */

        $this->getExecutor()
            ->add(function(Database $database) {
                $database->getTransaction()->begin();
            })
            ->add('deleteTask', $taskService, array('id' => $id))
            ->add(function(Database $databasse, $data) {
                $databasse->getTransaction()->commitAll();
                return $data;
            });
    }

    /**
     * Return configuration of menus for cron actions.
     *
     * @link /menus/action
     *
     * @method GET
     *
     * @return void
     */
    public function actionMenusActions() {
        $assoc = $this->getProvider()->get('PM\Cron\Association'); /* @var $assoc Cron\Association */

        return $assoc->getMenusForActions();
    }
}
