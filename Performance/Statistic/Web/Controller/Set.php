<?php

namespace PM\Statistic\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;
use PM\Main\Web\Component\Request;
use PM\Main\Database;

/**
 * This scripts defines class of statistic set controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 *
 * @link /statistic/set
 */
class Set extends Json {

    /**
     * This find all statistic sets in database.
     *
     * @link /get
     *
     * @method GET
     *
     * @return void
     */
    public function actionFindSets() {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Set'); /* @var $statisticService \PM\Statistic\Service\Set */

        $this->getExecutor()->add('findSets', $statisticService);
    }

    /**
     * Gets statistic set by given ID.
     *
     * @link /get/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetSet($id) {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Set'); /* @var $statisticService \PM\Statistic\Service\Set */

        $includeRuns = $this->getProvider()->get('utils')->convertToBoolean(
            $this->getRequest()->getGet()->get('includeRuns', false)
        );

        $data = array(
            'id'          => $id,
            'includeRuns' => $includeRuns
        );

        $this->getExecutor()
            ->clean()
            ->add('getSet', $statisticService, $data);
    }

    /**
     * Creates new statistic set by given data.
     *
     * @link /create
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreateSet() {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Set'); /* @var $statisticService \PM\Statistic\Service\Set */

        $this->getExecutor()
            ->add(function(Database $database, Request $request) {
                $database->getTransaction()->begin();
                return array('setData' => $request->getInput());
            })
            ->add('createSet', $statisticService)
            ->add(function(Database $databasse) {
                $databasse->getTransaction()->commitAll();
            });
    }

    /**
     * Updates statistic set by given data and ID.
     *
     * @link /update/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdateSet() {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Set'); /* @var $statisticService \PM\Statistic\Service\Set */

        $this->getExecutor()
            ->add(function(Database $database, Request $request) {
                $database->getTransaction()->begin();
                return array('setData' => $request->getInput());
            })
            ->add('updateSet', $statisticService)
            ->add(function(Database $databasse) {
                $databasse->getTransaction()->commitAll();
            });
    }

    /**
     * Deletes statistic set by given ID.
     *
     * @link /delete/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteSet($id) {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Set'); /* @var $statisticService \PM\Statistic\Service\Set */

        $this->getExecutor()
        ->add(function(Database $database) {
            $database->getTransaction()->begin();
        })
        ->add('deleteSet', $statisticService)
        ->add(function(Database $databasse, $data) {
            $databasse->getTransaction()->commitAll();
            return $data;
        })->getResult()->setId($id);
    }
}
