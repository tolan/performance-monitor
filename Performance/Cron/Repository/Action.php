<?php

namespace PM\Cron\Repository;

use PM\Main\Abstracts\Repository;
use PM\Cron;

/**
 * This script defines class for manage cron actions.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Action extends Repository {

    const ITEMS_TABLE = 'cron_source_item';

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('cron_trigger_source');
    }

    /**
     * Returns actions for triggers.
     *
     * @param array $triggersIds Set of triggers ids
     *
     * @return Cron\Entity\Action[]
     */
    public function getActionsForTriggers($triggersIds) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('cronTriggerId IN (?)', $triggersIds);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id']               = (int)$item['id'];
            $item['cronTriggerId']    = (int)$item['cronTriggerId'];
            $item['sourceTemplateId'] = (int)$item['sourceTemplateId'];

            $result[] = new Cron\Entity\Action($item);
        }

        return $result;
    }

    /**
     * It creates new cron action.
     *
     * @param Cron\Entity\Action $action Cron entity action instance
     *
     * @return Cron\Entity\Action
     */
    public function createAction(Cron\Entity\Action $action) {
        $data = array(
            'cronTriggerId'    => $action->get('cronTriggerId'),
            'sourceType'       => $action->get('sourceType'),
            'sourceTemplateId' => $action->get('sourceTemplateId')
        );
        $id = parent::create($data);

        return $action->setId($id);
    }

    /**
     * It updates existed cron action.
     *
     * @param Cron\Entity\Action $action Cron entity action instance
     *
     * @return int
     */
    public function updateAction(Cron\Entity\Action $action) {
        $data = array(
            'cronTriggerId'    => $action->get('cronTriggerId'),
            'sourceType'       => $action->get('sourceType'),
            'sourceTemplateId' => $action->get('sourceTemplateId')
        );

        return parent::update($action->getId(), $data);
    }

    /**
     * It deletes cron action.
     *
     * @param int $id Cron action id
     *
     * @return int
     */
    public function deleteAction($id) {
        return parent::delete($id);
    }

    /**
     * It assigns cource items to cron action.
     *
     * @param int   $actionId Id of cron action
     * @param array $items    Set of source items ids
     *
     * @return array
     */
    public function assignItemsToAction($actionId, array $items = array()) {
        $insert = $this->getDatabase()->insert();
        $data   = array();

        foreach ($items as $item) {
            $data[] = array(
                'cronTriggerSourceId' => $actionId,
                'sourceId'            => $item
            );
        }

        $insert->setTable(self::ITEMS_TABLE)
                ->massInsert($data)
                ->run();

        return $items;
    }

    /**
     * It deletes source items assigned to cron action.
     *
     * @param int $actionId Id of cron action
     *
     * @return int
     */
    public function deleteItemsToAction($actionId) {
        $delete = $this->getDatabase()->delete()
            ->setTable(self::ITEMS_TABLE)
            ->where('cronTriggerSourceId = ?', $actionId);

        return $delete->run();
    }

    /**
     * It returns source items for cron action.
     *
     * @param int $actionId Id of cron action
     *
     * @return array
     */
    public function getItemsForAction($actionId) {
        $select = $this->getDatabase()
                ->select()
                ->from(self::ITEMS_TABLE)
                ->where('cronTriggerSourceId = ?', $actionId);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $result[] = (int)$item['sourceId'];
        }

        return $result;
    }
}
