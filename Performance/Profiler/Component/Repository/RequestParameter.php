<?php

/**
 * This script defines repository for measures request parameters.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Repository_RequestParameter extends Performance_Main_Abstract_Repository {

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
}
