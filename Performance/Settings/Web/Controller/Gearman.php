<?php

namespace PM\Settings\Web\Controller;

use PM\Main\Database;
use PM\Main\Web\Component\Request;
use PM\Main\Web\Controller\Abstracts\Json;

/**
 * This scripts defines class of settings gearman controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 *
 * @link /settings/gearman
 */
class Gearman extends Json {

    /**
     * Returns gearman status.
     *
     * @link /status
     *
     * @method GET
     *
     * @return void
     */
    public function actionStatus() {
        $status = $this->getProvider()->get('PM\Settings\Gearman\Status'); /* @var $status \PM\Settings\Gearman\Status */

        $this->getExecutor()
            ->add('get', $status);
    }

    /**
     * Returns gearman workers.
     *
     * @link /workers
     *
     * @method GET
     *
     * @return void
     */
    public function actionWorkers() {
        $workerService = $this->getProvider()->get('PM\Settings\Service\Worker'); /* @var $workerService \PM\Settings\Service\Worker */

        $this->getExecutor()->add('findWorkers', $workerService);
    }

    /**
     * Returns gearman worker by ID.
     *
     * @link /worker/get/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetWorker($id) {
        $workerService = $this->getProvider()->get('PM\Settings\Service\Worker'); /* @var $workerService \PM\Settings\Service\Worker */

        $this->getExecutor()->add('getWorker', $workerService, array('id' => $id));
    }

    /**
     * Creates new worker by given data.
     *
     * @link /worker/create
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreateWorker() {
        $workerService = $this->getProvider()->get('PM\Settings\Service\Worker'); /* @var $workerService \PM\Settings\Service\Worker */

        $this->getExecutor()
            ->add(function(Database $database, Request $request) {
                $database->getTransaction()->begin();
                return array('data' => $request->getInput());
            })
            ->add('createWorker', $workerService)
            ->add(function(Database $databasse) {
                $databasse->getTransaction()->commitAll();
            });
    }

    /**
     * Updates existed worker by given data.
     *
     * @link /worker/update/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdateWorker($id) {
        $workerService = $this->getProvider()->get('PM\Settings\Service\Worker'); /* @var $workerService \PM\Settings\Service\Worker */

        $this->getExecutor()
            ->add(function(Database $database, $input) use ($id) {
                $database->getTransaction()->begin();
                $input['id'] = $id;

                return array('data' => $input);
            })
            ->add('updateWorker', $workerService)
            ->add(function(Database $databasse) {
                $databasse->getTransaction()->commitAll();
            });
    }

    /**
     * Deletes woker by given ID.
     *
     * @link /worker/delete/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteWorker($id) {
        $workerService = $this->getProvider()->get('PM\Settings\Service\Worker'); /* @var $workerService \PM\Settings\Service\Worker */

        $this->getExecutor()
            ->add(function(Database $database) {
                $database->getTransaction()->begin();
            })
            ->add('deleteWorker', $workerService, array('id' => $id))
            ->add(function(Database $databasse, $data) {
                $databasse->getTransaction()->commitAll();
                return $data;
            });
    }

    /**
     * Starts german worker by given status and worker settings.
     *
     * @link /worker/start
     *
     * @method POST
     *
     * @return void
     */
    public function actionWorkerStart() {
        $this->_runOperation('start');
    }

    /**
     * Stops one german worker by given status and worker settings.
     *
     * @link /worker/stop
     *
     * @method POST
     *
     * @return void
     */
    public function actionWorkerStop() {
        $this->_runOperation('stop');
    }

    /**
     * Stops all german workers by given status and worker settings.
     *
     * @link /worker/stopAll
     *
     * @method POST
     *
     * @return void
     */
    public function actionWorkerStopAll() {
        $this->_runOperation('stopAll');
    }

    /**
     * Keeps count of available german workers by given status and worker settings.
     *
     * @link /worker/keep
     *
     * @method POST
     *
     * @return void
     */
    public function actionWorkerKeep() {
        $this->_runOperation('keep');
    }

    /**
     * It runs operation by given method and status and worker settings in request input.
     *
     * @param string $method Method of operation
     *
     * @return void
     */
    private function _runOperation($method) {
        $operation = $this->getProvider()->get('PM\Settings\Gearman\Operation'); /* @var $operation \PM\Settings\Gearman\Operation */

        $this->getExecutor()
            ->add(function(Request $request) {
                return $request->getInput();
            })
            ->add($method, $operation);
    }
}
