<?php

namespace PM\Statistic\Engine;

use PM\Main\Database;

/**
 * This script defines class for building statistic select with special methods.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Select extends Database\Select {

    /**
     * Returns that actual select has junction to entity (over from or join).
     *
     * @param string $entity One of enum \PM\Statistic\Enum\Source\Target
     *
     * @return boolean
     */
    public function hasJunction($entity) {
        $joins = $this->getPart(self::PART_JOINS);
        $from  = $this->getPart(self::PART_FROM);

        $joins[$from['alias']] = $from['table'];

        return array_key_exists($entity, $joins) || array_key_exists($this->getTableName($entity), $joins);
    }
}
