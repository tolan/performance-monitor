<?php

namespace PF\scripts\Install;

use PF\Main\Abstracts;

/**
 * This script defines repository class for migrations version system.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    scripts
 */
class Repository extends Abstracts\Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('version');
    }

    /**
     * It returns a flag that is already created database.
     *
     * @return boolean
     */
    public function isExistsDatabase() {
        $result = true;
        try {
            $this->getDatabase()->getConnection();
        } catch (\Exception $exc) {
            $result = false;
        }

        return $result;
    }

    /**
     * Finds records with migration name.
     *
     * @param string $name Name of migration file
     *
     * @return array
     */
    public function findVersionByName($name) {
        $select = $this->getDatabase()
            ->select()
            ->from($this->getTableName())
            ->where('name = ?', $name);

        return $select->fetchAll();
    }

    /**
     * Creates new record into table for migration.
     *
     * @param string $name Name of migration file
     *
     * @return int
     */
    public function createVersion($name) {
        $data = array(
            'installed' => $this->getUtils()->convertTimeToMySQLDateTime(),
            'name'      => $name
        );

        return parent::create($data);
    }
}
