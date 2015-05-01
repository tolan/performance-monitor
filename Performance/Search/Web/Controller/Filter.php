<?php

namespace PM\Search\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;

/**
 * This scripts defines class of search filter controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 *
 * @link /search/filter
 */
class Filter extends Json {

    /**
     * Returns filter menu for search entities.
     *
     * @link /menu/{usage}
     *
     * @method GET
     *
     * @param enum $usage One of enum \PM\Search\Enum\Usage
     *
     * @return void
     */
    public function actionFilterMenu($usage) {
        $data = $this->getProvider()->get('PM\Search\Association')->getMenu($usage);
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
     * @link /options/{target}/{filter}
     *
     * @method GET
     *
     * @param enum $target One of enum \PM\Search\Enum\Target
     * @param enum $filter One of \PM\Search\Enum\Filter
     *
     * @return void
     */
    public function actionGetFilterParams($target, $filter) {
        $result = $this->getProvider()->get('PM\Search\Association')->getFilter($target, $filter);

        $this->setData($result);
    }
}
