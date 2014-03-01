<?php

namespace PF\Profiler\Component\Repository;

use PF\Main\Abstracts\Repository;

/**
 * This script defines repository for measures.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Measure extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('measure');
    }

    /**
     * Gets measures by id/s. For empty input it returns all measures.
     *
     * @param array|int $ids ID/s of measure
     *
     * @return array
     */
    public function getMeasures($ids = null) {
        $select = $this->getDatabase()
                ->select()
                ->from(array('pm' => $this->getTableName()));

        if ($ids) {
            $select->where('pm.id IN (:ids)', array(':ids' => $ids));
        }

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $result[$item['id']] = array(
                'id'          => $item['id'],
                'name'        => $item['name'],
                'description' => $item['description'],
                'edited'      => strtotime($item['edited'])*1000
            );
        }

        return $result;
    }

    /**
     * Get one measure by given id.
     *
     * @param int $id ID of measure
     *
     * @return array
     */
    public function getMeasure($id) {
        $select = $this->getDatabase()
            ->select()
            ->from(array('m' => $this->getTableName()), array('id' => 'id', 'name', 'description', 'edited'))
            ->joinLeft(array('mr' => 'measure_request'), 'mr.measureId = m.id', array('requestId' => 'id', 'measureId', 'url', 'method', 'toMeasure'))
            ->joinLeft(array('rp' => 'request_parameter'), 'rp.requestId = mr.id', array('methodParam' => 'method', 'nameParam' => 'name', 'value'))
            ->where('m.id = :id', array(':id' => $id));

        $data     = $select->fetchAll();
        $requests = array();
        $params   = array();

        foreach ($data as $item) {
            if ($item['nameParam']) {
                $params[$item['requestId']][] = array(
                    'method' => $item['methodParam'],
                    'name'   => $item['nameParam'],
                    'value'  => $item['value']
                );
            }

            if (isset($item['requestId'])) {
                $requests[$item['requestId']] = array(
                    'id'        => $item['requestId'],
                    'method'    => $item['method'],
                    'url'       => $item['url'],
                    'toMeasure' => (boolean)$item['toMeasure']
                );
            }
        }

        $result = array(
            'id'          => $data[0]['id'],
            'name'        => $data[0]['name'],
            'description' => $data[0]['description'],
            'edited'      => $data[0]['edited']
        );

        foreach ($requests as $request) {
            $request['parameters'] = isset($params[$request['id']]) ? $params[$request['id']] : array();
            $result['requests'][] = $request;
        }

        return $result;
    }

    /**
     * Delete measure by given id.
     *
     * @param int $id ID of measure
     *
     * @return int Count of affected rows
     */
    public function delete($id) {
        return parent::delete($id);
    }

    /**
     * Create new measure with data.
     *
     * @param array $data Array with data
     *
     * @return int Inserted ID
     */
    public function create($data) {
        $data['edited'] = $this->getUtils()->convertTimeToMySQLDateTime(time());
        return parent::create($data);
    }

    /**
     * Update measure by given id and data.
     *
     * @param int   $id   ID of measure
     * @param array $data Data for update
     *
     * @return int Count affected rows
     */
    public function update($id, $data) {
        $data['edited'] = $this->getUtils()->convertTimeToMySQLDateTime(time());
        return parent::update($id, $data);
    }
}
