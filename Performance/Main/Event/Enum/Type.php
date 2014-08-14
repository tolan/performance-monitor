<?php

namespace PF\Main\Event\Enum;

use PF\Main\Abstracts\Enum;

/**
 * This class defines types of events.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Type extends Enum {
    const EVENT   = 'event';
    const MESSAGE = 'message';
}
