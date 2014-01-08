<?php

namespace PF\Main\Web\Controller;

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
        $data = $this->getProvider()->get('PF\Search\Component\Association')->getMenu();
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
     * @return void
     */
    public function actionGetFilterParams($params) {
        $result = $this->getProvider()->get('PF\Search\Component\Association')->getFilter($params['target'], $params['filter']);

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

        $find = $this->getProvider()->get('PF\Search\Engine')->find($data['filters'], $data['target']);

        $this->setData(array(
            'target' => $data['target'],
            'result' => $find
        ));
    }
}
