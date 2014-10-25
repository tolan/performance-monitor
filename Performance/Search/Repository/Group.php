<?php

namespace PM\Search\Repository;

use PM\Main\Abstracts\Repository;
use PM\Search\Entity;

/**
 * This script defines class for group repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Group extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('search_template_group');
    }

    /**
     * Gets groups for template.
     *
     * @param int $templateId ID of template
     *
     * @return \PM\Search\Entity\Group[]
     */
    public function getGroupsForTemplate($templateId) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('templateId = ?', $templateId);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id'] = (int)$item['id'];
            $item['templateId'] = (int)$item['templateId'];

            $result[] = new Entity\Group($item);
        }

        return $result;
    }

    /**
     * Gets group by given ID.
     *
     * @param int $id ID of group
     *
     * @return \PM\Search\Entity\Group
     */
    public function getGroup($id) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('id = ?', $id);

        $data = $select->fetchOne();

        $data['id']         = (int)$data['id'];
        $data['templateId'] = (int)$data['templateId'];

        return new Entity\Group($data);
    }

    /**
     * Creates new group entity into database.
     *
     * @param \PM\Search\Entity\Group $group Group instance
     *
     * @return \PM\Search\Entity\Group
     */
    public function createGroup(Entity\Group $group) {
        $data = array(
            'templateId'    => $group->get('templateId'),
            'target'        => $group->get('target'),
            'identificator' => $group->get('identificator')
        );
        $id = parent::create($data);

        return $group->setId($id);
    }

    /**
     * Updates group entity in database.
     *
     * @param \PM\Search\Entity\Group $group Group entity
     *
     * @return int
     */
    public function updateGroup(Entity\Group $group) {
        $data = array(
            'templateId'    => $group->get('templateId'),
            'target'        => $group->get('target'),
            'identificator' => $group->get('identificator')
        );

        return parent::update($group->getId(), $data);
    }

    /**
     * Deletes group by given ID.
     *
     * @param int $id ID of group
     *
     * @return int
     */
    public function deleteGroup($id) {
        return parent::delete($id);
    }
}
