<?php

/**
 * Class definition for performance provider. This class is responsible for creating classes and store their instances to the store.
 * Creating classes resolves dependencies between classes and provides a protection from loops.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
final class Performance_Main_Provider {

    /**
     * Storage for singleton instances.
     *
     * @var array
     */
    private $_instances    = array();

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
     * Self singleton instance
     *
     * @var Performance_Main_Provider
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
     * Construct method which initialize instance and prepare autoloder.
     *
     * @return void
     */
    public function __construct(Performance_Main_config $config = null) {
        $services = array();
        if ($config !== null) {
            $this->set($config);
            $providerConfig       = $config->hasOwnProperty('provider') ? $config->get('provider') : array();
            $this->_useAutoloader = isset($providerConfig['useAutoloader']) ? $providerConfig['useAutoloader'] : $this->_useAutoloader;
            $services             = isset($providerConfig['initServices']) ? $providerConfig['initServices'] : array();
            $this->_serviceMap    = isset($providerConfig['serviceMap']) ? $providerConfig['serviceMap'] : array();
        }

        if ($this->_useAutoloader === true && is_callable('spl_autoload_register')) {
            spl_autoload_register(array($this, '_autoloader'));
        } else {
            $this->_loadClass();
        }

        foreach ($services as $name => $service) {
            $instance = $this->get($service);
            $this->set($instance, $name);
        }
    }

    /**
     * Return singleton instance.
     *
     * @return Performance_Main_Provider
     */
    public static function getInstance(Performance_Main_config $config = null) {
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
     * @return Performance_Main_Provider
     *
     * @throws Performance_Main_Exception Throws when instance is not object
     */
    public function set($instance, $name=null) {
        if (is_object($instance) === false) {
            throw new Performance_Main_Exception('First parameter must be instance.');
        }

        if ($name === null || is_string($name) === false) {
            $name = get_class($instance);
        }

        $this->reset($name);
        $this->reset(get_class($instance));

        $this->_instances[] = array(
            'name' => $name,
            'classname' => get_class($instance),
            'instance' => $instance
        );

        return $this;
    }

    /**
     * Unsets instance from stack for singleton usage.
     *
     * @param string $name Name of class
     *
     * @return Performance_Main_Provider
     */
    public function reset($name=null) {
        if ($name === null) {
            $this->_instances = array();
        } else {
            foreach ($this->_instances as $key =>$instance) {
                if ($instance['name'] === $name) {
                    unset($this->_instances[$key]);
                    return $this;
                }
            }

            foreach ($this->_instances as $key =>$instance) {
                if ($instance['classname'] === $name) {
                    unset($this->_instances[$key]);
                    return $this;
                }
            }
        }

        return $this;
    }

    /**
     * Destruct function destroys all instances.
     *
     * @return void
     */
    public function __destruct() {
        $list = $this->getListInstances();

        foreach ($list as $instance) {
            $this->reset($instance['name']);
        }
    }

    /**
     * Gets instance from stack. It find instance by name and then by classname.
     * If instance is not in stack then it create instance with dependencies and save it to stack (lazy load principle).
     *
     * @param string $name Name of class
     *
     * @return ${name} Instance of $name
     */
    public function get($name) {
        $instance = $this->_getInstance($name);

        self::$_preventCycleDependencies = array();

        return $instance;
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
            $singleton = $this->get($name);
            $this->reset($name);
            $prototype = $this->get($name);
            $this->set($singleton, $name);
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
     * @return Performance_Main_Provider
     */
    public function loadEnum($name) {
        $this->_loadClass($name);

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
        foreach ($this->_instances as $instance) {
            if ($name === $instance['name']) {
                return $instance['instance'];
            }
        }

        foreach ($this->_instances as $instance) {
            if ($name === $instance['classname']) {
                return $instance['instance'];
            }
        }

        $instance = $this->_createInstance($name);
        $this->set($instance);

        return $instance;
    }

    /**
     * It creates instance with dependencies.
     *
     * @param string $name Name of class
     *
     * @return object {$name}
     *
     * @throws Performance_Main_Exception Throws when dependecies are cycling or instance was not created.
     */
    private function _createInstance($name) {
        if (isset($this->_serviceMap[$name])) {
            $name = $this->_serviceMap[$name];
        }

        $this->_loadClass($name);
        if (in_array($name, self::$_preventCycleDependencies)) {
            throw new Performance_Main_Exception('Object has cycling dependencies!');
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

        if (!isset($dependencies['dependencies'])) {
            $instance = new $name();
        } else {
            if ($dependencies['method'] === 'getInstance') {
                $instance = forward_static_call_array(array($name, 'getInstance'), $arguments);
            } else {
                $class    = new ReflectionClass($name);
                $instance = $class->newInstanceArgs($arguments);
            }
        }

        if (is_object($instance) === false) {
            throw new Performance_Main_Exception('Instance '.$name.' was not created!');
        }

        return $instance;
    }

    /**
     * Gets dependencies for create new class. It finds dependecies for standard constructor or singleton method
     * with name getInstance.
     *
     * @param string $name Name of class
     *
     * @return array Array with all dependencies.
     *
     * @throws Performance_Main_Exception Throws when class doesn't exists.
     */
    private function _getDependencies($name) {
        if (!isset($this->_dependencies[$name])) {
            if (class_exists($name)) {
                $reflClass = new ReflectionClass($name);
            } else {
                throw new Performance_Main_Exception('Object doesn\'t exists: '.$name);
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
                $this->_dependencies[$name] = array();
                return $this->_dependencies[$name];
            }

            $params = $method->getParameters();
            foreach ($params as $param) {
                if ($param->getClass() !== null) {
                    $dependencies[$param->getPosition()] = array(
                        'className' => $param->getClass()->getName(),
                        'position' => $param->getPosition(),
                        'name' => $param->getName()
                    );
                } else {
                    $dependencies[$param->getPosition()] = array(
                        'defaultValue' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                        'position' => $param->getPosition(),
                        'name' => $param->getName()
                    );
                }
            }

            $this->_dependencies[$name] = array(
                'method' => $method->getName(),
                'dependencies' => $dependencies
            );
        }

        return $this->_dependencies[$name];
    }

    /**
     * Autoloader function
     *
     * @param string $class Name of class
     *
     * @return void
     */
    private function _autoloader($class) {
        $root      = dirname(__DIR__);
        $classPath = strstr(str_replace('_', '/', $class), '/');

        $path = $root.$classPath.'.php';

        if (file_exists($path)) {
            include $path;

            return true;
        }

        return false;
    }

    /**
     * Loads file for class. It loads whole module.
     *
     * @param string $class Name of class
     *
     * @return Performance_Main_Provider
     *
     * @throws Performance_Main_Exception Throws when you try load class from non Performance pool
     */
    private function _loadClass($class = null) {
        $class = $class === null ? get_class() : $class;
        if (class_exists($class) === false && ($this->_useAutoloader === false || is_callable('spl_autoload_register') === false)) {
            if (preg_match('/^Performance.*/', $class)) {
                $tmp    = substr($class, strpos($class, '_') + 1);
                $module = substr($tmp, 0, strpos($tmp, '_'));
                $path   = dirname(__DIR__).'/'.$module.'/';
                $files  = $this->_getFiles($path);
                foreach ($files as $file) {
                    include_once $file;
                }
            } else {
                throw new Performance_Main_Exception('Provider loading only classes from Performance.');
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

        foreach (glob($path.'/*') as $item) {
            if (is_dir($item)) {
                $result += $this->_getFiles($item);
            } elseif(preg_match('/\.php$/', $item)) {
                $result[] = $item;
            }
        }

        return $result;
    }
}
