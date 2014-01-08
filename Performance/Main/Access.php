<?php

namespace PF\Main;

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
     * @var \PF\Main\Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instance
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /*
     * It checks access by allow and denied address in configuration.
     *
     * @return \PF\Main\Access
     *
     * @throws \PF\Main\Access\Exception Throws when ip address is unauthorized.
     */
    public function checkAccess() {
        $allowPrio  = $this->_provider->get('PF\Main\Access\AllowFrom')->checkAccess();
        $deniedPrio = $this->_provider->get('PF\Main\Access\DeniedFrom')->checkAccess();

        if ($allowPrio <= $deniedPrio) {
            throw new Access\Exception('Unauthorized access.');
        }

        return $this;
    }
}
