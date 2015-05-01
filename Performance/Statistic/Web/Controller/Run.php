<?php

namespace PM\Statistic\Web\Controller;

use PM\Statistic\Gearman;
use PM\Main\Web\Controller\Abstracts\Json;
use PM\Main\Database;

/**
 * This scripts defines class of statistic run controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 *
 * @link /statistic/run
 */
class Run extends Json {

    /**
     * Gets statistic run by given ID.
     *
     * @link /get/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetRun($id) {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Run'); /* @var $statisticService \PM\Statistic\Service\Run */

        $includeData = $this->getProvider()->get('utils')->convertToBoolean(
            $this->getRequest()->getGet()->get('includeData', false)
        );

        $includeTemplate = $this->getProvider()->get('utils')->convertToBoolean(
            $this->getRequest()->getGet()->get('includeTemplate', false)
        );

        $data = array(
            'id'              => $id,
            'includeData'     => $includeData,
            'includeTemplate' => $includeTemplate
        );

        $this->getExecutor()
            ->clean()
            ->add('getRun', $statisticService, $data);
    }

    /**
     * Deletes statistic set run by given ID.
     *
     * @link /delete/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteRun($id) {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Run'); /* @var $statisticService \PM\Statistic\Service\Run */

        $this->getExecutor()
            ->add(function(Database $database) {
                $database->getTransaction()->begin();
            })
            ->add('deleteRun', $statisticService, array('id' => $id))
            ->add(function(Database $databasse, $data) {
                $databasse->getTransaction()->commitAll();
                return $data;
            });
    }

    /**
     * Starts new statistic set run.
     *
     * @link /start/{id}
     *
     * @method POST
     *
     * @return void
     */
    public function actionStartRun($id) {
        $runService = $this->getProvider()->get('\PM\Statistic\Service\Run'); /* @var $runService \PM\Statistic\Service\Run */

        $this->getExecutor()
            ->add(function(Database $database) {
                $database->getTransaction()->begin();
            })
            ->add('createRun', $runService, array('setId' => $id))
            ->add(function(Database $database) {
                $database->getTransaction()->commitAll();
            })
            ->add(function(Gearman\Run\Client $client, $data) {
                $client->setData(array('id' => $data->getId()))
                    ->doAsynchronize();
            });
    }
}
