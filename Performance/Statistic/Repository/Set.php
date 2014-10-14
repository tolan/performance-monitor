<?php

namespace PF\Statistic\Repository;

use PF\Main\Abstracts\Repository;
use PF\Statistic\Entity;

/**
 * This script defines class for set repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Set extends Repository {

    const TEMPLATES_TABLE = 'statistic_set_template';

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('statistic_set');
    }

    /**
     * Returns list of statistic set entities.
     *
     * @return \PF\Statistic\Entity\Set[]
     */
    public function findSets() {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName());

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id'] = (int)$item['id'];

            $result[] = new Entity\Set($item);
        }

        return $result;
    }

    /**
     * Returns statistic set entity by given id.
     *
     * @param int $id Id of statistic set
     *
     * @return Entity\Set
     */
    public function getSet($id) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('id = ?', $id);

        $data       = $select->fetchOne();
        $data['id'] = (int)$data['id'];

        $set = new Entity\Set($data);

        return $set;
    }

    /**
     * Creates new statistic set into database.
     *
     * @param Entity\Set $set Statistic set entity instance
     *
     * @return Entity\Set
     */
    public function createSet(Entity\Set $set) {
        $data = array(
            'name'        => $set->get('name'),
            'description' => $set->get('description', null)
        );

        $id = parent::create($data);

        return $set->setId($id);
    }

    /**
     * Updates statistic set entity by given instance.
     *
     * @param Entity\Set $set Statistic set entity instance
     *
     * @return int
     */
    public function updateSet(Entity\Set $set) {
        $data = array(
            'name'        => $set->get('name'),
            'description' => $set->get('description', null)
        );

        return parent::update($set->getId(), $data);
    }

    /**
     * Deletes statistic set entity by given ID.
     *
     * @param int $id ID of statistic set
     *
     * @return int
     */
    public function deleteSet($id) {
        return parent::delete($id);
    }

    /**
     * It assigns statistic templates to statistic set.
     *
     * @param int   $setId     Id of statistic set
     * @param array $templates Set of statistic template ids
     *
     * @return array Set of statistic template ids
     */
    public function assignTemplates($setId, $templates) {
        $insert = $this->getDatabase()->insert();
        $data   = array();

        foreach ($templates as $template) {
            $data[] = array(
                'statisticSetId'      => $setId,
                'statisticTemplateId' => $template
            );
        }

        $insert->setTable(self::TEMPLATES_TABLE)
                ->massInsert($data)
                ->run();

        return $templates;
    }

    /**
     * Deletes relation between templates and statistic set.
     *
     * @param int $setId Id of statistic set
     *
     * @return int
     */
    public function deleteTemplates($setId) {
        $delete = $this->getDatabase()->delete()
            ->setTable(self::TEMPLATES_TABLE)
            ->where('statisticSetId = ?', $setId);

        return $delete->run();
    }

    /**
     * Returns list of attached template ids to statistic set.
     *
     * @param int $setId Id of statistic set
     *
     * @return array
     */
    public function getTemplates($setId) {
        $select = $this->getDatabase()
                ->select()
                ->from(self::TEMPLATES_TABLE)
                ->where('statisticSetId = ?', $setId);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $result[] = (int)$item['statisticTemplateId'];
        }

        return $result;
    }
}
