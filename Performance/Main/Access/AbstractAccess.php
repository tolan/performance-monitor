<?php

namespace PF\Main\Access;

use PF\Main\Config;
use PF\Main\Web\Component\Request;

/**
 * Abstract class for access control by ip address.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractAccess {

    /**
     * Request instance
     *
     * @var \PF\Main\Web\Component\Request
     */
    private $_request;

    /**
     * Config instance
     *
     * @var \PF\Main\Config
     */
    private $_config;

    /**
     * Construct method.
     *
     * @param \PF\Main\Web\Component\Request $request Request instance
     * @param \PF\Main\Config                $config  Config instance
     */
    final public function __construct(Request $request, Config $config) {
        $this->_request = $request;
        $this->_config  = $config;
    }

    /**
     * It checks access by remote ip address.
     *
     * @return int Priority of checked address
     */
    abstract public function checkAccess();

    /**
     * Returns config instance for access module.
     *
     * @return array
     */
    final protected function getConfig() {
        return $this->_config->get('access');
    }

    /**
     * Returns server variable instance.
     *
     * @return \PF\Main\Web\Component\Http\Server
     */
    final protected function getServer() {
        return $this->_request->getServer();
    }

    /**
     * Returns remote ip address from server variable.
     *
     * @return string
     */
    final protected function getRemoteIp() {
        $server = $this->_request->getServer();

        return $server->getREMOTE_ADDR();
    }

    /**
     * Match ip address by pattern and returns checked priority.
     *
     * @param string $ip      Matched ip address
     * @param string $pattern Pattern for matching
     *
     * @return int
     */
    protected function matchIpAddress($ip, $pattern) {
        $this->_validateIp($ip);
        list($start, $end, $lengthMask) = $this->_resolvePattern($pattern);

        $priority = 0;

        if (ip2long($ip) >= ip2long($start) && ip2long($ip) <= ip2long($end)) {
            $priority = $lengthMask;
        }

        return $priority;
    }

    /**
     * Resolve pattern and return parameters:
     *  - Start ip address
     *  - End ip address
     *  - Length of mask
     *
     * @param string $pattern Matching pattern
     *
     * @return array
     */
    private function _resolvePattern($pattern) {
        $this->_validatePattern($pattern);
        list($ip, $mask) = $this->_preparePattern($pattern);
        $lengthMask      = $this->_convertMaskToLength($mask);
        $bin             = str_pad(decbin(ip2long($ip)), 32, '0', STR_PAD_LEFT);

        $prefix = substr($bin, 0, $lengthMask);

        $start = long2ip(bindec(str_pad($prefix, 32, '0', STR_PAD_RIGHT)));
        $end   = long2ip(bindec(str_pad($prefix, 32, '1', STR_PAD_RIGHT)));

        return array($start, $end, $lengthMask);
    }

    /**
     * It provides validation for matching pattern.
     *
     * @param string $pattern Matching pattern
     *
     * @return \PF\Main\Access\AbstractAccess
     */
    private function _validatePattern($pattern) {
        list($ip, $mask) = $this->_preparePattern($pattern);
        $this->_validateIp($ip);
        $this->_convertMaskToLength($mask);

        return $this;
    }

    /**
     * It prepares pattern and return ip address and mask.
     *
     * @param string $pattern Matching pattern
     *
     * @return array
     *
     * @throws \PF\Main\Access\Exception Throws when patter has too much slashes
     */
    private function _preparePattern($pattern) {
        if (substr_count($pattern, '/') >= 2) {
            throw new Exception('Pattern has too much slashes.');
        }

        $pattern = strpos($pattern, '/') ? $pattern : $pattern.'/32';
        return explode('/', $pattern);
    }

    /**
     * It validate ip address.
     *
     * @param string $ip Ip address
     *
     * @return string Ip address
     *
     * @throws \PF\Main\Access\Exception Throws when ip address is not valid
     */
    private function _validateIp($ip) {
        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        if ($ip === false) {
            throw new Exception('Pattern has invalid ip address.');
        }

        return $ip;
    }

    /**
     * It converts mask to mask length.
     *
     * @param string $mask CIDR mask of IP address
     *
     * @return int
     *
     * @throws \PF\Main\Access\Exception Throws when mask is invalid
     */
    private function _convertMaskToLength($mask) {
        if (filter_var($mask, FILTER_VALIDATE_IP)) {
            $bin = decbin(ip2long($mask));
            if (preg_match('#^1{1,32}0{0,31}$#', $bin)) {
                return substr_count($bin, '1');
            }
        } elseif ($mask > 0 && $mask <= 32) {
            return (int)$mask;
        }

        throw new Exception('Pattern has invalid mask.');
    }
}
