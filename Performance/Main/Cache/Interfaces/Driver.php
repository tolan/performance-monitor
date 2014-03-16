<?php

namespace PF\Main\Cache\Interfaces;

interface Driver {
    const SESSION_NAME      = 'Cache';
    const DEFAULT_NAMESPACE = 'Performance';

    public function load($name=null);
    public function save($name, $value);
    public function has($name);
    public function clean($name=null);
    public function flush();
}
