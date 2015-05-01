<?php

namespace PM\Cron;

use PM\Cron\Enum\Target;
use PM\Cron\Enum\Action;

/**
 * This script defines class for define associations between target entity and actions.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Association {

    /**
     * Associations between target entity and actions.
     *
     * @var array
     */
    private static $_menusActions = array(
        Target::SCENARIO => array(
            Action::RUN
        ),
        Target::STATISTIC_SET => array(
            Action::RUN
        ),
        Target::STATISTIC_RUN => array(
            Action::DELETE
        )
    );

    /**
     * Returns configurations for menus of target entities and actions.
     *
     * @return array
     */
    public function getMenusForActions() {
        $assoc  = self::$_menusActions;
        $result = array();

        foreach ($assoc as $target => $actions) {
            $result[$target] = array();

            foreach ($actions as $action) {
                $result[$target][] = array(
                    'text'   => 'settings.cron.trigger.action.task.action.'.$action,
                    'target' => $target,
                    'action' => $action
                );
            }
        }

        return $result;
    }
}
