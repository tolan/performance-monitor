<?php

namespace PM\Settings\Gearman\Control;

use PM\Main\Provider;
use PM\Main\Utils;
use PM\Settings\Enum\ControlType;
use PM\Settings\Gearman\Exception;

/**
 * This script defines factory class for create instance of AbstractControl.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 */
class Factory {

    /**
     * Provider instance.
     *
     * @var Provider
     */
    private $_provider;

    /**
     * Utils instance.
     *
     * @var Utils
     */
    private $_utils;

    /**
     * Construct method.
     *
     * @param Provider $provider Provider instance
     * @param Utils    $utils    Utils instance
     *
     * @return void
     */
    public function __construct(Provider $provider, Utils $utils) {
        $this->_provider = $provider;
        $this->_utils    = $utils;
    }

    /**
     * Returns instance of control by type.
     *
     * @param string $type Control type
     *
     * @return AbstractControl
     */
    public function getControl($type) {
        if (!in_array($type, ControlType::getConstants())) {
            throw new Exception('Undefined control type: '.$type);
        }

        $class = __NAMESPACE__.'\\'.ucfirst($this->_utils->toCamelCase($type));

        return $this->_provider->prototype($class);
    }
}
