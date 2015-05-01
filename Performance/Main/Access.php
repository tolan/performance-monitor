<?php

namespace PM\Main;

/**
 * This script defines class for access control by ip address.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Access {

    /**
     * Provider instance
     *
     * @var Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param Provider $provider Provider instance
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /*
     * It checks access by allow and denied address in configuration.
     *
     * @return Access
     *
     * @throws Access\Exception Throws when ip address is unauthorized.
     */
    public function checkAccess() {
        $allowFrom  = $this->_provider->get('PM\Main\Access\AllowFrom'); /* @var $allowFrom \PM\Main\Access\AllowFrom */
        $deniedFrom = $this->_provider->get('PM\Main\Access\DeniedFrom'); /* @var $deniedFrom \PM\Main\Access\DeniedFrom */
        $allowPrio  = $allowFrom->checkAccess();
        $deniedPrio = $deniedFrom->checkAccess();

        if ($allowPrio <= $deniedPrio && $deniedPrio > 0) {
            throw new Access\Exception('Unauthorized access.');
        }

        return $this;
    }
}
