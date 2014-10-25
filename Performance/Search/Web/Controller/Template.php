<?php

namespace PM\Search\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;
use PM\Main\Web\Component\Request;
use PM\Main\Database;

/**
 * This scripts defines class of search template controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 *
 * @link /search/template
 */
class Template extends Json {

    /**
     * This find all search templates in database.
     *
     * @link /find/{usage}
     *
     * @method GET
     *
     * @return void
     */
    public function actionFindTemplates($usage) {
        $searchService = $this->getProvider()->get('\PM\Search\Service\Template'); /* @var $searchService \PM\Search\Service\Template */

        $this->getExecutor()
            ->add('findTemplates', $searchService, array('usage' => $usage));
    }

    /**
     * Get search template by ID.
     *
     * @link /get/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetTemplate($id) {
        $searchService = $this->getProvider()->get('\PM\Search\Service\Template'); /* @var $searchService \PM\Search\Service\Template */

        $this->getExecutor()
            ->add('getTemplate', $searchService)
            ->getResult()
            ->setId($id);
    }

    /**
     * Creates new search template by given data.
     *
     * @link /create
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreateTemplate() {
        $searchService = $this->getProvider()->get('\PM\Search\Service\Template'); /* @var $searchService \PM\Search\Service\Template */

        $this->getExecutor()
        ->add(function(Database $database, Request $request) {
            $database->getTransaction()->begin();
            return array('templateData' => $request->getInput());
        })
        ->add('createTemplate', $searchService)
        ->add(function(Database $databasse) {
            $databasse->getTransaction()->commitAll();
        });
    }

    /**
     * Updates search template by given data and ID.
     *
     * @link /update/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdateTemplate($id) {
        $searchService = $this->getProvider()->get('\PM\Search\Service\Template'); /* @var $searchService \PM\Search\Service\Template */
        /* @var $scenarioService \PM\Profiler\Service\Scenario */

        $this->getExecutor()
        ->add(function(Database $database, $input) use ($id) {
            $database->getTransaction()->begin();
            $input['id'] = $id;
            return array('templateData' => $input);
        })
        ->add('updateTemplate', $searchService)
        ->add(function(Database $databasse) {
            $databasse->getTransaction()->commitAll();
        });
    }

    /**
     * Deletes search template by given ID.
     *
     * @link /delete/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteTemplate($id) {
        $searchService = $this->getProvider()->get('\PM\Search\Service\Template'); /* @var $searchService \PM\Search\Service\Template */

        $this->getExecutor()
        ->add(function(Database $database) {
            $database->getTransaction()->begin();
        })
        ->add('deleteTemplate', $searchService, array('id' => $id))
        ->add(function(Database $databasse, $data) {
            $databasse->getTransaction()->commitAll();
            return $data;
        });
    }
}
