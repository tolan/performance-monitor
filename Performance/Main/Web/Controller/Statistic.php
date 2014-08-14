<?php

namespace PF\Main\Web\Controller;

use PF\Main\Commander\Result;
use PF\Main\Database;
use PF\Main\Web\Component\Request;

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
     * @return type
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
}
