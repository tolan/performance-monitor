<?php

namespace PM\Main\Logic\Evaluate\Databases;

use PM\Main\Logic\Evaluate\AbstractComposer;

/**
 * This script defines class for database statement composing result of performer and extraction of extractor.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Composer extends AbstractComposer {

    /**
     * Composed result.
     *
     * @var array
     */
    private $_result = null;

    /**
     * This method make compose of performer result and extracted data from extractor.
     *
     * @param array $performerResult Result of performer
     *
     * @return array
     */
    public function compose($performerResult) {

        $this->_result = $performerResult;

        return $performerResult;
    }

    /**
     * Return composed result.
     *
     * @return array|null
     */
    public function getResult() {
        return $this->_result;
    }
}
