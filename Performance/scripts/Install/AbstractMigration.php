<?php

namespace PF\scripts\Install;

use PF\Main\Provider;

/**
 * Abstract class for migrations.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    scripts
 */
abstract class AbstractMigration {

    /**
     * Provider instance.
     *
     * @var Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instance
     *
     * @return void
     */
    final public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * Returns provider instance.
     *
     * @return Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }

    /**
     * Returns database instance.
     *
     * @return \PF\Main\Database
     */
    final protected function getDatabase() {
        return $this->_provider->get('PF\Main\Database');
    }

    /**
     * Returns utils instance.
     *
     * @return \PF\Main\Utils
     */
    final protected function getUtils() {
        return $this->_provider->get('PF\Main\Utils');
    }

    /**
     * Returns configuration for module.
     *
     * @param string $module Name of config module.
     *
     * @return mixed
     */
    final protected function getConfig($module) {
        return $this->_provider->get('PF\Main\Config')->get($module, array());
    }

    /**
     * Abstract method for start migration.
     */
    abstract public function run();
}
