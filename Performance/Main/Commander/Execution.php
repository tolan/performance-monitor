<?php

namespace PF\Main\Commander;

use PF\Main\Provider;

/**
 * This script defines class for execution of executor (set of commands).
 * This class provide trigger input function with saving data to Result entity and sharing this entity between executions.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Execution implements Interfaces\Execution {

    /**
     * Command for execution.
     *
     * @var mixed
     */
    private $_command;

    /**
     * Scope of command.
     *
     * @var mixed
     */
    private $_scope = null;

    /**
     * Construct method.
     *
     * @param string|\Closure $command Name of function in scope or Closure function
     * @param Object          $scope   If command is only name of function then must be defined scope (class) where is command triggered
     *
     *  @return void
     */
    public function __construct($command, $scope = null) {
        $this->_command = $command;
        $this->_scope   = $scope;
    }

    /**
     * This method triggers the input command (in scope).
     *
     * @param \PF\Main\Commander\Result $result   Entity for saving data from command
     * @param \PF\Main\Provider         $provider Provider instance
     *
     * @return \PF\Main\Commander\Execution
     */
    public function execute(Result $result, Provider $provider) {
        $command = $this->_command;
        $scope   = $this->_scope;

        $attributes = $this->_getCommandAttributes($command, $scope);
        $arguments  = $this->_getArguments($provider, $result, $attributes);

        $answer = $this->_execute($command, $this->_scope, $arguments);

        $this->_saveAnswer($answer, $result);

        return $this;
    }

    /**
     * It extracts required attributes for command.
     *
     * @param string|\Closure $command Name of function in scope or Closure function
     * @param Object          $scope   If command is only name of function then must be defined scope (class) where is command triggered
     *
     * @return array
     *
     * @throws \PF\Main\Commander\Exception Throws when combination of command and scope is not supported.
     */
    private function _getCommandAttributes($command, $scope = null) {
        $attributes = array();
        if ($command instanceof \Closure) {
            $attributes = $this->_getCommandAttributesClosure($command);
        } elseif ($scope !== null) {
            $attributes = $this->_getCommandAttributesClass($command, $scope);
        } else {
            throw new Exception('Unsupported combination of scope and command.');
        }

        return $attributes;
    }

    /**
     * It extracts required arguments for command. Create requied instances or take attributes from actual result instance or leave as default.
     *
     * @param string|\Closure $command Name of function in scope or Closure function
     * @param Object          $scope   If command is only name of function then must be defined scope (class) where is command triggered
     *
     * @return array
     */
    private function _getArguments(Provider $provider, Result $entityResult, $attributes = array()) {
        $result = array();

        foreach ($attributes as $name => $attribut) {
            if (is_string($attribut) && is_a($entityResult, $attribut)) {
                $result[$name] = $entityResult;
            } elseif (is_string($attribut) && class_exists($attribut)) {
                $result[$name] = $provider->get($attribut);
            } elseif ($entityResult->has($name)) {
                $result[$name] = $entityResult->get($name);
            } else {
                $result[$name] = $attribut;
            }
        }

        return $result;
    }

    /**
     * It triggers input command with scope.
     *
     * @param string|\Closure $command   Name of function in scope or Closure function
     * @param Object          $scope     If command is only name of function then must be defined scope (class) where is command triggered
     * @param array           $arguments Array with required arguments
     *
     * @return mixed
     *
     * @throws \PF\Main\Commander\Exception Throws when combination of command and scope is not supported.
     */
    private function _execute($command, $scope = null, $arguments = array()) {
        $result = array();
        if ($command instanceof \Closure) {
            $result = $this->_executeClosure($command, $arguments);
        } elseif ($scope !== null) {
            $result = $this->_executeClass($command, $scope, $arguments);
        } else {
            throw new Exception('Unsupported combination of scope and command.');
        }

        return $result;
    }

    /**
     * It saves answer of execution into result instance. If answer is not array then it save answer to data property.
     *
     * @param mixed                     $answer Answer from execute of command
     * @param \PF\Main\Commander\Result $result Result instance
     *
     * @return \PF\Main\Commander\Execution
     */
    private function _saveAnswer($answer, Result $result) {
        // code: array_keys... means checking for non-associative array
        if (!empty($answer) && (is_array($answer) || $answer instanceof \ArrayIterator) && (array_keys($answer) !== range(0, count($answer) - 1))) {
            foreach ($answer as $name => $value) {
                $result->set($name, $value);
            }
        } elseif($answer !== null) {
            $result->setData($answer);
        }

        return $this;
    }

    /**
     * It extracts required attributes for Closure.
     *
     * @param string|\Closure $command Closure function
     *
     * @return array
     */
    private function _getCommandAttributesClosure(\Closure $command) {
        $reflection = new \ReflectionFunction($command);

        return $this->_getAttributesReflectionFunction($reflection);
    }

    /**
     * It extracts required attributes for command.
     *
     * @param string|\Closure $command Name of function in scope
     * @param Object          $scope   Scope (class) where is command triggered
     *
     * @return array
     */
    private function _getCommandAttributesClass($command, $scope) {
        $reflection = new \ReflectionClass($scope);
        $reflMethod = $reflection->getMethod($command);

        return $this->_getAttributesReflectionFunction($reflMethod);
    }

    /**
     * It extracts required attributes from method reflection.
     *
     * @param ReflectionFunction|ReflectionMethod $reflection Reflection of method|function
     *
     * @return array
     */
    private function _getAttributesReflectionFunction($reflection) {
        $result = array();
        $params = $reflection->getParameters();

        foreach ($params as $param) {
            /* @var $param \ReflectionParameter */
            $class = $param->getClass();
            if ($class) {
                $result[$param->getName()] = $class->getName();
            } else {
                $hasDefault = $param->isDefaultValueAvailable();
                if ($hasDefault === true) {
                    $result[$param->getName()] = $param->getDefaultValue();
                } else {
                    $result[$param->getName()] = $class;
                }
            }
        }

        return $result;
    }

    /**
     * It triggers input Closure with attributes.
     *
     * @param \Closure $command   Closure function
     * @param array    $arguments Array with required arguments
     *
     * @return mixed
     */
    private function _executeClosure($command, $arguments = array()) {
        return forward_static_call_array($command, $arguments);
    }

    /**
     * It triggers input command with scope and arguments.
     *
     * @param string $command   Name of function in scope
     * @param Object $scope     Scope (class) where is command triggered
     * @param array  $arguments Array with required arguments
     *
     * @return mixed
     */
    private function _executeClass($command, $scope, $arguments = array()) {
        return forward_static_call_array(array($scope, $command), $arguments);
    }
}
