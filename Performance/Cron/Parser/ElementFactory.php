<?php

namespace PM\Cron\Parser;

use PM\Main\Provider;

/**
 * This script defines class for create parser element.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class ElementFactory {

    /**
     * Provider instance.
     *
     * @var Provider
     */
    private $_provider;

    /**
     * Construct method for inject dependencies.
     *
     * @param Provider $provider Provider instance
     *
     * @return void
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * Returns new element instance.
     *
     * @return Element
     */
    public function createElement() {
        $className = __NAMESPACE__.'\Element';
        $class     = $this->_provider->prototype($className);
        /* @var $class Element */

        return $class;
    }
}
