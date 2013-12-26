<?php

/**
 * This script defines class for application module loader. It provides auto start all services.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_ModuleLoader {

    /**
     * Config instance
     *
     * @var Performance_Main_Config
     */
    private $_config;

    /**
     * Provider instance
     *
     * @var Performance_Main_Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param Performance_Main_Config   $config   Config instance
     * @param Performance_Main_Provider $provider Provider instance
     */
    public function __construct(Performance_Main_Config $config, Performance_Main_Provider $provider) {
        $modules = $config->get('modules');
        $this->_config   = $config;
        $this->_provider = $provider;

        foreach ($modules as $module) {
            $this->load($module);
        }
    }

    /**
     * This method provides loading of services in module config file.
     *
     * @param string $module Name of module
     */
    public function load($module) {
        $root = $this->_config->get('root');

        $this->_config->loadJson($root.'/'.$module.'/config.json');

        if ($this->_config->hasOwnProperty('services')) {
            foreach ($this->_config->get('services') as $service) {
                $this->_provider->get($service);
            }
        }
    }
}
