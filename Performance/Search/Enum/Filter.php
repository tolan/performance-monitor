<?php

namespace PF\Search\Enum;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with all posible filter for all entities.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Filter extends Enum {
    const FULLTEXT      = 'fulltext';
    const NAME          = 'name';
    const EDITED        = 'edited';
    const METHOD        = 'method';
    const URL           = 'url';
    const STATE         = 'state';
    const STARTED       = 'started';
    const TIME          = 'time';
    const CALLS         = 'calls';
    const FILE          = 'file';
    const LINE          = 'line';
    const IMMERSION     = 'immersion';
    const CONTENT       = 'content';
}
