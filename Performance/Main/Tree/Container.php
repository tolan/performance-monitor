<?php

namespace PF\Main\Tree;

/**
 * This script defines class for container for elements of tree structure.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Container {

    /**
     * Main tree entity.
     *
     * @var Entity
     */
    private $_tree;

    /**
     * Map of entities for store relation between ids and their parent.
     *
     * @var array
     */
    private $_idMap = array();

    /**
     * Map inverse to idMap.
     *
     * @var array
     */
    private $_parentMap = array();

    /**
     * Construct method.
     *
     * @return void
     */
    public function __construct() {
        $this->clean();
    }

    /**
     * It assigns entity to another entity by parent id.
     *
     * @param Entity     $entity   Entity tree instance
     * @param int|string $parentId Id of parent entity
     *
     * @return Container
     *
     * @throws Exception Throws when entity is same as parent.
     */
    public function assingTo(Entity $entity, $parentId = null) {
        if ($entity->getId() === $parentId) {
            throw new Exception('Fatal error: entity ID can not be same as parent ID.');
        }

        if (empty($parentId)) {
            $this->_tree->assignSubEntity($entity, $entity->getOrder());
        }

        if (array_key_exists($parentId, $this->_idMap)) {
            $this->_idMap[$parentId]->assignSubEntity($entity, $entity->getOrder());
        }

        if (array_key_exists($entity->getId(), $this->_parentMap)) {
            foreach ($this->_parentMap[$entity->getId()] as $child) {
                $entity->assignSubEntity($child, $child->getOrder());
            }
        }

        $this->_idMap[$entity->getId()]                   = $entity;
        $this->_parentMap[$parentId][$entity->getOrder()] = $entity;

        return $this;
    }

    /**
     * Returns tree structure.
     *
     * @return array
     */
    public function getTree() {
        $entityData = $this->_tree->toArray();

        return $entityData['children'];
    }

    /**
     * It cleans container.
     *
     * @return Container
     */
    public function clean() {
        $this->_tree      = new Entity(null, new Config());
        $this->_idMap     = array();
        $this->_parentMap = array();

        return $this;
    }
}
