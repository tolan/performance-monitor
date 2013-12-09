<?php

/**
 * This script defines repository for measures.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Repository_Measure extends Performance_Main_Abstract_Repository {

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
                ->from(array('pm' => 'profiler_measure'))
                ->joinLeft(array('pmp' => 'profiler_measure_parameter'), 'pm.id = pmp.profiler_measure_id', array('key', 'value'));

        if ($ids) {
            $select->where('pm.id IN (?)', $ids);
        }

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $parameters = isset($result[$item['id']]) ? $result[$item['id']]['parameters'] : array();

            if (!empty($item['key']) && !empty($item['value'])) {
                $parameters[] = array(
                    'key'   => $item['key'],
                    'value' => $item['value']
                );
            }

            $result[$item['id']] = array(
                'id'          => $item['id'],
                'name'        => $item['name'],
                'description' => $item['description'],
                'link'        => $item['link'],
                'edited'      => strtotime($item['edited'])*1000,
                'parameters'  => $parameters
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
        $data = $this->getMeasures(array($id));
        return $data[$id];
    }

    /**
     * Delete measure by given id.
     *
     * @param int $id ID of measure
     *
     * @return int Count of affected rows
     */
    public function delete($id) {
        return $this->getDatabase()
            ->delete()
            ->setTable('profiler_measure')
            ->where('id = ?', $id)
            ->run();
    }

    /**
     * Create new measure with data.
     *
     * @param array $data Array with data
     *
     * @return int Inserted ID
     */
    public function create($data) {
        return $this->getDatabase()
            ->insert()
            ->setTable('profiler_measure')
            ->setInsertData($data)
            ->run();
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
        return $this->getDatabase()
            ->update()
            ->setTable('profiler_measure')
            ->setUpdateData($data)
            ->where('id = ?', $id)
            ->run();
    }

    /**
     * Delete all measure parameters by given measure id.
     *
     * @param int $id ID of measure
     *
     * @return int Count of affected rows
     */
    public function deleteParameters($id) {
        return $this->getDatabase()
            ->delete()
            ->setTable('profiler_measure_parameter')
            ->where('profiler_measure_id = ?', $id)
            ->run();
    }

    /**
     * Set new parameters for measure by given measure id.
     *
     * @param int   $id     ID of measure
     * @param array $params Array with arrays of paramaters
     *
     * @return array Array with inserted IDs
     */
    public function setParameters($id, $params) {
        $ids = array();
        foreach ($params as $param) {
            if (!empty($param['key']) && !empty($param['value'])) {
                $insert = array(
                    'profiler_measure_id' => $id,
                    'key'                 => $param['key'],
                    'value'               => $param['value']
                );
                $ids[] = $this->getDatabase()
                    ->insert()
                    ->setTable('profiler_measure_parameter')
                    ->setInsertData($insert)
                    ->run();
            }
        }

        return $ids;
    }
}
