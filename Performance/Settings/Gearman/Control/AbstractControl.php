<?php

namespace PM\Settings\Gearman\Control;

use PM\Settings\Gearman\Operation;

/**
 * This script defines abstract class for each control operation mode.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 */
abstract class AbstractControl {

    /**
     * Operation instance.
     *
     * @var Operation
     */
    private $_operation = null;

    /**
     * Construct method.
     *
     * @param Operation $operation Operation instance
     *
     * @return void
     */
    public function __construct(Operation $operation) {
        $this->_operation = $operation;
    }

    /**
     * Abstract method fox executing operation.
     *
     * @param array $status Actual gearman worker status
     * @param array $worker Worker options
     *
     * @return AbstractControl
     */
    abstract public function control($status, $worker);

    /**
     * Returns instance of Operation.
     *
     * @return Operation
     */
    protected function getOperation() {
        return $this->_operation;
    }
}
