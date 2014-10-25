<?php

namespace PM\Statistic\Repository;

use PM\Main\Abstracts\Repository;
use PM\Statistic\Entity;

/**
 * This script defines class for line repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Line extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('statistic_view_line');
    }

    /**
     * Returns list of line entities for view IDs.
     *
     * @param array $viewIds List of view IDs
     *
     * @return \PM\Statistic\Entity\Line[]
     */
    public function getLinesForViews($viewIds) {
        $select = $this->getDatabase()->select()
            ->from($this->getTableName())
            ->where('viewId IN (?)', $viewIds);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id']     = (int)$item['id'];
            $item['viewId'] = (int)$item['viewId'];
            $item['value']  = json_decode($item['value'], true);

            $result[] = new Entity\Line($item);
        }

        return $result;
    }

    /**
     * Creates new line entity into database.
     *
     * @param \PM\Statistic\Entity\Line $line Line entity instance
     *
     * @return \PM\Statistic\Entity\Line
     */
    public function createLine(Entity\Line $line) {
        $value = json_encode($line->get('value'));
        $data = array(
            'viewId'   => $line->get('viewId'),
            'function' => $line->get('function'),
            'type'     => $line->get('type'),
            'value'    => $value
        );

        $id = parent::create($data);

        return $line->setId($id);
    }

    /**
     * Updates line entity in database.
     *
     * @param \PM\Statistic\Entity\Line $line Line entity instance
     *
     * @return int
     */
    public function updateLine(Entity\Line $line) {
        $value = json_encode($line->get('value'));
        $data = array(
            'viewId'   => $line->get('viewId'),
            'function' => $line->get('function'),
            'type'     => $line->get('type'),
            'value'    => $value
        );

        return parent::update($line->getId(), $data);
    }

    /**
     * Deletes line entity by given ID.
     *
     * @param int $id ID of line entity.
     *
     * @return int
     */
    public function deleteLine($id) {
        return parent::delete($id);
    }
}
