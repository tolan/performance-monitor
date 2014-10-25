<?php

namespace PM\Statistic\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;
use PM\Main\Web\Component\Request;
use PM\Main\Database;

/**
 * This scripts defines class of statistic template controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 *
 * @link /statistic/template
 */
class Template extends Json {

    /**
     * This find all statistic templates in database.
     *
     * @link /find
     *
     * @method GET
     *
     * @return void
     */
    public function actionFindTemplates() {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Template'); /* @var $statisticService \PM\Statistic\Service\Template */

        $this->getExecutor()
            ->add('findTemplates', $statisticService);
    }

    /**
     * Gets statistic template by given ID.
     *
     * @link /get/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetTemplate($id) {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Template'); /* @var $statisticService \PM\Statistic\Service\Template */

        $this->getExecutor()
            ->clean()
            ->add('getTemplate', $statisticService, array('id' => $id));
    }

    /**
     * Creates new statistic template by given data.
     *
     * @link /create
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreateTemplate() {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Template'); /* @var $statisticService \PM\Statistic\Service\Template */

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
     * @link /update/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdateTemplate() {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Template'); /* @var $statisticService \PM\Statistic\Service\Template */

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
     * @link /delete/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteTemplate($id) {
        $statisticService = $this->getProvider()->get('\PM\Statistic\Service\Template'); /* @var $statisticService \PM\Statistic\Service\Template */

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
}
