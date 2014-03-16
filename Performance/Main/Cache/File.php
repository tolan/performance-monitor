<?php

namespace PF\Main\Cache;

class File extends AbstractCache implements Interfaces\Driver {
    private $_fileName;

    public function __construct(\PF\Main\Config $config, $namespace = self::DEFAULT_NAMESPACE) {
        $tmpDir = is_dir($config->get('tmpDir')) ? $config->get('tmpDir') : $config->get('root').'/tmp';

        $this->_fileName = $tmpDir.'/'.$namespace.'.CACHE.php';

        if (file_exists($this->_fileName)) {
            $this->_data = unserialize(file_get_contents($this->_fileName));
        }
    }

    public function flush() {
        if (file_exists($this->_fileName)) {
            unlink($this->_fileName);
        }

        touch($this->_fileName);
        file_put_contents($this->_fileName, serialize($this->_data));
    }
}
