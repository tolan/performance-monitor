<?php

namespace PM\Search\Repository;

use PM\Main\Abstracts\Repository;
use PM\Search\Entity;

/**
 * This script defines class for filter repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Filter extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('search_template_filter');
    }

    /**
     * Returns filters for list of group IDs.
     *
     * @param array $groupIds List of group IDs
     *
     * @return \PM\Search\Entity\Filter[]
     */
    public function getFiltersForGroups($groupIds) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('groupId IN (?)', $groupIds);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id']      = (int)$item['id'];
            $item['groupId'] = (int)$item['groupId'];

            $result[] = new Entity\Filter($item);
        }

        return $result;
    }

    /**
     * Creates new filter entity to database.
     *
     * @param \PM\Search\Entity\Filter $filter Filter instance
     *
     * @return \PM\Search\Entity\Filter
     */
    public function createFilter(Entity\Filter $filter) {
        $data = array(
            'groupId'  => $filter->get('groupId'),
            'target'   => $filter->get('target'),
            'filter'   => $filter->get('filter'),
            'operator' => $filter->get('operator'),
            'value'    => $filter->get('value')
        );
        $id = parent::create($data);

        return $filter->setId($id);
    }

    /**
     * Updates filter entity in database.
     *
     * @param \PM\Search\Entity\Filter $filter Filter instance
     *
     * @return int
     */
    public function updateFilter(Entity\Filter $filter) {
        $data = array(
            'groupId'  => $filter->get('groupId'),
            'target'   => $filter->get('target'),
            'filter'   => $filter->get('filter'),
            'operator' => $filter->get('operator'),
            'value'    => $filter->get('value')
        );

        return parent::update($filter->getId(), $data);
    }

    /**
     * Deletes filter by given ID.
     *
     * @param int $id ID of filter
     *
     * @return int
     */
    public function deleteFilter($id) {
        return parent::delete($id);
    }
}
