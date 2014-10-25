<?php

namespace PM\Main\Access;

use PM\Main\Access\Exception;

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
     * @throws Exception Throws when are set allow address and remote ip address is not in allowed.
     */
    public function checkAccess() {
        $config      = $this->getConfig();
        $ipAddresses = isset($config[self::CONFIG_KEY]) ? $config[self::CONFIG_KEY] : null;

        if (empty($ipAddresses)) {
            return 0;
        }

        $remoteIp = $this->getRemoteIp();

        $configTime     = $this->getConfigTime();
        $cachedPriority = $this->getFromCache($remoteIp, $configTime);

        if ($cachedPriority !== false) {
            return $cachedPriority;
        }

        $priority = 0;

        foreach ($ipAddresses as $pattern) {
            $priority = max($this->matchIpAddress($remoteIp, $pattern), $priority);
        }

        if ($priority === 0) {
            throw new Exception('Access denied by allow address.');
        }

        $this->saveToCache($remoteIp, $configTime, $priority);

        return $priority;
    }
}
