<?php

namespace PM\Settings\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with types of worker control.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 */
class ControlType extends Enum {
    const MANUAL    = 'manual';
    const KEEP      = 'keep';
    const ON_DEMAND = 'on_demand';
}
