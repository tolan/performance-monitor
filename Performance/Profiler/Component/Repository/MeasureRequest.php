<?php

/**
 * This script defines repository for measures requests.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Repository_MeasureRequest extends Performance_Main_Abstract_Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('measure_request');
    }

    /**
     * Creates new request for measure.
     *
     * @param array $data Data for request
     *
     * @return int
     */
    public function create($data) {
        $data['toMeasure'] = $data['toMeasure'] ? 1 : 0;
        return parent::create($data);
    }

    /**
     * Deletes request by given id.
     *
     * @param int $id ID of request
     *
     * @return int
     */
    public function delete($id) {
        return parent::delete($id);
    }
}
