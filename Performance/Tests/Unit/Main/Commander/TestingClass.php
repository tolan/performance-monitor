<?php

namespace PM\Tests\Unit\Main\Commander;

/**
 * This script defines testing class for classes from Commander namespace. It is only for testing execution,
 * because it can not be simply stubed.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class TestingClass {

    /**
     * Testing method for execute tests.
     *
     * @param mixed $mul   multiplier
     * @param mixed $const some constant
     *
     * @return array
     */
    public function test($mul = 1, $const = 200) {
        return array('mul' => $const * $mul, 'const' => 100);
    }
}