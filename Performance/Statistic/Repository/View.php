<?php

namespace PM\Statistic\Repository;

use PM\Main\Abstracts\Repository;
use PM\Statistic\Entity;

/**
 * This script defines class for view repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class View extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('statistic_view');
    }

    /**
     * Returns view entities for statistic template by given template ID.
     *
     * @param int $templateId ID of template
     *
     * @return \PM\Statistic\Entity\View[]
     */
    public function getViewsForTemplate($templateId) {
        $select = $this->getDatabase()->select()
            ->from($this->getTableName())
            ->where('templateId = ?', $templateId);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id']         = (int)$item['id'];
            $item['templateId'] = (int)$item['templateId'];

            $result[] = new Entity\View($item);
        }

        return $result;
    }

    /**
     * Creates new view entity into database.
     *
     * @param \PM\Statistic\Entity\View $view View entity instance
     *
     * @return \PM\Statistic\Entity\View
     */
    public function createView(Entity\View $view) {
        $data = array(
            'templateId' => $view->get('templateId'),
            'target'     => $view->get('target'),
            'type'       => $view->get('type')
        );

        $id = parent::create($data);

        return $view->setId($id);
    }

    /**
     * Updates view entity in database.
     *
     * @param \PM\Statistic\Entity\View $view View entity instance
     *
     * @return int
     */
    public function updateView(Entity\View $view) {
        $data = array(
            'templateId' => $view->get('templateId'),
            'target'     => $view->get('target'),
            'type'       => $view->get('type')
        );

        return parent::update($view->getId(), $data);
    }

    /**
     * Deletes view entity by given ID.
     *
     * @param int $id ID of view
     *
     * @return int
     */
    public function deleteView($id) {
        return parent::delete($id);
    }
}
