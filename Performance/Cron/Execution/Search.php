<?php

namespace PM\Cron\Execution;

use PM\Search\Engine;
use PM\Search\Entity;
use PM\Statistic\Enum\Source;

/**
 * This script defines class for create scope of entities which will be processed in execution.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Search {

    /**
     * Search engine instance.
     *
     * @var Provider
     */
    private $_engine;

    /**
     * Search template entity instance.
     *
     * @var Entity\Template
     */
    private $_template = null;

    /**
     * Set of items for manual or all type.
     *
     * @var array
     */
    private $_items = array();

    /**
     * Type of selected items.
     *
     * @var string
     */
    private $_type = Source\Type::MANUAL;

    /**
     * Set of items.
     *
     * @var array
     */
    private $_result = array();

    /**
     * Construct method for set dependencies.
     *
     * @param Engine $engine Search engine instance
     *
     * @return void
     */
    public function __construct(Engine $engine) {
        $this->_engine = $engine;
    }

    /**
     * Set search template for finding entities.
     *
     * @param Entity\Template $template Search template entity instance
     *
     * @return Search
     */
    public function setTemplate(Entity\Template $template) {
        $this->_template = $template;

        return $this;
    }

    /**
     * Set selected items.
     *
     * @param array $items Selected items
     *
     * @return Search
     */
    public function setItems(array $items=array()) {
        $this->_items = $items;

        return $this;
    }

    /**
     * Set type of selected data in search template.
     *
     * @param string $type Value from enum Source\Type
     *
     * @return Search
     */
    public function setType($type=Source\Type::MANUAL) {
        $this->_type = $type;

        return $this;
    }

    /**
     * Returns set of entities which corresponds to search template and type of selected items.
     *
     * @return array
     */
    public function getResult() {
        $this
            ->_validate()
            ->_execute();

        return $this->_result;
    }

    /**
     * It validates that the search can be executed.
     *
     * @return Search
     *
     * @throws Exception It throws when setting is not valid (template or items can't be null).
     */
    private function _validate() {
        if ($this->_template === null || empty($this->_items) || !in_array($this->_type, Source\Type::getConstants())) {
            throw Exception('Search of cron execution is not valid.');
        }

        return $this;
    }

    /**
     * It executes search by search template and set it to private property _result.
     *
     * @return Search
     */
    private function _execute() {
        $items = $this->_items;
        if ($this->_type === Source\Type::TEMPLATE) {
            $searchSelects = $this->_engine->find($this->_template->toArray());
            $items         = $searchSelects['result'];
        }

        $this->_result = $items;

        return $this;
    }
}
