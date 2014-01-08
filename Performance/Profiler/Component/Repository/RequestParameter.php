<?php

namespace PF\Profiler\Component\Repository;

use PF\Main\Abstracts\Repository;

/**
 * This script defines repository for measures request parameters.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class RequestParameter extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('request_parameter');
    }

    /**
     * Creates new parameter for request of mesure by given data.
     *
     * @param array $data Data of parameter
     *
     * @return int
     */
    public function create($data) {
        return parent::create($data);
    }

    /**
     * It provides create method for multiple parameters.
     *
     * @param array $parameters List of parameters
     *
     * @return int last insert id
     */
    public function massCreate($parameters) {
        return $this->getDatabase()
            ->insert()
            ->setTable($this->getTableName())
            ->massInsert($parameters)
            ->run();
    }
}
