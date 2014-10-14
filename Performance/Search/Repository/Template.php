<?php

namespace PF\Search\Repository;

use PF\Main\Abstracts\Repository;
use PF\Search\Entity;

/**
 * This script defines class for template repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Template extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('search_template');
    }

    /**
     * Find templates for usage.
     *
     * @param string $usage Usage of templates (optional)
     *
     * @return \PF\Search\Entity\Template[]
     */
    public function findTemplates($usage = null) {
        $select = $this->getDatabase()
                ->select()
                ->from(array('st' => $this->getTableName()));

        if ($usage !== null) {
            $select->where('st.usage = ?', $usage);
        }

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id'] = (int)$item['id'];

            $result[] = new Entity\Template($item);
        }

        return $result;
    }

    /**
     * Gets search template by given ID.
     *
     * @param int $id ID of template
     *
     * @return \PF\Search\Entity\Template
     */
    public function getTemplate($id) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('id = ?', $id);

        $data            = $select->fetchOne();
        $data['id']      = (int)$data['id'];
        $data['visible'] = (int)$data['visible'] === 0 ? false : true;

        $result = new Entity\Template($data);

        return $result;
    }

    /**
     * Creates new search template into database.
     *
     * @param \PF\Search\Entity\Template $template Template instance
     *
     * @return \PF\Search\Entity\Template
     */
    public function createTemplate(Entity\Template $template) {
        $data = array(
            'target'      => $template->get('target'),
            'usage'       => $template->get('usage'),
            'name'        => $template->get('name'),
            'logic'       => $template->get('logic'),
            'description' => $template->get('description', ''),
            'visible'     => $template->get('visible', false)
        );
        $id = parent::create($data);

        return $template->setId($id);
    }

    /**
     * Updates search template in database.
     *
     * @param \PF\Search\Entity\Template $template Template instance
     *
     * @return int
     */
    public function updateTemplate(Entity\Template $template) {
        $data = array(
            'target'      => $template->get('target'),
            'usage'       => $template->get('usage'),
            'name'        => $template->get('name'),
            'logic'       => $template->get('logic'),
            'description' => $template->get('description', ''),
            'visible'     => $template->get('visible', false)
        );

        return parent::update($template->getId(), $data);
    }

    /**
     * Deletes search template by given ID.
     *
     * @param int $id ID of template
     *
     * @return int
     */
    public function deleteTemplate($id) {
        return parent::delete($id);
    }
}
