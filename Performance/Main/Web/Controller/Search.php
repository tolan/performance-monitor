<?php

namespace PF\Main\Web\Controller;

use PF\Main\Database;
use PF\Main\Web\Component\Request;

/**
 * This scripts defines class for search controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @link /search
 */
class Search extends Abstracts\Json {

    /**
     * Returns filter menu for search entities.
     *
     * @link /filters/menu
     *
     * @method GET
     *
     * @return void
     */
    public function actionFilterMenu() {
        $data = $this->getProvider()->get('PF\Search\Association')->getMenu();
        $menu = array();

        foreach ($data as $target => $filters) {
            $submenu = array();
            foreach (array_keys($filters) as $filter) {
                $submenu[] = array(
                    'text'   => 'search.filter.'.$target.'.'.$filter,
                    'target' => $target,
                    'filter' => $filter
                );
            }

            $menu[$target] = array(
                'text'    => 'search.filter.target.'.$target,
                'submenu' => $submenu
            );
        }

        $this->setData($menu);
    }

    /**
     * Returns options for filter by given target entity and filter.
     *
     * @link /filter/{target}/{filter}
     *
     * @method GET
     *
     * @param enum $target One of enum \PF\Search\Enum\Target
     * @param enum $filter One of \PF\Search\Enum\Filter
     *
     * @return void
     */
    public function actionGetFilterParams($target, $filter) {
        $result = $this->getProvider()->get('PF\Search\Association')->getFilter($target, $filter);

        $this->setData($result);
    }

    /**
     * It provides entry point for finding entities by given filters and target entity.
     * It returns a list of matching entities.
     * Request method is POST because method GET has not body as POST for sets filters.
     *
     * @link /find
     *
     * @method POST
     *
     * @return void
     */
    public function actionFind() {
        $data = $this->getRequest()->getInput();
        $find = $this->getProvider()->get('PF\Search\Engine')->find($data['template']);

        $this->setData(array(
            'target' => $data['template']['target'],
            'result' => $find
        ));
    }

    /**
     * This find all search templates in database.
     *
     * @link /templates/{usage}
     *
     * @method GET
     *
     * @return void
     */
    public function actionFindTemplates($usage) {
        $searchService = $this->getProvider()->get('\PF\Search\Service\Template'); /* @var $searchService \PF\Search\Service\Template */

        $this->getExecutor()
            ->add('findTemplates', $searchService)
            ->getResult()
            ->setUsage($usage);
    }

    /**
     * Get search template by ID.
     *
     * @link /template/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetTemplate($id) {
        $searchService = $this->getProvider()->get('\PF\Search\Service\Template'); /* @var $searchService \PF\Search\Service\Template */

        $this->getExecutor()
            ->add('getTemplate', $searchService)
            ->getResult()
            ->setId($id);
    }

    /**
     * Creates new search template by given data.
     *
     * @link /template
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreateTemplate() {
        $searchService = $this->getProvider()->get('\PF\Search\Service\Template'); /* @var $searchService \PF\Search\Service\Template */

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
     * @link /template/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdateTemplate($id) {
        $searchService = $this->getProvider()->get('\PF\Search\Service\Template'); /* @var $searchService \PF\Search\Service\Template */
        /* @var $scenarioService \PF\Profiler\Service\Scenario */

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
     * @link /template/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteTemplate($id) {
        $searchService = $this->getProvider()->get('\PF\Search\Service\Template'); /* @var $searchService \PF\Search\Service\Template */

        $this->getExecutor()
        ->add(function(Database $database) {
            $database->getTransaction()->begin();
        })
        ->add('deleteTemplate', $searchService)
        ->add(function(Database $databasse, $data) {
            $databasse->getTransaction()->commitAll();
            return $data;
        })->getResult()->setId($id);
    }
}
