<?php

namespace PF\Main\Logic\Evaluate\Arrays;

use PF\Main\Logic\Evaluate\AbstractPerformer;

/**
 * This script defines class for array performer for evaluate expression and data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performer extends AbstractPerformer {

    /**
     * Returns relevant extractor.
     *
     * @return \PF\Main\Logic\Evaluate\Arrays\Extractor
     */
    protected function createExtractor() {
        return new Extractor();
    }

    /**
     * Returns relevant composer.
     *
     * @return \PF\Main\Logic\Evaluate\Arrays\Composer
     */
    protected function createComposer() {
        return new Composer();
    }

    /**
     * This method evaluate operator AND.
     *
     * @param array $first  First array
     * @param array $second Second array
     *
     * @return array
     */
    protected function perform_and($first, $second) {
        $result = array();

        foreach($first as $key => $item) {
            if (isset($second[$key])) {
                $result[$key] = $item + $second[$key];
            }
        }

        return $result;
    }

    /**
     * This method evaluate operator OR.
     *
     * @param array $first  First array
     * @param array $second Second array
     *
     * @return array
     */
    protected function perform_or($first, $second) {
        $result = $first;

        foreach ($second as $key => $item) {
            if (isset($result[$key])) {
                $result[$key] = $result[$key] + $item;
            } else {
                $result[$key] = $item;
            }
        }

        return $result;
    }

    /**
     * This method evaluate operator NAND.
     *
     * @param array $first  First array
     * @param array $second Second array
     *
     * @return array
     */
    protected function perform_nand($first, $second) {
        $and   = $this->perform_and($first, $second);
        $scope = $this->getExtractor()->getScope();

        return array_diff_key($scope, $and);
    }

    /**
     * This method evaluate operator NOR.
     *
     * @param array $first  First array
     * @param array $second Second array
     *
     * @return array
     */
    protected function perform_nor($first, $second) {
        $or    = $this->perform_or($first, $second);
        $scope = $this->getExtractor()->getScope();

        return array_diff_key($scope, $or);
    }

    /**
     * This method evaluate operator XOR.
     *
     * @param array $first  First array
     * @param array $second Second array
     *
     * @return array
     */
    protected function perform_xor($first, $second) {
        $result = $first;

        foreach ($second as $key => $item) {
            if (isset($result[$key])) {
                unset($result[$key]);
            } else {
                $result[$key] = $item;
            }
        }

        return $result;
    }

    /**
     * This method evaluate operator XNOR.
     *
     * @param array $first  First array
     * @param array $second Second array
     *
     * @return array
     */
    protected function perform_xnor($first, $second) {
        $result = $this->getExtractor()->getScope();

        foreach ($result as $key => $item) {
            if (isset($first[$key]) && isset($second[$key])) {
                $result[$key] = $item + $first[$key] + $second[$key];
            } elseif (isset($first[$key]) || isset($second[$key])) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
