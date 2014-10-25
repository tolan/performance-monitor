<?php

namespace PM\Main\Logic\Evaluate\Arrays;

use PM\Main\Logic\Evaluate\AbstractComposer;

/**
 * This script defines class for array composing result of performer and extraction of extractor.
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
        $result    = array();
        $extractor = $this->getEvaluator()->getPerformer()->getExtractor(); /* @var $extractor \PM\Main\Logic\Evaluate\Arrays\Extractor */

        foreach ($performerResult as $id => $names) {
            $tmp = array();
            foreach ($names as $name) {
                $map = $extractor->getMap($name);

                if ($name === 'all') {
                    $data = $this->getEvaluator()->getScope();
                } else {
                    $data = $this->getEvaluator()->getData($name);
                }

                $tmp += $data[$map[$id]];
            }

            $tmp['applied_filters'] = $names;

            $result[] = $tmp;
        }

        $this->_result = $result;

        return $result;
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
