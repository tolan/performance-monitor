<?php

/**
 * This script defines class for access control by denied ip address.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Access_DeniedFrom extends Performance_Main_Access_Abstract {
    const CONFIG_KEY = 'deniedFrom';

    /**
     * It checks ip address and return checked priority. Higher priority means exact match and lower means match in big range.
     *
     * @return int
     *
     * @throws Performance_Main_Access_Exception Throws when are set denied address and remote ip address is not in denied.
     */
    public function checkAccess() {
        $config      = $this->getConfig();
        $ipAddresses = $config[self::CONFIG_KEY];

        if (empty($ipAddresses)) {
            return 0;
        }

        $remoteIp = $this->getRemoteIp();
        $priority = 32;

        foreach ($ipAddresses as $pattern) {
            $priority = min($this->matchIpAddress($remoteIp, $pattern), $priority);
        }

        if ($priority === 32) {
            throw new Performance_Main_Access_Exception('Access denied by denied address.');
        }

        return $priority;
    }
}
