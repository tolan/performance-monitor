<?php

namespace PM\Main\Tree;

use PM\Main\Abstracts;

/**
 * This script defines class for entity tree of structure.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Entity {

    /**
     * Instance of entity with required data.
     *
     * @var Abstracts\Entity
     */
    private $_entity;

    /**
     * Tree configuration instance.
     *
     * @var Config
     */
    private $_config;

    /**
     * List of sub-entities.
     *
     * @var array
     */
    private $_subEntities = array();

    /**
     * Construct method for set source entity and configuration instance.
     *
     * @param Abstracts\Entity $data   Source entity with required data
     * @param Config           $config Tree configuration instance
     *
     * @return viod
     */
    public function __construct(Abstracts\Entity $data = null, Config $config) {
        $this->_entity = $data;
        $this->_config = $config;
    }

    /**
     * Returns identificator of entity.
     *
     * @return int|string
     */
    public function getId() {
        return $this->_entity->get($this->_config->getIdentificator());
    }

    /**
     * Returns idetificator of parent entity
     *
     * @return int|string
     */
    public function getParentId() {
        return $this->_entity->get($this->_config->getParentIdentificator());
    }

    /**
     * Returns order of entity in structure.
     *
     * @return int
     */
    public function getOrder() {
        return $this->_entity->get($this->_config->getOrderIdentificator());
    }

    /**
     * It assigns entity into sub-entities.
     *
     * @param Entity $entity Tree entity instance
     * @param int    $order  Order entity in sub-entities
     *
     * @return Entity
     *
     * @throws Exception Throws when sub-entity with order already exists.
     */
    public function assignSubEntity(Entity $entity, $order) {
        if (array_key_exists($order, $this->_subEntities)) {
            throw new Exception('Entity with order '.$order.' already exists.');
        }

        $this->_subEntities[$order] = $entity;

        return $this;
    }

    /**
     * Returns entity data in array format.
     *
     * @return array
     */
    public function toArray() {
        return array(
            'entity'   => $this->_entity,
            'children' => $this->_subEntities
        );
    }

}
