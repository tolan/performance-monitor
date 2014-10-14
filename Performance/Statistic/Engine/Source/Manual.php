<?php

namespace PF\Statistic\Engine\Source;

use PF\Statistic\Entity;

/**
 * This script defines class for creation source select for manual selected items.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Manual extends AbstractSource {

    /**
     * Returns source select with set of manual selected items.
     *
     * @param Entity\Template $template Statistic template entity
     *
     * @return \PF\Main\Database\Select
     */
    public function getSelect(Entity\Template $template) {
        $database = $this->getProvider()->get('database'); /* @var $database \PF\Main\Database */
        $select   = $database->select();

        $target = $this->getProvider()->singleton('PF\Statistic\Engine\Target\\'.ucfirst($template->getSource()['target']));
        $target->setTarget($select);

        $select->where('id IN (?)', join(', ', $template->getSource()['items']));
        $select->columns('id');

        return $select;
    }
}
