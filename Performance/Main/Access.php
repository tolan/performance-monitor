<?php

/**
 * This script defines class for access control by ip address.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Access {

    /**
     * Provider instance
     *
     * @var Performance_Main_Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param Performance_Main_Provider $provider Provider instance
     */
    public function __construct(Performance_Main_Provider $provider) {
        $this->_provider = $provider;
    }

    /*
     * It checks access by allow and denied address in configuration.
     *
     * @return Performance_Main_Access
     *
     * @throws Performance_Main_Access_Exception Throws when ip address is unauthorized.
     */
    public function checkAccess() {
        $allowPrio  = $this->_provider->get('Performance_Main_Access_AllowFrom')->checkAccess();
        $deniedPrio = $this->_provider->get('Performance_Main_Access_DeniedFrom')->checkAccess();

        if ($allowPrio <= $deniedPrio) {
            throw new Performance_Main_Access_Exception('Unauthorized access.');
        }

        return $this;
    }
}
