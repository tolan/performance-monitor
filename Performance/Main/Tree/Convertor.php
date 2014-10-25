<?php

namespace PM\Main\Tree;

use PM\Main\Abstracts;
use PM\Main\CommonEntity;

/**
 * This script defines class for convertor of tree structure.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Convertor {

    /**
     * Container instance.
     *
     * @var Container
     */
    private $_container;

    /**
     * Construct method.
     *
     * @return void
     */
    public function __construct() {
        $this->_container = new Container();
    }

    /**
     * This method provides converting list of entities into tree structure by configuration.
     *
     * @param array  $list   List of entities (When entity is array then it is converted into common entity)
     * @param Config $config Tree configuration instance
     *
     * @return array
     */
    public function convert($list, Config $config) {
        $this->_prepare($config);

        foreach ($list as $item) {
            if ($item instanceof Abstracts\Entity === false) {
                $item = new CommonEntity($item);
            }

            $entity = new Entity($item, $config);
            $this->_container->assingTo($entity, $entity->getParentId());
        }

        $tree   = $this->_container->getTree();
        $result = $this->_formatTree($tree, $config);

        return $result;
    }

    /**
     * Returns container instance.
     *
     * @return Container
     */
    public function getContainer() {
        return $this->_container;
    }

    /**
     * It takes converted tree structure and convert it into structure by configuration.
     *
     * @param array  $tree   Tree structure with tree entities.
     * @param Config $config Tree configuration instance
     *
     * @return array
     */
    private function _formatTree($tree, Config $config) {
        $result = array();
        ksort($tree);

        foreach ($tree as $entity) {
            $entity   = $entity->toArray();
            $children = $entity['children'];
            $item     = $entity['entity'];

            if (!empty($children)) {
                $children = $this->_formatTree($children, $config);
                $item->set($config->getChildrenIdentificator(), $children);
            }

            $result[] = $item;
        }

        return $result;
    }

    /**
     * It prepares convertor to right work.
     *
     * @param Config $config Tree configuration instance
     *
     * @return Convertor
     */
    private function _prepare(Config $config) {
        $config->validate();
        $this->_container->clean();

        return $this;
    }
}
