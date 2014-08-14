<?php

namespace PF\Statistic\Repository;

use PF\Main\Abstracts\Repository;
use PF\Statistic\Entity;

/**
 * This script defines class for template repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Template extends Repository {

    const ITEMS_TABLE = 'statistic_source_item';

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('statistic_template');
    }

    /**
     * Returns list of statistic template entities.
     *
     * @return \PF\Statistic\Entity\Template[]
     */
    public function findTemplates() {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName());

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id']               = (int)$item['id'];
            $item['sourceTemplateId'] = (int)$item['sourceTemplateId'];

            $result[] = new Entity\Template($item);
        }

        return $result;
    }

    /**
     * Returns list of assigned items for statistic template.
     *
     * @param int $templateId ID of statistic template
     *
     * @return array
     */
    public function getItemsForTemplate($templateId) {
        $select = $this->getDatabase()->select()
            ->from(self::ITEMS_TABLE)
            ->where('templateId = ?', $templateId);

        $data = $select->fetchAll();

        foreach ($data as $key => $item) {
            $data[$key] = (int)$item['sourceId'];
        }

        return $data;
    }

    /**
     * Deletes items assigned to statistic template entity by given template ID.
     *
     * @param int $templateId Statistic template ID
     *
     * @return int
     */
    public function deleteItemsForTemplate($templateId) {
        $delete = $this->getDatabase()->delete()
            ->setTable(self::ITEMS_TABLE)
            ->where('templateId = ?', $templateId);

        return $delete->run();
    }

    /**
     * Assigns items to statistic template.
     *
     * @param int   $templateId ID of statistic template
     * @param array $items      List of item to assign
     *
     * @return array
     */
    public function assignItemsToTemplate($templateId, array $items = array()) {
        $insert = $this->getDatabase()->insert();
        $data   = array();

        foreach ($items as $item) {
            $data[] = array(
                'templateId' => $templateId,
                'sourceId'   => $item
            );
        }

        $insert->setTable(self::ITEMS_TABLE)
                ->massInsert($data)
                ->run();

        return $items;
    }

    /**
     * Returns statistic template entity by given ID.
     *
     * @param int $id ID of statistic template
     *
     * @return \PF\Statistic\Entity\Template
     */
    public function getTemplate($id) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('id = ?', $id);

        $data                     = $select->fetchOne();
        $data['id']               = (int)$data['id'];
        $data['sourceTemplateId'] = (int)$data['sourceTemplateId'];

        $template = new Entity\Template($data);

        return $template;
    }

    /**
     * Creates new statistic template entity in database.
     *
     * @param \PF\Statistic\Entity\Template $template Statistic template entity instance
     *
     * @return \PF\Statistic\Entity\Template
     */
    public function createTemplate(Entity\Template $template) {
        $source         = $template->get('source');
        $searchTemplate = $source['template'];

        $data = array(
            'name'             => $template->get('name'),
            'description'      => $template->get('description', null),
            'sourceType'       => $source['type'],
            'sourceTemplateId' => $searchTemplate['id']
        );

        $id = parent::create($data);

        return $template->setId($id);
    }

    /**
     * Updates statistic template entity in database.
     *
     * @param \PF\Statistic\Entity\Template $template Statistic template entity instance
     *
     * @return int
     */
    public function updateTemplate(Entity\Template $template) {
        $source         = $template->get('source');
        $searchTemplate = $source['template'];

        $data = array(
            'name'             => $template->get('name'),
            'description'      => $template->get('description', null),
            'sourceType'       => $source['type'],
            'sourceTemplateId' => $searchTemplate['id']
        );

        return parent::update($template->getId(), $data);
    }

    /**
     * Deletes statistic template entity by given ID.
     *
     * @param int $id ID of statistic template
     *
     * @return int
     */
    public function deleteTemplate($id) {
        return parent::delete($id);
    }
}
