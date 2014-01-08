<?php

namespace PF\Main\Access;

use PF\Main\Access\Exception;

/**
 * This script defines class for access control by allowed ip address.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class AllowFrom extends AbstractAccess {
    const CONFIG_KEY = 'allowFrom';

    /**
     * It checks ip address and return checked priority. Higher priority means exact match and lower means match in big range.
     *
     * @return int
     *
     * @throws \PF\Main\Access\Exception Throws when are set allow address and remote ip address is not in allowed.
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
            throw new Exception('Access denied by allow address.');
        }

        return $priority;
    }
}
