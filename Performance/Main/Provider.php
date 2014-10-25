<?php

namespace PM\Main;

/**
 * Class definition for performance provider. This class is responsible for creating classes and store their instances to the store.
 * Creating classes resolves dependencies between classes and provides a protection from loops.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Provider {

    /**
     * Storage for singleton instances.
     *
     * @var array
     */
    private $_instances = array();

    /**
     * Information about dependencies between classes.
     *
     * @var array
     */
    private $_dependencies = array();

    /**
     * Flag for using spl autoloader function
     *
     * @var boolean
     */
    private $_useAutoloader = true;

    /**
     * Flag for allow kill app which have memory leak.
     *
     * @var boolean
     */
    private $_allowKillApp = false;

    /**
     * Self singleton instance
     *
     * @var Provider
     */
    private static $_selfInstance = null;

    /**
     * Array for prevention cycling dependencies.
     *
     * @var array
     */
    private static $_preventCycleDependencies = array();

    /**
     * Maps for resolve short name for class name.
     *
     * @var array
     */
    private $_serviceMap = array();

    /**
     * List of file which are excluded from manual loading files.
     *
     * @var array
     */
    private $_excludeFilesFromLoad = array(
        'Main/Abstracts/Unit/TestCase.php',
        'scripts/install.php',
        'scripts/memoryLeakCleaner.php',
        'scripts/startWorker.php'
    );

    /**
     * Cache instance.
     *
     * @var Cache
     */
    private $_cache = null;

    /**
     * Construct method which initialize instance and prepare autoloder.
     *
     * @var $config Config instance
     *
     * @return void
     */
    public function __construct(Config $config = null) {
        $services       = array();
        $providerConfig = null;

        if ($config !== null) {
            $this->set($config);
            $providerConfig       = $config->hasOwnProperty('provider') ? $config->get('provider') : array();
            $this->_useAutoloader = isset($providerConfig['useAutoloader']) ? $providerConfig['useAutoloader'] : $this->_useAutoloader;
            $this->_allowKillApp  = isset($providerConfig['allowKillApp']) ? $providerConfig['allowKillApp'] : $this->_allowKillApp;
            $services             = isset($providerConfig['initServices']) ? $providerConfig['initServices'] : array();
            $this->_serviceMap    = isset($providerConfig['serviceMap']) ? $providerConfig['serviceMap'] : array();
        }

        if ($this->_useAutoloader === true && is_callable('spl_autoload_register')) {
            spl_autoload_register(array($this, '_autoloader'));
        } else {
            $this->_loadClass();

            if ($config) {
                foreach ($config->get('modules', array()) as $module) {
                    $this->_loadClass('PM\\'.$module.'\\');
                }
            }
        }

        $this->set($this);
        $this->_initCache($config);

        foreach ($services as $name => $service) {
            $instance = $this->get($service);
            $this->set($instance, $name);
        }
    }

    /**
     * Return singleton instance.
     *
     * @var $config Config instance
     *
     * @return \PM\Main\Provider
     */
    public static function getInstance(Config $config = null) {
        if (self::$_selfInstance === null) {
            self::$_selfInstance = new self($config);
        }

        return self::$_selfInstance;
    }

    /**
     * Sets instance to stack for singleton usage.
     *
     * @param object $instance Some instance
     * @param string $name     Optional name for instance
     *
     * @return \PM\Main\Provider
     *
     * @throws \PM\Main\Exception Throws when instance is not object
     */
    public function set($instance, $name=null) {
        if (is_object($instance) === false) {
            throw new Exception('First parameter must be instance.');
        }

        if ($name === null || is_string($name) === false) {
            $name = get_class($instance);
        }

        $this->reset($name);
        $this->reset(get_class($instance));

        $this->_instances[] = array(
            'name'      => ltrim($name, '\\'),
            'classname' => ltrim(get_class($instance), '\\'),
            'instance'  => $instance
        );

        return $this;
    }

    /**
     * Unsets instance from stack for singleton usage.
     *
     * @param string $name Name of class
     *
     * @return \PM\Main\Provider
     */
    public function reset($name=null) {
        if ($name === null) {
            $this->_instances = array();
        } else {
            foreach ($this->_instances as $key => $instance) {
                if ($instance['name'] === $name) {
                    unset($this->_instances[$key]);
                    break;
                }

                if ($instance['classname'] === $name) {
                    unset($this->_instances[$key]);
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Destruct function destroys all instances and check memory leak.
     *
     * @return void
     */
    public function __destruct() {
        if ($this->_cache) {
            $this->_cache->save(__CLASS__, $this->_dependencies);
        }

        $list    = $this->getListInstances();
        $root    = $this->get('config')->get('root');
        $memory  = $this->get('PM\Main\System\Memory'); /* @var $memory \PM\Main\System\Memory */
        $process = $this->get('PM\Main\System\Process'); /* @var $process \PM\Main\System\Process */

        foreach ($list as $instance) {
            $this->reset($instance['name']);
        }

        if ($this->_allowKillApp && $memory->getRelativeUsage() > 0.2) {
            $pid    = $process->getPid();
            $script = 'php '.$root.'/scripts/memoryLeakCleaner.php '.$pid;
            $this->get('PM\Main\Log')->warning('Application was killed with PID: '.$pid);
            $process->exec($script);
        }
    }

    /**
     * Gets instance from stack. It find instance by name and then by classname.
     * If instance is not in stack then it create instance with dependencies and save it to stack (lazy load principle).
     *
     * @param string $name Name of class
     *
     * @return object ${name} Instance of $name
     */
    public function get($name) {
        $name = ltrim($name, '\\');
        if (isset($this->_serviceMap[$name])) {
            $name = $this->_serviceMap[$name];
        }

        $instance = $this->_getInstance($name);

        self::$_preventCycleDependencies = array();

        return $instance;
    }

    /**
     * Returns that provider has created requested instance.
     *
     * @param string $name Name of class
     *
     * @return boolean
     */
    public function has($name) {
        $has  = false;
        $name = ltrim($name, '\\');
        if (isset($this->_serviceMap[$name])) {
            $name = $this->_serviceMap[$name];
        }

        foreach ($this->_instances as $instance) {
            if ($name === $instance['name']) {
                $has = true;
                break;
            }

            if ($name === $instance['classname']) {
                $has = true;
                break;
            }
        }

        return $has;
    }

    /**
     * Returns list of instances in array where are names and classnames.
     *
     * @return array
     */
    public function getListInstances() {
        $result = array();
        foreach ($this->_instances as $instance) {
            $result[] = array(
                'name'      => $instance['name'],
                'classname' => $instance['classname']
            );
        }

        return $result;
    }

    /**
     * Return instance from stack where are singleton instances.
     *
     * @param string $name Name of class
     *
     * @return object {$name}
     */
    public function singleton($name) {
        return $this->get($name);
    }

    /**
     * Return new instance.
     *
     * @param string  $name Name of class
     * @param boolean $deep Flag for create new dependencies
     *
     * @return object {$name}
     */
    public function prototype($name, $deep=false) {
        if ($deep === false) {
            $singleton = null;
            if ($this->has($name)) {
                $singleton = $this->get($name);
            }

            $this->reset($name);
            $prototype = $this->get($name);
            $this->reset($name);

            if ($singleton) {
                $this->set($singleton, $name);
            }
        } else {
            $instances = $this->_instances;
            $this->reset();
            $prototype = $this->get($name);
            $this->_instances = $instances;
        }

        return $prototype;
    }

    /**
     * Load class of enum for using constants
     *
     * @param strin $name Class name of constants
     *
     * @return \PM\Main\Provider
     */
    public function loadEnum($name) {
        $this->_autoloader($name);

        return $this;
    }

    /**
     * Gets instance from stack. It find instance by name and then by classname.
     *
     * @param string $name Name of class
     *
     * @return object {$name}
     */
    private function _getInstance($name) {
        $intance = null;
        foreach ($this->_instances as $item) {
            if ($name === $item['name']) {
                $intance = $item['instance'];
                break;
            }

            if ($name === $item['classname']) {
                $intance = $item['instance'];
                break;
            }
        }

        if (!$intance) {
            $intance = $this->_createInstance($name);
            $this->set($intance, $name);
        }

        return $intance;
    }

    /**
     * It creates instance with dependencies.
     *
     * @param string $name Name of class
     *
     * @return object {$name}
     *
     * @throws \PM\Main\Exception Throws when dependecies are cycling or instance was not created.
     */
    private function _createInstance($name) {
        $this->_loadClass($name);
        if (in_array($name, self::$_preventCycleDependencies)) {
            throw new Exception('Object has cycling dependencies: ['.join(', ', self::$_preventCycleDependencies).']!');
        }

        self::$_preventCycleDependencies[] = $name;

        $dependencies = $this->_getDependencies($name);
        $instances    = array();
        $arguments    = array();

        if (isset($dependencies['dependencies'])) {
            foreach ($dependencies['dependencies'] as $key => $dependency) {
                if (isset($dependency['className'])) {
                    $instances[$key] = $this->_getInstance($dependency['className']);
                }
            }

            foreach ($dependencies['dependencies'] as $key => $dependency) {
                if (isset($dependency['className'])) {
                    $arguments[$dependency['name']] = $instances[$key];
                } elseif (key_exists('defaultValue', $dependency)) {
                    $arguments[$dependency['name']] = $dependency['defaultValue'];
                }
            }
        }

        $instance = null;

        if (array_key_exists('dependencies', $dependencies) === false || empty($dependencies['dependencies'])) {
            if (isset($dependencies['method']) && $dependencies['method'] === 'getInstance') {
                $instance = forward_static_call_array(array($name, 'getInstance'), array());
            } else {
                $instance = new $name();
            }
        } else {
            if ($dependencies['method'] === 'getInstance') {
                $instance = forward_static_call_array(array($name, 'getInstance'), $arguments);
            } else {
                $class    = new \ReflectionClass($name);
                $instance = $class->newInstanceArgs($arguments);
            }
        }

        if (is_object($instance) === false) {
            throw new Exception('Instance '.$name.' was not created!');
        }

        return $instance;
    }

    /**
     * Gets dependencies for create new class. It finds dependecies for standard constructor or singleton method
     * with name getInstance.
     *
     * @param string $class Name of class
     *
     * @return array Array with all dependencies.
     *
     * @throws \PM\Main\Exception Throws when class doesn't exists.
     */
    private function _getDependencies($class) {
        if (!isset($this->_dependencies[$class])) {
            if (class_exists($class)) {
                $reflClass = new \ReflectionClass($class);
            } else {
                throw new Exception('Object doesn\'t exists: '.$class);
            }

            $methods      = $reflClass->getMethods();
            $searched     = false;
            $dependencies = array();

            foreach ($methods as $method) {
                if ($method->getName() === 'getInstance' && $method->isPublic()) {
                    $searched = true;
                    break;
                }
            }

            if ($searched === false) {
                foreach ($methods as $method) {
                    if ($method->isConstructor() && $method->isPublic()) {
                        $searched = true;
                        break;
                    }
                }
            }

            if ($searched === false) {
                $this->_dependencies[$class] = array();
                return $this->_dependencies[$class];
            }

            $params = $method->getParameters();
            foreach ($params as $param) {
                if ($param->getClass() !== null) {
                    $dependencies[$param->getPosition()] = array(
                        'className' => $param->getClass()->getName(),
                        'position'  => $param->getPosition(),
                        'name'      => $param->getName()
                    );
                } else {
                    $dependencies[$param->getPosition()] = array(
                        'defaultValue' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                        'position'     => $param->getPosition(),
                        'name'         => $param->getName()
                    );
                }
            }

            $path = $this->_getFileNameForClass($class);

            $this->_dependencies[$class] = array(
                'method'       => $method->getName(),
                'dependencies' => $dependencies,
                'time'         => $path ? filemtime($path) : false
            );
        }

        return $this->_dependencies[$class];
    }

    /**
     * Autoloader function
     *
     * @param string $class Name of class
     *
     * @return void
     */
    private function _autoloader($class) {
        $path   = $this->_getFileNameForClass($class);
        $loaded = false;

        if ($path) {
            include_once $path;
            $loaded = true;
        }

        return $loaded;
    }

    /**
     * Construct path to file with class.
     *
     * @param string $class Name of class
     *
     * @return string|false
     */
    private function _getFileNameForClass($class) {
        $root            = dirname(__DIR__);
        $translatedClass = strtr($class, array('_' => '/', '\\' => '/'));
        $classPath       = strstr($translatedClass, '/');

        $path = $root.$classPath.'.php';
        if (file_exists($path) === false) {
            $path = false;
        }

        return $path;
    }

    /**
     * Loads file for class. It loads whole module.
     *
     * @param string $class Name of class
     *
     * @return \PM\Main\Provider
     *
     * @throws \PM\Main\Exception Throws when you try load class from non Performance pool
     */
    private function _loadClass($class = null) {
        $class = $class === null ? get_class() : $class;
        if (class_exists($class) === false && $this->_useAutoloader === false) {
            if (preg_match('/^Performance.*|^PM.*/', $class)) {
                $tmp    = substr($class, strpos($class, '\\') + 1);
                $module = substr($tmp, 0, strpos($tmp, '\\'));
                $path   = dirname(__DIR__).'/'.$module.'/';
                $files  = $this->_getFiles($path);
                spl_autoload_register(array($this, '_autoloader'));
                foreach ($files as $file) {
                    include_once $file;
                }

                spl_autoload_unregister(array($this, '_autoloader'));
            } else {
                throw new Exception('Provider loading only classes from Performance (PM namespace).');
            }
        }

        return $this;
    }

    /**
     * Helper function which return all files from path include subfolders.
     *
     * @param string $path Path
     *
     * @return array Array with paths all files
     */
    private function _getFiles($path = '') {
        $result = array();
        $path   = rtrim($path, '/');
        $root   = $this->get('config')->get('root');

        foreach ((array)glob($path.'/*', GLOB_ONLYDIR) as $item) {
            if (strlen($item) > strlen($path)) {
                $result = array_merge($result, $this->_getFiles($item));
            }
        }

        foreach ((array)glob($path.'/*.php') as $item) {
            $shortPath = substr($item, strlen($root) + 1);
            if (!in_array($shortPath, $this->_excludeFilesFromLoad)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Initialize cache by config.
     *
     * @param Config $config Config instance or null
     *
     * @return Provider
     */
    private function _initCache($config) {
        $providerConfig = $config->get('provider', array());

        if ($config !== null && isset($providerConfig['cache']) && $providerConfig['cache']) {
            $this->_cache        = $this->get($providerConfig['cache']);
            if ($this->_cache->has(__CLASS__)) {
                $cached = $this->_cache->load(__CLASS__);
                foreach ($cached as $class => $dependencies) {
                    $path = $this->_getFileNameForClass($class);

                    if ($path && !empty($dependencies) && filemtime($path) === $dependencies['time']) {
                        $this->_dependencies[$class] = $dependencies;
                    }
                }
            }
        }

        return $this;
    }
}
