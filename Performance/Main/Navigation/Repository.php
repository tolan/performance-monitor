<?php

namespace PM\Main\Navigation;

use PM\Main\Abstracts;

/**
 * This script defines repository class for navigation menu.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Repository extends Abstracts\Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('navigation');
    }

    /**
     * Returns all items for navigation.
     *
     * @return Navigation\Entity[]
     */
    public function getMenuItems() {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName());

        $data = $select->fetchAll();
        $result = array();

        foreach ($data as $item) {
            $item['id']       = (int)$item['id'];
            $item['parentId'] = (int)$item['parentId'];
            $item['order']    = (int)$item['order'];

            if (empty($item['href'])) {
                unset($item['href']);
            }

            $result[] = new Entity($item);
        }

        return $result;
    }
}
