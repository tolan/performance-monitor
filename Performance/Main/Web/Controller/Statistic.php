<?php

namespace PF\Main\Web\Controller;

use PF\Main\Commander\Result;
use PF\Main\Database;
use PF\Main\Web\Component\Request;
use PF\Statistic\Gearman\Client;

/**
 * This scripts defines class for statistic controller.
 *
 * @author     Martin Kovar
 * @category   Statistic
 * @package    Main
 *
 * @link /statistic
 */
class Statistic extends Abstracts\Json {

    /**
     * This find all statistic templates in database.
     *
     * @link /templates
     *
     * @method GET
     *
     * @return void
     */
    public function actionFindTemplates() {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Template'); /* @var $statisticService \PF\Statistic\Service\Template */

        $this->getExecutor()
            ->add('findTemplates', $statisticService);
    }

    /**
     * Gets configuration and references for menus, line - type map, etc.
     *
     * @link /views/config
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetConfig() {
        $associtaion = $this->getProvider()->get('\PF\Statistic\Association'); /* @var $associtaion \PF\Statistic\Association */

        $this->getExecutor()
            ->add('getConfig', $associtaion)
            ->add(function(Result $result) {
                $data = $result->toArray();
                unset($data['input']);

                return array('data' => $data);
            });
    }

    /**
     * Gets statistic template by given ID.
     *
     * @link /template/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetTemplate($id) {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Template'); /* @var $statisticService \PF\Statistic\Service\Template */

        $this->getExecutor()
            ->clean()
            ->add('getTemplate', $statisticService)
            ->getResult()
            ->setId($id);
    }

    /**
     * Creates new statistic template by given data.
     *
     * @link /template
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreateTemplate() {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Template'); /* @var $statisticService \PF\Statistic\Service\Template */

        $this->getExecutor()
            ->add(function(Database $database, Request $request) {
                $database->getTransaction()->begin();
                return array('templateData' => $request->getInput());
            })
            ->add('createTemplate', $statisticService)
            ->add(function(Database $databasse) {
                $databasse->getTransaction()->commitAll();
            });
    }

    /**
     * Updates statistic template by given data and ID.
     *
     * @link /template/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdateTemplate() {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Template'); /* @var $statisticService \PF\Statistic\Service\Template */

        $this->getExecutor()
            ->add(function(Database $database, Request $request) {
                $database->getTransaction()->begin();
                return array('templateData' => $request->getInput());
            })
            ->add('updateTemplate', $statisticService)
            ->add(function(Database $databasse) {
                $databasse->getTransaction()->commitAll();
            });
    }

    /**
     * Deletes statistic template by given ID.
     *
     * @link /template/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteTemplate($id) {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Template'); /* @var $statisticService \PF\Statistic\Service\Template */

        $this->getExecutor()
        ->add(function(Database $database) {
            $database->getTransaction()->begin();
        })
        ->add('deleteTemplate', $statisticService)
        ->add(function(Database $databasse, $data) {
            $databasse->getTransaction()->commitAll();
            return $data;
        })->getResult()->setId($id);
    }

    /**
     * This find all statistic sets in database.
     *
     * @link /sets
     *
     * @method GET
     *
     * @return void
     */
    public function actionFindSets() {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Set'); /* @var $statisticService \PF\Statistic\Service\Set */

        $this->getExecutor()->add('findSets', $statisticService);
    }

    /**
     * Creates new statistic set by given data.
     *
     * @link /set
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreateSet() {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Set'); /* @var $statisticService \PF\Statistic\Service\Set */

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
     * @link /set/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdateSet() {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Set'); /* @var $statisticService \PF\Statistic\Service\Set */

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
     * Gets statistic set by given ID.
     *
     * @link /set/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetSet($id) {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Set'); /* @var $statisticService \PF\Statistic\Service\Set */

        $includeRuns = $this->getProvider()->get('utils')->convertToBoolean(
            $this->getRequest()->getGet()->get('includeRuns', false)
        );

        $this->getExecutor()
            ->clean()
            ->add('getSet', $statisticService)
            ->getResult()
            ->setId($id)
            ->setIncludeRuns($includeRuns);
    }

    /**
     * Deletes statistic set by given ID.
     *
     * @link /set/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteSet($id) {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Set'); /* @var $statisticService \PF\Statistic\Service\Set */

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

    /**
     * Deletes statistic set run by given ID.
     *
     * @link /set/run/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteRun($id) {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Run'); /* @var $statisticService \PF\Statistic\Service\Run */

        $this->getExecutor()
        ->add(function(Database $database) {
            $database->getTransaction()->begin();
        })
        ->add('deleteRun', $statisticService)
        ->add(function(Database $databasse, $data) {
            $databasse->getTransaction()->commitAll();
            return $data;
        })->getResult()->setId($id);
    }

    /**
     * Starts new statistic set run.
     *
     * @link /set/run/{id}/start
     *
     * @method POST
     *
     * @return void
     */
    public function actionStartRun($id) {
        $runService = $this->getProvider()->get('\PF\Statistic\Service\Run'); /* @var $runService \PF\Statistic\Service\Run */

        $this->getExecutor()
            ->add(function(Database $database) {
                $database->getTransaction()->begin();
            })
            ->add('createRun', $runService)
            ->add(function(Database $database) {
                $database->getTransaction()->commitAll();
            })
            ->add(function(Client $client, $data) {
                $client->setData(array('id' => $data->getId()))
                    ->doAsynchronize();
            })
            ->getResult()
            ->setSetId($id);
    }

    /**
     * Gets statistic run by given ID.
     *
     * @link /set/run/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetRun($id) {
        $statisticService = $this->getProvider()->get('\PF\Statistic\Service\Run'); /* @var $statisticService \PF\Statistic\Service\Run */

        $includeData = $this->getProvider()->get('utils')->convertToBoolean(
            $this->getRequest()->getGet()->get('includeData', false)
        );

        $includeTemplate = $this->getProvider()->get('utils')->convertToBoolean(
            $this->getRequest()->getGet()->get('includeTemplate', false)
        );

        $this->getExecutor()
            ->clean()
            ->add('getRun', $statisticService)
            ->getResult()
            ->setId($id)
            ->setIncludeData($includeData)
            ->setIncludeTemplate($includeTemplate);
    }
}
