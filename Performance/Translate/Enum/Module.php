<?php

namespace PM\Translate\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This class defines modules in translate.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Translate
 */
class Module extends Enum {

    const MAIN      = 'main';
    const PROFILER  = 'profiler';
    const SEARCH    = 'search';
    const STATISTIC = 'statistic';
}
