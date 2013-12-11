<?php

/**
 * This script defines class for access control by allowed ip address.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Access_AllowFrom extends Performance_Main_Access_Abstract {
    const CONFIG_KEY = 'allowFrom';

    /**
     * It checks ip address and return checked priority. Higher priority means exact match and lower means match in big range.
     *
     * @return int
     *
     * @throws Performance_Main_Access_Exception Throws when are set allow address and remote ip address is not in allowed.
     */
    public function checkAccess() {
        $config      = $this->getConfig();
        $ipAddresses = $config[self::CONFIG_KEY];

        if (empty($ipAddresses)) {
            return 32;
        }

        $remoteIp = $this->getRemoteIp();
        $priority = 0;

        foreach ($ipAddresses as $pattern) {
            $priority = max($this->matchIpAddress($remoteIp, $pattern), $priority);
        }

        if ($priority === 0) {
            throw new Performance_Main_Access_Exception('Access denied by allow address.');
        }

        return $priority;
    }
}
