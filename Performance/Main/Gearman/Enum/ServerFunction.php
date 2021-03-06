<?php

namespace PM\Main\Gearman\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This class defines name of gearman worker on gearmand application.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class ServerFunction extends Enum {
    const GEARMAN_FUNCTION = 'Performance_Gearman';
}
