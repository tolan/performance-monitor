<?php

namespace PF\Profiler\Repository;

use PF\Main\Abstracts\Repository;
use PF\Profiler\Entity;

/**
 * This script defines class for scenario repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Scenario extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('scenario');
    }

    /**
     * Find all scenarios in database.
     *
     * @return array
     */
    public function findScenarios() {
        $select = $this->getDatabase()
                ->select()
                ->from(array('s' => $this->getTableName()));

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id']     = (int)$item['id'];
            $item['edited'] = strtotime($item['edited']) * 1000;

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Creates new scenario.
     *
     * @param \PF\Profiler\Entity\Scenario $scenario Scenarion entity instance
     *
     * @return \PF\Profiler\Entity\Scenario
     */
    public function create(Entity\Scenario $scenario) {
        $data = array(
            'name'        => $scenario->get('name', ''),
            'description' => $scenario->get('description', ''),
            'edited'      => $scenario->get('edited', $this->getUtils()->convertTimeToMySQLDateTime())
        );

        $id = parent::create($data);

        $scenario->setId($id);

        return $scenario;
    }

    /**
     * Updates scenario by given entity.
     *
     * @param \PF\Profiler\Entity\Scenario $scenario Scenario entity instance
     *
     * @return boolean
     */
    public function update(Entity\Scenario $scenario) {
        $data = array(
            'name'        => $scenario->get('name', ''),
            'description' => $scenario->get('description', ''),
            'edited'      => $scenario->get('edited', $this->getUtils()->convertTimeToMySQLDateTime())
        );

        parent::update($scenario->getId(), $data);

        return true;
    }

    /**
     * Deletes scenario by given ID.
     *
     * @param int $id ID of scenario
     *
     * @return boolean
     */
    public function delete($id) {
        parent::delete($id);

        return true;
    }

    /**
     * Returns scenario entity by given ID.
     *
     * @param int $id ID of scenario
     * 
     * @return \PF\Profiler\Entity\Scenario
     */
    public function getScenario($id) {
        $select = $this->getDatabase()->select()
            ->from($this->getTableName())
            ->where('id = ?', $id);

        $data = $select->fetchOne();

        $data['id']     = (int)$data['id'];
        $data['edited'] = $this->getUtils()->convertTimeFromMySQLDateTime($data['edited']);

        return new Entity\Scenario($data);
    }
}
